<?php

namespace App\Commands\CLI;

use App\Interfaces\Commands\CLI\IScheduler;

class Scheduler implements IScheduler {

    public static $interval = 100;
    public object $record;

    public function __construct(
        public string $name, 
        public array $config,
        string $callable)
    {
        // Check if scheduler file and folder exist, else create them.
        $callable = str_replace(PHP_EOL, "", $callable);
        if (!is_dir("App/Data/Scheduler"))
            mkdir(directory : "App/Data/Scheduler", recursive : true);
        if (!file_exists("App/Data/Scheduler/${name}-schedule.json"))
            file_put_contents("App/Data/Scheduler/${name}-schedule.json", "{
    \"name\" : \"${name}\",
    \"interval\" : \"$config[interval]\",
    \"callable\": \"${callable}\"
}"          );
        $this->record = json_decode(file_get_contents("App/Data/Scheduler/${name}-schedule.json"));
    }

    public function start(array $configOverride = []): bool
    {

        return false;
    }

    public function stop(): bool
    {
        return false;
    }
}
