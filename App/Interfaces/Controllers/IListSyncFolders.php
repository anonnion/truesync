<?php

namespace App\Interfaces\Controllers;

interface IListSyncFolders {
    
    /* Arguments accepted by $sortFunc */
    const SORT_FUNC_ARGS = [
        /* Name of file or folder */
        "name",
        /* Check if it is a file or folder */
        "type",
        /* Filesize, zero for folders */
        "fileSize",
        /* Creation date */
        "created_at",
        /* Last modification date */
        "modified_at",
    ];
    function listAll(): array;
    function listSome(string $condition): array|false;
    function sortAndListAll(callable $sortFunc): array|false;
    function sortAndListSome(
        callable $sortFunc, 
        string $condition): array|false; 
}