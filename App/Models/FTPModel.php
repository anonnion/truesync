<?php

namespace App\Models;

use App\Interfaces\Config\IBaseConfig;
use App\Interfaces\Config\Destinations\IBaseConnect;
use App\Interfaces\Config\Destinations\FTP\IConnectFTP;
use FFI\Exception;
use stdClass;
use App\Config\Destinations\FTP\FTPConfig as FTPConf;

/**
 * Includes for use with Lazzard FtpClient
 */
use Lazzard\FtpClient\Connection\FtpSSLConnection;
use Lazzard\FtpClient\Config\FtpConfig;
use Lazzard\FtpClient\FtpClient;
 /**
  * Creates connection to the FTP server, and handle requests.
  */

class FTPModel implements IBaseConnect, IConnectFTP {

    private FTPConf $conf;
    private FtpSSLConnection $connection;
    private FtpConfig $config;
    private FtpClient $client;

    public function __construct(IBaseConfig $config) {
        $this->conf = new FTPConf(
            true, 
            $config->getConfig("FTP_HOST"),
            $config->getConfig("FTP_USERNAME"),
            $config->getConfig("FTP_PASSWORD"),
        );
    }
    

    /**
     * IBaseConfig implementation
     */
    /**
     * Opens a new FTP connection, set passive status, then create a new FTP client
     * @param stdClass $options => $options->
     * @throws \RuntimeException  
     * @return bool Returns bool
     */
    function connect():bool {
        try {
            if (!extension_loaded('ftp')) {
                throw new \RuntimeException("FTP extension not loaded.");
            }
        
            $this->connection = new FtpSSLConnection($this->conf->host, $this->conf->username, $this->conf->password);
            $this->connection->open();
        
            $this->config = new FtpConfig($this->connection);
            $this->config->setPassive($this->conf->passive);
        
            $this->client = new FtpClient($this->connection);
        
        } catch (Exception $ex) {
            print_r($ex->getMessage());
        }
        return true;
    }
    /**
     * Get list of files in a directory
     * @param string $dir
     * @return array A list of file names as an array
     */
    function fetch(string $dir): array {
        return $this->client->listDir($dir);
    }
    function upload(stdClass $options): bool {
        return $this->client->upload($options->localFile, $options->remoteFile);
    }
    function download(stdClass $options): bool {
        return $this->client->download($options->remoteFile, $options->localFile);
    }
    
    function asyncUpload(stdClass $options): bool {
        return $this->client->asyncUpload($options->localFile, $options->remoteFile, $options->callBack);
    }
    function asyncDownload(stdClass $options): bool {
        return $this->client->asyncDownload($options->remoteFile, $options->localFile, $options->callBack);
    }


    /**
     * IConnectFTP implementations
     */
    function copyFromLocal(string $source, string $destinationFolder): bool {
        return $this->client->copyFromLocal($source, $destinationFolder);
    }
    function copyToLocal(string $source, string $destinationFolder): bool {
        return $this->client->copyFromLocal($source, $destinationFolder);
    }
    function find(string $pattern, $path, $recursive = false): array {
        return $this->client->find($pattern, $path, $recursive);
    }
    function fileSize(string $remoteFile): int {
        return $this->client->fileSize($remoteFile);
    }
    function dirSize(string $directory): int {
        return $this->client->dirSize($directory);
    }
    function createFile(string $fileName, string | null $content = null):bool {
        return $this->client->createFile($fileName, $content);
    }
    function appendFile(string $remoteFile, mixed $content): bool {
        return $this->client->appendFile($remoteFile, $content);
    }
    function rename(string $remoteFile, string $newName): bool {
        return $this->client->rename($remoteFile, $newName);
    }
    function move(string $source, string $destinationFolder): bool {
        return $this->client->move($source, $destinationFolder);
    }
    function removeFile(string $remoteFile): bool {
        return $this->client->removeFile($remoteFile);
    }
    function removeDir(string $directory): bool {
        return $this->client->removeDir($directory);
    }
    function isFile(string $remoteFile): bool {
        return $this->client->isFile($remoteFile);
    }
    function isDir(string $remoteFile): bool {
        return $this->client->isDir($remoteFile);
    }
    function isEmpty(string $remoteFile): bool {
        return $this->client->isEmpty($remoteFile);
    }
    function exists(string $remoteFile): bool {
        return $this->client->isExists($remoteFile);
    }
    function setPermissions(string $fileName, array | int | string $mode): bool {
        return $this->client->setPermissions($fileName, $mode);
    }
    function lastMTime(string $remoteFile): string|int {
        return $this->client->lastMTime($remoteFile);
    }
    function getFileContent(string $remoteFile, string $mode): string {
        return $this->client->getFileContent($remoteFile, $mode);
    }
    function getFeatures(): array | bool {
        return $this->client->getFeatures();
    }
}