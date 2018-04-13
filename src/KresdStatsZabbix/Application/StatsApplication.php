<?php

namespace KresdStatsZabbix\Application;

use KresdStatsZabbix\Command\GetStatisticsValuesCommand;
use KresdStatsZabbix\Command\PrepareJsonDataCommand;
use KresdStatsZabbix\Application\Config\LoadConfig;
use Symfony\Component\Console\Application;

class StatsApplication extends Application
{

    /** @var LoadConfig $config */
    private $config;

    public function __construct(LoadConfig $config, string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);
        $this->config = $config;
    }

    public function loadCommands(){
        $this->add(new PrepareJsonDataCommand($this->config));
        $this->add(new GetStatisticsValuesCommand($this->config));
    }

}