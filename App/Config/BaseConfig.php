<?php

namespace App\Config;
use App\Interfaces\Config\IBaseConfig;

#[BaseConfig]
class BaseConfig implements IBaseConfig {
    protected const serverName = "";
    protected string $serverHash;
    protected array $_config = [];
    protected const VERSION = "0.1";

    public function __construct() 
    {
        $_conf = file('.env');
        foreach($_conf as $c){
            $obj = explode('=', $c);
            $this->_config[trim($obj[0])] = trim($obj[1]);
        }
    }
    public function getConfig($name):string|array|false {
        return $this->_config[$name] ?? false;
    }

    public function getAll():array {
        return $this->_config;
    }

    public function setConfig($name, $vale):bool {
        return false;
    }
}