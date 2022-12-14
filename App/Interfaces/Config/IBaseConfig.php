<?php
namespace App\Interfaces\Config;

interface IBaseConfig {
    const SERVER_NAME = "";
    function getConfig($name):string|array|false;
    function setConfig($name, $vale):bool;
}   