<?php

namespace App\Interfaces\Commands\CLI;

interface IScheduler {

    function __construct(string $name, array $config, string $action);

    function start(array $configOverride=[]): bool;
    function stop(): bool;

}