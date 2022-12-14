<?php
namespace App\Interfaces\Events;

use App\Commands\CLI\Logger;

interface IReceiveOnShutdown {
    static function onShutdown(): bool;
    function onShouldDie(Logger $logger):bool|null;
}