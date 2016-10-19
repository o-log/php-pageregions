<?php

namespace OLOG\PageRegions\Admin;

use OLOG\CheckClassInterfaces;
use OLOG\Layouts\InterfaceMenu;
use OLOG\PageRegions\PageRegionsConfig;

class PageregionsAdminActionsBaseProxy implements InterfaceMenu
{
    static public function menuArr(){
        $admin_actions_base_classname = PageRegionsConfig::getAdminActionsBaseClassname();
        if (CheckClassInterfaces::classImplementsInterface($admin_actions_base_classname, InterfaceMenu::class)){
            return $admin_actions_base_classname::menuArr();
        }

        return [];
    }
}