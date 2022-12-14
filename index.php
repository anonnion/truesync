#!/usr/bin/env php
<?php


use App\Commands\CLI\Monitor;
use App\Events\RunSchedule;
use App\Events\ReceiveOnShutdown;
use App\Models\ConsoleModel;


require_once 'vendor/autoload.php';
ini_set('memory_limit', -1);

$autoloader = function ($class) {
    $file = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $class) . '.php';
    return include_once $file;
};
spl_autoload_register($autoloader);
register_shutdown_function(function () {
    return ReceiveOnShutdown::onShutdown();
});
$config = new App\Config\BaseConfig();


$func = function () {
    global $config;
    $logger = new ConsoleModel();
    $data = (array) json_decode(file_get_contents($config->getConfig('WATCHER_CONFIG')));
    foreach ((array)$data['items'] as $item) {
        $logger->raw("Checking \"$item->name\"<br>", true);
        ($e = new Monitor($item->name, $config))->monitor();
    }
    $e->render();
};

new App\Commands\CLI\Scheduler("Check for new music", ["interval" => 10000], "\$func");
(new RunSchedule(6))->loadStackFromFile()->start(false);
