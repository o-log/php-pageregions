<?php

namespace OLOG\PageRegions;

class PageRegionsConfig
{
    static protected $regions_arr = [];
    static protected $admin_actions_base_classname;

    /**
     * @return mixed
     */
    public static function getAdminActionsBaseClassname()
    {
        return self::$admin_actions_base_classname;
    }

    /**
     * @param mixed $admin_actions_base_classname
     */
    public static function setAdminActionsBaseClassname($admin_actions_base_classname)
    {
        self::$admin_actions_base_classname = $admin_actions_base_classname;
    }

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