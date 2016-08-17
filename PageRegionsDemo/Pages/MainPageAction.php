<?php

namespace PageRegionsDemo\Pages;

use OLOG\Auth\Admin\CurrentUserNameTrait;
use OLOG\BT\BT;
use OLOG\BT\InterfacePageTitle;
use OLOG\BT\InterfaceUserName;
use OLOG\BT\Layout;
use OLOG\PageRegions\Admin\BlocksListAction;
use OLOG\PageRegions\PageRegions;

class MainPageAction implements InterfacePageTitle, InterfaceUserName
{
	use CurrentUserNameTrait;

	static public function getUrl()
	{
		return "/";
	}

	public function currentPageTitle()
	{
		return 'PHP-PageRegions demo';
	}

	public function action()
	{
		$html = '';

        $html .= '<div>' . BT::a(DemoCSGOPageAction::getUrl(), 'Demo CSGO page') . '</div>';
        $html .= '<div>' . BT::a(BlocksListAction::getUrl(), 'Blocks admin') . '</div>';

		$html .= '<div class="panel panel-default"><div class="panel-heading">head</div><div class="panel-body">';
		$html .= PageRegions::renderRegion('head'); // TODO: replace with actual region ID
		$html .= '</div></div>';

        $html .= '<div>You can set ' . PageRegions::INVISIBLE_BLOCKS_DEBUG_COOKIE_NAME . ' cookie to see invisible blocks debug in html comments.</div>';

		Layout::render($html);
	}
}