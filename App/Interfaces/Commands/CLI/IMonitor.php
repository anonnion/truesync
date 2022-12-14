<?php

namespace App\Commands\CLI;
use App\Interfaces\Config\IBaseConfig;
use Attribute;
use ReturnTypeWillChange;

interface IMonitor {
    #[Attribute('$name')]
    #[IBaseConfig, Attribute('$baseConfig')]
    function __construct(string $name, IBaseConfig $baseConfig);
    function monitor(): bool;
}