<?php

namespace PageRegionsDemo;

use OLOG\Layouts\InterfaceMenu;
use OLOG\Layouts\InterfaceTopActionObj;
use OLOG\PageRegions\Admin\PageRegionsAdminMenu;
use PageRegionsDemo\Pages\MainPageAction;

class PageregionsDemoAdminActionsBase implements
    InterfaceMenu,
    InterfaceTopActionObj
{
    public function topActionObj(){
        return new MainPageAction();
    }

    static public function menuArr()
    {
        return array_merge(PageRegionsAdminMenu::menuArr());
    }
}