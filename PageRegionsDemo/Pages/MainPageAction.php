<?php

namespace PageRegionsDemo\Pages;

use OLOG\HTML;
use OLOG\InterfaceAction;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;
use OLOG\PageRegions\Admin\BlocksListAction;
use OLOG\PageRegions\InterfacePageRegionsPageType;
use OLOG\PageRegions\PageRegions;

class MainPageAction implements
    InterfaceAction,
    InterfacePageTitle,
    InterfacePageRegionsPageType
{
    public function pageRegionsPageType(){
        return 'main_page';
    }

	public function url()
	{
		return "/";
	}

	public function pageTitle()
	{
		return 'PHP-PageRegions demo';
	}

	public function action()
	{
		$html = '';

        $html .= '<div>' . HTML::a(DemoCSGOPageAction::getUrl(), 'Demo CSGO page') . '</div>';
        $html .= '<div>' . HTML::a((new BlocksListAction())->url(), 'Blocks admin') . '</div>';

		$html .= '<div class="panel panel-default"><div class="panel-heading">head</div><div class="panel-body">';
		$html .= PageRegions::renderRegion('head'); // TODO: replace with actual region ID
		$html .= '</div></div>';

        $html .= '<div>You can set ' . PageRegions::INVISIBLE_BLOCKS_DEBUG_COOKIE_NAME . ' cookie to see invisible blocks debug in html comments.</div>';

        $html .= '<div>Page type: ' . $this->pageRegionsPageType() . '</div>';

		AdminLayoutSelector::render($html);
	}
}