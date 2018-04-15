<?php

namespace KresdStatsZabbix\Command;

use KresdStatsZabbix\Application\Config\LoadConfig;
use KresdStatsZabbix\Application\Logger\OutputLogger;
use KresdStatsZabbix\Application\Process\ProcessJsonData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PrepareJsonDataCommand
 * @package KresdStatsZabbix\Command
 */
class GetStatisticsValuesCommand extends Command
{

    /** @var LoadConfig $config */
    private $config;
    /** @var array $params */
    private $params;
    /** @var OutputLogger $logger */
    private $logger;

    /**
     * GetStatisticsValuesCommand constructor.
     * @param LoadConfig $config
     */
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
            ->addOption('addr', '-i', InputOption::VALUE_REQUIRED,"IP address")
            ->addOption('port', '-p', InputOption::VALUE_REQUIRED, "Port")
            ->addUsage('stats:get worker.queries')
            ->addUsage('stats:get worker.queries [by default will be used \'server:\' section from config.yml]')
            ->addUsage('stats:get worker.queries -i 127.0.0.1 -p 8053 [override \'server:\' section]')
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
        $this->logger = new OutputLogger($output);
        $this->params = $this->config->getConfig();

        $addr = $input->getOption('addr');
        $port = $input->getOption('port');

        $tmpFile = $this->params['stats-file'] . $this->params['server']['addr'] . '_' . $this->params['server']['port'] . '.tmp';

        // override config.yml
        if (!is_null($addr) && !is_null($port)) {
            $tmpFile = $this->params['stats-file'] . $addr . '_' . $port . '.tmp';
        }

        try {
            $nameOfValue = $input->getArgument('name');
            $value = new ProcessJsonData();
            echo $value->getValueFromJsonParameter($tmpFile, $nameOfValue);
        } catch (\Exception | InvalidArgumentException $exception) {
            $this->sendEmail($exception);
            throw $exception;
        }

    }

    /**
     * @param \Exception $exception
     */
    private function sendEmail(\Exception $exception) {
        if ($this->params['email']['allow']) {
            $this->logger->addMailBody($exception->getMessage());
            $this->logger->send($this->params['email']['from'], $this->params['email']['to']);
        }
    }


}