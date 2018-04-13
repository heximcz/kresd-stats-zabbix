<?php

namespace KresdStatsZabbix\Application\Http;

use KresdStatsZabbix\Application\Config\LoadConfig;

class GetHttpData
{

    /**
     * @param LoadConfig $config
     * @throws \Exception
     * @return string
     */
    public function getJsonDataFromUrl(LoadConfig $config): ?string
    {
        $params = $config->getConfig();
        $url = 'https://'.$params['server']['addr'].':'.$params['server']['port'].'/'.$params['server']['service'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT,
            'Mozilla/4.0 (compatible; App.KresdStatisticsZabbix PHP Bot; ' . php_uname('a') . '; PHP/' . phpversion() . ')'
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $response = curl_exec($ch);

        if ($response === false) throw new \Exception('Curl error: ' . curl_error($ch));

        return $response;
    }

}

