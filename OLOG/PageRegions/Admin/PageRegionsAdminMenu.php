<?php

namespace OLOG\PageRegions\Admin;

use OLOG\BT\InterfaceMenu;
use OLOG\BT\MenuItem;

class PageRegionsAdminMenu implements InterfaceMenu
{
	static public function menuArr()
	{
		return [
			new MenuItem(BlocksListAction::pageTitle(), BlocksListAction::getUrl(), NULL, 'glyphicon glyphicon-pushpin')
		];
	}
}