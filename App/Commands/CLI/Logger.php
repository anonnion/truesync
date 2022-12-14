<?php

namespace App\Commands\CLI;

use DOMDocument;
use Termwind\Terminal;

use function Termwind\{render};
use function Termwind\{terminal};
use function Termwind\{ask};
use App\Interfaces\Commands\CLI\ILogger;

class Logger implements ILogger
{
    public static string $message = "";
    public static $count = 0;
    public function __construct()
    {
        $this->clear();
    }
    /**
     * Log a simple <code>$message</code>, without decorations. Note that <br> tags are removed.
     * @param mixed $message
     * @param mixed $clear
     * @return bool
     */
    function simpleLog($message, $clear = false):bool {
        if($clear === true) print("\r");
        print(str_replace('<br>',"",$message));
        return true;
    }
    /**
     * Logs <code>$message</code> to output. TODO: standardize output, so it can be pointed elsewhere.
     * @param mixed $message
     * @return bool
     */
    function log($message):bool {
        self::$count++;
        // if(self::$count < 2)terminal()->clear();
        self::$message = self::$message . PHP_EOL . trim($message);
        $result = self::$message.PHP_EOL;
        render(<<<HTML
    <div>
        <div class="px-1 bg-green-600 p-30">TrueSync®️</div><br>
        $result
    </div>
HTML);
        return true;
    }
    /**
     * Export <code>$message</code> via <code>var_export($message, true)</code> and logs it to the console
     * @param mixed $message
     * @return bool
     */
    function varLog($message): bool
    {
        // terminal()->clear();
        $e = str_split(trim(str_replace("\'",'',var_export($message, true))));
        if($e[0] == PHP_EOL)array_shift($e);
        if($e[0] == " ")array_shift($e);
        if($e[0] == "'")array_shift($e);
        if($e[count($e)-1] == PHP_EOL)array_pop($e);
        if($e[count($e)-1] == " ")array_pop($e);
        if($e[count($e)-1] == "'")array_pop($e);
        $e = implode("", $e);
        self::$message = self::$message . PHP_EOL . trim($e);
        $result = self::$message;
        render(<<<HTML
    <div>
        <div class="px-1 bg-green-600">TrueSync®️</div><br>
        <pre>$result</pre>
    </div>
HTML);
        return true;
    }
    /**
     * Grabs user input, while displaying an <code>$info</code> about what is expected from the user.
     * @param mixed $arg
     * @return callable|string
     */
    public function input($info): callable|string {
        return ask(<<<HTML
        <span class="mt-1 ml-2 mr-1 bg-green px-1 text-black">
            $info
        </span>
    HTML);
    }
    /**
     * Reset static property <code>self::$message</code>, new calls to <code>Logger::log</code> will no longer add former data to the output.
     * @return bool
     */
    public function reset():bool {
        self::$message = "";
        return true;
    }
    /**
     * Logs a single line output to terminal, which can be updated.
     * <b>Note:</b> <code>$callable</code> should always return a value that is !== <code>false</code> until it wishes to stop updating the line.
     * To stop updating the line and close the output, <code>$callable</code> should return false.
     * @param int $interval amount of seconds to sleep before waking and running <code>$callable</code> again.
     * @param callable $callable
     * @return string|false
     * 
     */
    public function updatable(int $interval, callable $callable): string|false
    {
        $return = "";
        print("\n\r");
        $res = $callable();
        while($res !== false){
            print($res . "\r");
            sleep($interval);
            $return = $res;
            $res = $callable();
        }
        
        return $return ?? $res;
    }
    /**
     * Clears the terminal content.
     * @return void
     */
    public function clear()
    {
        self::$count = 0;
        // terminal()->clear();
    }
}