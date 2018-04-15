<?php

namespace KresdStatsZabbix\Command;

use KresdStatsZabbix\Application\Http\GetHttpData;
use KresdStatsZabbix\Application\Config\LoadConfig;
use KresdStatsZabbix\Application\Logger\OutputLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class PrepareJsonDataCommand
 * @package KresdStatsZabbix\Command
 */
class PrepareJsonDataCommand extends Command
{

    /** @var LoadConfig $config */
    private $config;
    /** @var array $params */
    private $params;
    /** @var OutputLogger $logger */
    private $logger;

    /**
     * PrepareJsonDataCommand constructor.
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
            ->setName('stats:prepare')
            ->setDescription('Prepare data for Zabbix.')
            ->addOption('addr', '-i', InputOption::VALUE_REQUIRED,"IP address")
            ->addOption('port', '-p', InputOption::VALUE_REQUIRED, "Port")
            ->addUsage('stats:prepare [by default will be used \'server:\' section from config.yml]')
            ->addUsage('stats:prepare -i 127.0.0.1 -p 8053 [override \'server:\' section]')
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

        // override config.yml
        if (!is_null($addr) && !is_null($port)) {
            $this->createTemporaryData($this->params['stats-file'] . $addr . '_' . $port . '.tmp', $addr, $port);
            return;
        }

        // use config.yml
        $this->createTemporaryData(null, $this->params['server']['addr'], $this->params['server']['port']);
    }

    /**
     * @param null|string $file
     * @param string $addr
     * @param string $port
     */
    private function createTemporaryData($file = null, $addr, $port) {

        try {
            $http = new GetHttpData();
            $url = 'https://' . $addr . ':' . $port . '/stats';
            $data = $http->getJsonDataFromUrl($url);

            $fs = new Filesystem();

            if ($file === null) {
                $file = $this->params['stats-file'] . $this->params['server']['addr'] . '_' . $this->params['server']['port'] . '.tmp';
            }

            if (!$fs->isAbsolutePath($file)) {
                throw new \Exception($file . ' is not absolute path!');
            }

            $fs->dumpFile($file, $data);

        } catch (\Exception | IOException $exception) {
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