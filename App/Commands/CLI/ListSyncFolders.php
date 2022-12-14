<?php

namespace App\Commands\CLI;

use App\Config\BaseConfig;
use App\Interfaces\Controllers\IListSyncFolders;

class ListSyncFolders implements IListSyncFolders {

    private BaseConfig $_config;
    private string $_watcherConfigFile;

    #[BaseConfig]
    public function __construct(BaseConfig $config)
    {
        $this->_watcherConfigFile = $config->getConfig("WATCHER_CONFIG");
    }
    /**
     * Loads watcher json file into App
     * @return array|object
     */
    private function loadSyncFolders():array|object {
        $list = (array)json_decode(file_get_contents($this->_watcherConfigFile));
        return $list;
    }

    public function listAll(): array
    {
        return $this->loadSyncFolders()->items ?? [];
    }

    public function listSome(string $condition):array|false {
        
        return false;
    }

    public function sortAndListAll(callable $sortFunc): array|false
    {
        return false;
    }

    public function sortAndListSome(callable $sortFunc, string $condition): array|false
    {
        return false;
    }
}