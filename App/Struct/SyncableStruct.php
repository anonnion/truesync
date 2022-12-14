<?php

namespace App\Struct;

use SplFileInfo;

abstract class SyncableStruct {
    public string $name;
    public string $serverId;
    public string $path;
    public SplFileInfo $fileInfo; 

    public function transfer() {
        return true;
    }
}