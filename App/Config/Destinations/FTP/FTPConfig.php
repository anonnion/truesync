<?php

namespace App\Config\Destinations\FTP;

readonly final class FTPConfig {
    public bool $passive;
    public string $host;
    public string $username;
    public string $password;


    /**
     * @param bool $passive 
     * @param string $host 
     * @param string $username 
     * @param string $password 
     */
    public function __construct(bool $passive, string $host, string $username, string $password) {
    	$this->passive = $passive;
    	$this->host = $host;
    	$this->username = $username;
    	$this->password = $password;
    }
}