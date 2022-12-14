<?php

namespace App\Interfaces\Config\Destinations;

use App\Interfaces\Config\IBaseConfig;
use stdClass;

interface IBaseConnect {
    function __construct(IBaseConfig $options);
    function connect():bool;
    function fetch(string $dir): array;
    function upload(stdClass $options): bool;
    function download(stdClass $options): bool;
}