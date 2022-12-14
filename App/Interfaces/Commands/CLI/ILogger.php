<?php

namespace App\Interfaces\Commands\CLI;


interface ILogger {
    /**
     * Log a simple <code>$message</code>, without decorations. Note that <br> tags are removed.
     * @param mixed $message
     * @param mixed $clear Set to true to clear the console, defaults to false.
     * @return bool
     */
    function simpleLog($message, bool $clear = false): bool;
    /**
     * Logs <code>$message</code> to output. TODO: standardize output, so it can be pointed elsewhere.
     * @param mixed $message
     * @return bool
     */
    function log($message): bool;
    /**
     * Export <code>$message</code> via <code>var_export($message, true)</code> and logs it to the console
     * @param mixed $message
     * @return bool
     */
    function varLog($message): bool;
    /**
     * Grabs user input, while displaying an <code>$info</code> about what is expected from the user.
     * @param mixed $arg
     * @return callable|string
     */
    function input($arg): callable|string;
    /**
     * Reset static property <code>self::$message</code>, new calls to <code>Logger::log</code> will no longer add former data to the output.
     * @return bool
     */
    function reset(): bool;
    
    /**
     * Logs a single line output to terminal, which can be updated.
     * <b>Note:</b> <code>$callable</code> should always return a value that is !== <code>false</code> until it wishes to stop updating the line.
     * To stop updating the line and close the output, <code>$callable</code> should return false.
     * @param int $interval
     * @param callable $callable
     * @return string|false
     * 
     */
    function updatable(int $interval, callable $callable): string|false;
}