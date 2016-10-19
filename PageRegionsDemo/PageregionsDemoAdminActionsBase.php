<?php

namespace PageRegionsDemo;

use OLOG\Layouts\InterfaceMenu;
use OLOG\PageRegions\Admin\PageRegionsAdminMenu;

class PageregionsDemoAdminActionsBase implements InterfaceMenu
{
    static public function menuArr()
    {
        return array_merge(PageRegionsAdminMenu::menuArr());
    }
}