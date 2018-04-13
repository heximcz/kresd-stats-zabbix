<?php

namespace KresdStatsZabbix\Application\Process;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ProcessJsonData
 * @package KresdStatsZabbix\Application\Process
 */
class ProcessJsonData
{

    /**
     * @param string $file Path to the statistics file
     * @param string $key Name of the value
     * @return string
     * @throws \Exception
     */
    public function getValueFromJsonParameter(string $file, string $key): string
    {
        $fs = new Filesystem();
        if ($fs->exists($file) && $jsonData = file_get_contents($file)) {
            $data = json_decode($jsonData, true);
            if (array_key_exists($key, $data)) {
                return $data[$key];
            }
            // after server reboot some values non-exist
            return "-1";
            //throw new \Exception('Key: ' . $key . ' not exist.');
        }
        throw new \Exception('File: ' . $file . ' not exist or it is empty.');
    }

}

