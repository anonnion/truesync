<?php
namespace App\Models;

use App\Models\FTPModel;
use App\Interfaces\Config\IBaseConfig;
use FFI\Exception;
use stdClass;
class TransferData {
    private IBaseConfig $config;
    private FTPModel $ftp;

    public function __construct(IBaseConfig $config) {
        $this->config = $config;
        $this->ftp = new FTPModel($this->config);
        $this->ftp->connect();
    }
    public function up(string $source):bool {
        $options = new stdClass;
        $options->localFile = $this->config->getConfig("LOCAL_BASEDIR") . $source;
        $options->remoteFile = $this->config->getConfig("REMOTE_BASEDIR") .$source;
        return $this->ftp->upload($options);
    }

    public function down(string $source):bool {
        $options = new stdClass;
        $options->localFile = $this->config->getConfig("LOCAL_BASEDIR") . $source;
        $options->remoteFile = $this->config->getConfig("REMOTE_BASEDIR") .$source;
        return $this->ftp->download($options);
    }
    public function delFile(string $source): bool {
        $options = new stdClass;
        $options->remoteFile = $this->config->getConfig("REMOTE_BASEDIR") .$source;
        return $this->ftp->removeFile($options->remoteFile);
    }
    public function delDir(string $source): bool {
        $options = new stdClass;
        $options->remoteDir = $this->config->getConfig("REMOTE_BASEDIR") .$source;
        return $this->ftp->removeDir($options->remoteDir);
    }
}