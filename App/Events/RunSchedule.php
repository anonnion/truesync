<?php

namespace App\Events;

use App\Interfaces\Events\IRunSchedule;
use App\Models\ConsoleModel;

class RunSchedule implements IRunSchedule
{
    public int $interval;
    private array $stack = [];
    public function __construct($interval)
    {
        $this->interval = $interval;
    }

    public function addToStack(string $name, int $interval, string $callable): bool
    {
        $this->stack[$name] = [
            "interval" => $interval,
            "callable" => $callable,
            "lastRunTimestamp" => time(),
        ];
        return true;
    }
    public function removeFromStack(string $name): bool
    {
        unset($this->stack[$name]);
        return true;
    }

    public function loadStackFromFile(): IRunSchedule
    {
        $files = scandir("App/Data/Scheduler");
        array_shift($files);
        array_shift($files);
        foreach ($files as $file) {
            $data = json_decode(file_get_contents("App/Data/Scheduler/" . $file));
            $name = strrev(explode('-', strrev($file))[1]);
            $this->addToStack($data->name, $data->interval, $data->callable);
        }
        return $this;
    }

    public function start(bool $waitForInput = true)
    {
        $logger = new ConsoleModel();
        $logger->clear();
        if (isset($GLOBALS['stop']) && $GLOBALS['stop'] === true)
            return;
        else {
            foreach ((array)$this->stack as $name => $job) {
                if (intval($job["lastRunTimestamp"]) + intval($job["interval"]) >= time()) {
                    $time = time();
                    $logger->raw("Running Job: ${name}...", true);
                    eval("global".$job["callable"]."; ".$job["callable"]."();");
                    $logger->raw("Job $name completed in " . (time() - $time) . " ms", true);
                    $this->stack[$name]["lastRunTimestamp"] = time();
                }
            }
            if($waitForInput === true) {
                $command = $logger->input(": ");
                $logger->raw("Running ".$command, true);
                usleep(1000000);
                $this->start(true);
            }
            else {
                $num = $this->interval;
                $logger->updatable(interval: 1, callable: function () use ($logger, $num) {
                    static $n;
                    $n++;
                    $num-=$n;
                    if ($num < 0) {
                        $logger->clear();
                        return false;
                    }
                    return str_pad("Checking again in ".$num." seconds", 100);
                });
                $logger->clear();
                $this->start(false);
            }
        }
    }
}