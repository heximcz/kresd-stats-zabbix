<?php

namespace KresdStatsZabbix\Command;

use KresdStatsZabbix\Application\Config\LoadConfig;
use KresdStatsZabbix\Application\Logger\OutputLogger;
use KresdStatsZabbix\Application\Process\ProcessJsonData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PrepareJsonDataCommand
 * @package KresdStatsZabbix\Command
 */
class GetStatisticsValuesCommand extends Command
{

    private $config;

    public function __construct(LoadConfig $config)
    {
        parent::__construct();
        $this->config = $config;
    }

    protected function configure()
    {
        $this
            ->setName('stats:get')
            ->setDescription('Get statistical data for Zabbix.')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the value.')
            ->addUsage('stats:get worker.queries')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new OutputLogger($output);

        $params = $this->config->getConfig();

        try {

            $nameOfValue = $input->getArgument('name');
            $value = new ProcessJsonData();
            echo $value->getValueFromJsonParameter($params['stats-file'], $nameOfValue);

        } catch (\Exception | InvalidArgumentException $exception) {
            if ($params['email']['allow']) {
                $logger->addMailBody($exception->getMessage());
                $logger->send($params['email']['from'], $params['email']['to']);
            }
            throw $exception;
        }

    }


}