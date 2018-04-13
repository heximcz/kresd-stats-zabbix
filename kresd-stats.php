<?php

use KresdStatsZabbix\Application\StatsApplication;
use KresdStatsZabbix\Application\Config\LoadConfig;

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'vendor/autoload.php';

try {
    $app = new StatsApplication( new LoadConfig(),'Knot statistics for Zabbix', '0.1');
    $app->loadCommands();
    $app->run();
} catch (Exception $exception) {
    echo 'Caught exception: ' . $exception->getMessage() . PHP_EOL;
}
