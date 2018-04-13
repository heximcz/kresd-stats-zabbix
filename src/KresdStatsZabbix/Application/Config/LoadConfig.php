<?php

namespace KresdStatsZabbix\Application\Config;

use Symfony\Component\Yaml\Parser;

/**
 * Class LoadConfig
 * @package KresdStatsZabbix\Config
 */
class LoadConfig
{

    private $defaultConfigPath;
    private $customConfigPath;
    /** @var array $config */
    private $config;

    /**
     * loadConfig constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        $this->defaultConfigPath =  $path . 'config.default.yml';
        $this->customConfigPath = $path . 'config.yml';
        $this->createConfig();
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    private function parseConfig(string $configPath)
    {
        $parser = new Parser ();
        return $parser->parse(file_get_contents($configPath));
    }

    /**
     * @throws \Exception
     */
    private function createConfig()
    {
        //TODO:
        if (file_exists($this->defaultConfigPath)) {
            $defaultConf = $this->parseConfig($this->defaultConfigPath);
            if (file_exists($this->customConfigPath)) {
                $customConf = $this->parseConfig($this->customConfigPath);
                $this->config = array_replace_recursive($defaultConf, $customConf);
                return;
            }
            $this->config = $defaultConf;
            return;
        }
        throw new \Exception ('FATAL ERROR: config.default.yml no exist!' . get_class($this));
    }

}