<?php
namespace App\Interfaces\Events;

interface IRunSchedule {
    function __construct($interval);
    function addToStack(string $name, int $interval, string $callable): bool;
    function removeFromStack(string $name): bool;
    function loadStackFromFile(): IRunSchedule;
    function start(bool $waitForInput = true);
}