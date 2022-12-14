<?php

namespace App\Commands\CLI;

use App\Config\BaseConfig;
use App\Models\ConsoleModel;
use App\Models\TransferData;

class Monitor
{
    public BaseConfig $config;
    public string $name;
    public array|object $conf;
    public array $content = [];
    public static array $formerData = [];
    public array $data;
    public array $needSyncing;
    public string $message;
    public ConsoleModel $console;
    public function __construct(string $name, BaseConfig $baseConfig)
    {
        $cp = ((array) json_decode(file_get_contents($baseConfig->getConfig('WATCHER_CONFIG'))));
        foreach ($cp["items"] as $v) {
            if ($v->name == $name)
                $this->conf = $v;
        }
        $this->config = $baseConfig;
        $this->console = new ConsoleModel();
        $this->name = $name;
    }
    public function monitor(): bool
    {
        $folderPath = $this->conf->absolutePath;
        try {
            $dirIterator = new \RecursiveDirectoryIterator($folderPath);
            /** @var \RecursiveDirectoryIterator | \RecursiveIteratorIterator $it */
            $it = new \RecursiveIteratorIterator($dirIterator);

            // the valid() method checks if current position is valid eg there is a valid file or directory at the current position
            while ($it->valid()) {
                // isDot to make sure it is not current or parent directory
                if (!$it->isDot() && $it->isFile() && $it->isReadable()) {
                    $this->content[$it->key()] = [
                        "name" => $it->getBasename(),
                        "path" => $it->key(),
                        "fileSize" => $it->getSize(),
                        "lastModified" => $it->getMTime() ?? 1,
                        "lastAccessTime" => $it->getATime() ?? 1,
                        "fileType" => $it->getExtension()
                    ];

                    // $file is a SplFileInfo instance
                    // $file = $it->current();
                    // $filePath = $it->key();
                    // do something about the file
                    // ...
                    // require $filePath;
                }

                $it->next();
            }
        } catch (\Exception $e) {
            throw $e;
        }
        $this->data = [
            "name" => $this->name,
            "contents" => $this->content,
        ];
        $buffer = $this->console->createBuffer();
        $toUpload = $toDelete = [];
        if(count($this->data["contents"]) >= 1){
            $this->console->paragraph("New/modified files", $buffer)->table(
                [
                    "S/N" => null,
                    "Type" => null,
                    "Name" => null,
                    "Path" => null,
                    "Size" => null,
                    "Last Modified" => null,
                    "Last Accessed" => null,
                    "File Type" => null,
                ], $buffer
            );
            $c = 0;
            foreach ($this->data["contents"] as $key => $data) {
                $c++;
                if(!isset(self::$formerData[$key]) || $data != @self::$formerData[$key]) {
                    $toUpload[$key] = $data;
                    $state = isset(self::$formerData[$key]) ? "modified" : "new";
                    $class = isset(self::$formerData[$key]) ? "bg-yellow-400" : "bg-green-500";
                    $temp = $data;
                    $this->console->tr(
                        [
                            $c => null,
                            $state => "bg-slate-800 ".$class,
                            $temp['name'] => null,
                            $temp['path'] => null,
                            $temp['fileSize'] => null,
                            $temp['lastModified'] => null,
                            $temp['lastAccessTime'] => null,
                            $temp['fileType'] => null,
                        ],
                        $buffer
                    );
                    self::$formerData[$key] = $data;
                }
                else {
                    $temp = $data;
                    $this->console->tr(
                        [
                            $c => null,
                            "not modified" => "bg-slate-800  bg-gray-500",
                            $temp['name'] => null,
                            $temp['path'] => null,
                            $temp['fileSize'] => null,
                            $temp['lastModified'] => null,
                            $temp['lastAccessTime'] => null,
                            $temp['fileType'] => null,
                        ],
                        $buffer
                    );
                }
            }
            $this->console->endTable($buffer);            
        } else {
            $this->console->raw("Nothing has changed<br>", true);
        }
        if(count(self::$formerData) >= 1){
            $this->console->paragraph("Deleted files", $buffer, "bg-purple-400 text-fuchsia-400")->table(
                [
                    "S/N" => null,
                    "Type" => null,
                    "Name" => null,
                    "Path" => null,
                    "Size" => null,
                    "Last Modified" => null,
                    "Last Accessed" => null,
                    "File Type" => null,
                ], $buffer
            );
            $c = 0;
            foreach (self::$formerData as $key => $data) {
                if(!isset($this->data["contents"][$key])) {
                    $toDelete[$key] = $data;
                    $this->console->tr(
                        [
                            $c => null,
                            "deleted" => "bg-slate-800 bg-pink-600",
                            $temp['name'] => null,
                            $temp['path'] => null,
                            $temp['fileSize'] => null,
                            $temp['lastModified'] => null,
                            $temp['lastAccessTime'] => null,
                            $temp['fileType'] => null,
                        ],
                        $buffer
                    );
                    unset(self::$formerData[$key]);
                }
                else {
                }
            }
            $this->console->endTable($buffer);
        } else {
            $this->console->raw("Nothing has been deleted<br>", true);
        }
        foreach ($toUpload as $key => $value) {
            $status = false;
            $transfer = new TransferData($this->config);
            $this->console->raw("Uploading $key...");
            $status = $transfer->up($key);
            $this->console->success("$key upload success");
        }
        foreach ($toDelete as $key => $value) {
            $status = false;
            $transfer = new TransferData($this->config);
            $this->console->raw("Deleting $key...");
            $status = $transfer->delFile($key);
            $this->console->success("$key delete success");
        }
        return true;
    }
    public function render() {
        $this->console->flushBuffer();
    }
}