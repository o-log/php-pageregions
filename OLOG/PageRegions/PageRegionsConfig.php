<?php

namespace OLOG\PageRegions;

class PageRegionsConfig
{
    static protected $regions_arr = [];

    /**
     * @return array
     */
    public static function getRegionsArr()
    {
        return self::$regions_arr;
    }

    /**
     * @param array $regions_arr
     */
    public static function setRegionsArr($regions_arr)
    {
        self::$regions_arr = $regions_arr;
    }
}