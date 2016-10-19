<?php

namespace OLOG\PageRegions\Admin;

use OLOG\Auth\Auth;
use OLOG\Layouts\InterfaceMenu;
use OLOG\Layouts\MenuItem;
use OLOG\PageRegions\Permissions;

class PageRegionsAdminMenu implements InterfaceMenu
{
	static public function menuArr()
	{
	    $menu_arr = [];

        if (Auth::currentUserHasAnyOfPermissions([Permissions::PERMISSION_PAGEREGIONS_MANAGE_BLOCKS])) {

            $menu_arr = array_merge($menu_arr, [
                new MenuItem((new BlocksListAction())->pageTitle(), (new BlocksListAction())->url(), NULL, 'glyphicon glyphicon-pushpin')
            ]);
        }

        return $menu_arr;
	}
}