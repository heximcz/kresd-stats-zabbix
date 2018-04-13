<?php

namespace KresdStatsZabbix\Application\Logger;

use Symfony\Component\Console\Output\OutputInterface;

class OutputLogger extends AbstractMailLogger implements InterfaceLogger
{

    /** @var OutputInterface $output */
    private $output;

    public function __construct(OutputInterface $output)
    {
        parent::__construct();
        $this->output = $output;
    }

    public function notice(string $message)
    {
        $this->output->writeln('<comment>' . $message . '</comment>');
    }

    public function info(string $message)
    {
        $this->output->writeln('<info>' . $message . '</info>');
    }

    public function error(string $message)
    {
        $this->output->writeln('<error>' . $message . '</error>');
    }

    public function debug(string $message)
    {
        $this->output->writeln('<question>' . $message . '</question>');
    }

    public function addMailBody(string $message)
    {
        $this->mailBody .= sprintf("%1s [%2s]: %3s\n", "ERROR", date("Y-d-m H:i:s"), $message);
    }

}