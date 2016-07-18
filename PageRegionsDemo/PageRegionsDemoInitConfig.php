<?php

namespace PageRegionsDemo;

use OLOG\Model\ModelConstants;
use OLOG\PageRegions\PageRegionConstants;
use OLOG\PageRegions\PageRegionsConfig;

class PageRegionsDemoInitConfig
{
    static public function getOldConfig(){
        // for mac
        header('Content-Type: text/html; charset=utf-8');
        date_default_timezone_set('Europe/Moscow');

        $c = [];

        $c[ModelConstants::MODULE_CONFIG_ROOT_KEY] = [
            'db' => [
                PageRegionConstants::DB_ID => [
                    'host' => 'localhost',
                    'db_name' => 'db_pageregions',
                    'user' => 'root',
                    'pass' => '1'
                ]
            ]
        ];

        return $c;
    }

    static public function initConfig(){
        

        PageRegionsConfig::setRegionsArr(
            [
                'head' => 'head',
                'footer' => 'footer'
            ]
        );
    }
}