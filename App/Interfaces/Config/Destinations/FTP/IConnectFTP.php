<?php

namespace App\Interfaces\Config\Destinations\FTP;

interface IConnectFTP {
    function copyFromLocal(string $source, string $destinationFolder): bool;
    function copyToLocal(string $source, string $destinationFolder): bool;
    function find(string $pattern, $path, $recursive): array;
    function fileSize(string $remoteFile): int;
    function dirSize(string $directory): int;
    function createFile(string $fileName, string | null $content = null):bool;
    function appendFile(string $remoteFIle, mixed $content): bool;
    function rename(string $remoteFile, string $newName): bool;
    function move(string $source, string $destinationFolder): bool;
    function removeFile(string $remoteFile): bool;
    function removeDir(string $directory): bool;
    function isFile(string $remoteFile): bool;
    function isDir(string $remoteFile): bool;
    function isEmpty(string $remoteFile): bool;
    function exists(string $remoteFile): bool;
    function setPermissions(string $fileName, array | int | string $mode): bool;
    function lastMTime(string $remoteFile): string|int;
    function getFileContent(string $remoteFile, string $mode): string;
    function getFeatures(): array| bool;
}