<?php

namespace KresdStatsZabbix\Command;

use KresdStatsZabbix\Application\Http\GetHttpData;
use KresdStatsZabbix\Application\Config\LoadConfig;
use KresdStatsZabbix\Application\Logger\OutputLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class PrepareJsonDataCommand
 * @package KresdStatsZabbix\Command
 */
class PrepareJsonDataCommand extends Command
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
            ->setName('stats:prepare')
            ->setDescription('Prepare data for Zabbix.')
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
            $http = new GetHttpData();
            $data = $http->getJsonDataFromUrl($this->config);

            $fs = new Filesystem();
            if (!$fs->isAbsolutePath($params['stats-file'])) {
                throw new \Exception($params['stats-file'] . ' is not absolute path!');
            }
            $fs->dumpFile($params['stats-file'], $data);
        } catch (\Exception | IOException $exception) {
            if ($params['email']['allow']) {
                $logger->addMailBody($exception->getMessage());
                $logger->send($params['email']['from'], $params['email']['to']);
            }
            throw $exception;
        }

    }


}