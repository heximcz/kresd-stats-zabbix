<?php

namespace KresdStatsZabbix\Application\Logger;

interface InterfaceLogger
{
    public function error(string $message);

    public function info(string $message);

    public function debug(string $message);

    public function notice(string $message);

}