<?php


namespace App\Models;
use App\Interfaces\Commands\CLI\ILogger;
use App\Commands\CLI\Logger;
use stdClass;

class ConsoleModel {
    
    public ILogger $logger;
    public string $message = "";
    public int $bufferId;
    public array $buffers;
    public const HEADING_1 = 1;
    public const HEADING_2 = 2;
    public const HEADING_3 = 3;
    public const HEADING_4 = 4;
    public const HEADING_5 = 5;
    public const HEADING_6 = 6;
    public function __construct() {
        $this->logger = new Logger;
    }
    /**
     * Creates a buffer for the output.
     * @return int
     */
    public function createBuffer(): int {
        $bufferId = random_int(10000, 99999);
        $this->buffers[$bufferId] = "";
        $this->bufferId = $bufferId;
        return $bufferId;
    }

    public function currentBufferId(): int {
        return $this->bufferId;
    }

    /**
     * Creates an heading element and adds it to the buffer with ID <code>$bufferId</code>. If <code>$bufferId</code> is not provided, it automatically creates one.
     * @param int $type of the heading element
     * @param string $content Content of the heading element
     * @param int|null $bufferId generated with createBuffer() method.
     * @param string $customClasses custom classes.
     * @return ConsoleModel Returns ConsoleModel.
     * @example heading(type : self::HEADING_1 - 6, content: "Hello World", bufferId: int|bool) description
     */
    public function heading(int $type = self::HEADING_1, string $content, int | null $bufferId = null, string | null $customClasses = null): ConsoleModel {
        if ($bufferId === null)
            $bufferId = $this->createBuffer();
        $this->message = $this->message . "<h$type class=\" \">$content</h$type>";
        $this->buffers[$bufferId] = $this->message;
        return $this;
    }

    /**
     * Creates a paragraph element and adds it to the buffer with ID <code>$bufferId</code>. If <code>$bufferId</code> is not provided, it automatically creates one.
     * @param int $type of the paragraph element
     * @param string $content Content of the paragraph element
     * @param int|null $bufferId generated with createBuffer() method.
     * @param string $customClasses custom classes.
     * @return ConsoleModel Returns ConsoleModel.
     * @example paragraph(content: "Hello World", bufferId: int|bool) description
     */
    public function paragraph(string $content, int | null $bufferId = null, string | null $customClasses = null): ConsoleModel {
        if ($bufferId === null)
            $bufferId = $this->createBuffer();
        $this->message = $this->message . "<p class=\"".($customClasses ?? "bg-purple-400 text-fuchsia-400 p-8")."\">$content</p>";
        $this->buffers[$bufferId] = $this->message;
        return $this;
    }

    /**
     * Creates a span element and adds it to the buffer with ID <code>$bufferId</code>. If <code>$bufferId</code> is not provided, it automatically creates one.
     * @param int $type of the span element
     * @param string $content Content of the span element
     * @param int|null $bufferId generated with createBuffer() method.
     * @param string $customClasses custom classes.
     * @return ConsoleModel Returns ConsoleModel.
     * @example span(content: "Hello World", bufferId: int|bool) description
     */
    public function span(string $content, int | null $bufferId = null, string | null $customClasses = null): ConsoleModel {
        if ($bufferId === null)
            $bufferId = $this->createBuffer();
        $this->message = $this->message . "<span>$content</span>";
        $this->buffers[$bufferId] = $this->message;
        return $this;
    }

    /**
     * Creates a table element and adds it to the buffer with ID <code>$bufferId</code>. If <code>$bufferId</code> is not provided, it automatically creates one.
     * @param int $type of the table element
     * @param string $content Content of the table element
     * @param int|null $bufferId generated with createBuffer() method.
     * @param string $customClasses custom classes.
     * @return ConsoleModel Returns ConsoleModel.
     * @example table(content: "Hello World", bufferId: int|bool) description
     */
    public function table(array $trows = ["Row content" => "custom classes"], int | null $bufferId = null, string | null $customClasses = null): ConsoleModel {
        if ($bufferId === null)
            $bufferId = $this->createBuffer();
        $this->message .= '<table class="'.($customClasses ?? "bg-slate-100").'">
        <thead>
          <tr>
';
        foreach ($trows as $content => $customClasses) {
            $this->message .= '<th class="'.($customClasses ?? "bg-slate-400 ").'">'.$content.'</th>';
        }
        $this->message .=  '          </tr>
        </thead>
        <tbody>';
        $this->buffers[$bufferId] = $this->message;
        return $this;
    }

    /**
     * adds </table> closing tag to the table element already added using table() method.
     * @param int $bufferId
     * @return ConsoleModel Returns ConsoleModel.
     */
    public function endTable(int $bufferId): ConsoleModel {
        $this->message .= '</tbody>
        </table>';
        $this->buffers[$bufferId] = $this->message;
        return $this;
    }

    /**
     * Creates a table row <code>tr</code> element and adds it to the buffer with ID <code>$bufferId</code>. If <code>$bufferId</code> is not provided, it automatically creates one.
     * @param int $type of the tr element
     * @param string $content Content of the tr element
     * @param int|null $bufferId generated with createBuffer() method.
     * @param string $customClasses custom classes.
     * @return ConsoleModel Returns ConsoleModel.
     * @example tr(content: "Hello World", bufferId: int|bool) description
     */
    public function tr(array $tdata = ["Row content" => "custom classes"], int | null $bufferId = null, string | null $customClasses = null): ConsoleModel {
        if ($bufferId === null)
            $bufferId = $this->createBuffer();
        $this->message .= '<tr class="'.($customClasses ?? "").'">';
        foreach ($tdata as $content => $customClasses) {
            $this->message .= '<td class="'.($customClasses ?? "bg-slate-800").'">'.$content.'</td>';
        }
        $this->message .=  '</tr>';
        $this->buffers[$bufferId] = $this->message;
        return $this;
    }

    /**
     * Sends the currently buffered output to the terminal, then empty the output buffer.
     * @return bool
     */
    public function flushBuffer(): bool {
        $this->logger->log($this->message);
        $this->message = "";
        return true;
    }

    /**
     * Loads a previously saved buffer into mainstream.
     * @param int $bufferId
     * @return bool Returns <code>false</code> if <code>$bufferId</code> is not set, returns <code>true</code> otherwise.
     */
    public function loadBuffer(int $bufferId): bool {
        if (!isset($this->buffers[$bufferId]))
            return false;
        $this->message = $this->buffers[$bufferId];
        return true;
    }
    public function raw($message, bool $clear = false): bool {
        return $this->logger->simpleLog($message, $clear);
    }
    public function clear(): bool {
        return $this->logger->reset();
    }
    public function input($message): mixed {
        return $this->logger->input($message);
    }
    /**
     * @see App\Interfaces\Commands\CLI\ILogger
     */
    public function updatable(int $interval, callable $callable): string | false {
        return $this->logger->updatable($interval, $callable);
    }
    public function success($content) {
        return $this->paragraph($content, customClasses: "text-green-600 p-8")->flushBuffer();
    }
    public function error($content) {
        return $this->paragraph($content, customClasses: "text-red-400 p-8")->flushBuffer();
    }
    public function warning($content) {
        return $this->paragraph($content, customClasses: "text-yellow-600 p-8")->flushBuffer();
    }
    public function info($content) {
        return $this->paragraph($content, customClasses: "text-blue-800 p-8")->flushBuffer();
    }
} 