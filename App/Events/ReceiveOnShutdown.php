<?php
namespace App\Events;

use App\Commands\CLI\Logger;
use App\Interfaces\Events\IReceiveOnShutdown;

class ReceiveOnShutdown implements IReceiveOnShutdown
{
    public function __construct()
    {
        self::onShutdown();
    }
    public static function onShutdown(): bool
    {
        global $argv;
        $_ = $_SERVER['_'];
        return pcntl_exec($_, $argv);
    }

    public function onShouldDie(Logger $logger):bool|null {
        $logger->log("Shutting down...");
        sleep(50000);
        return die;
    } 

    public function exit() {
        $GLOBALS['stop'] = true;
    }
}