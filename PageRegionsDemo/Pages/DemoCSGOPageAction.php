<?php

namespace PageRegionsDemo\Pages;

use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;
use OLOG\PageRegions\PageRegions;

class DemoCSGOPageAction implements
    InterfacePageTitle
{
    static public function getUrl()
    {
        return "/csgo";
    }

    public function pageTitle()
    {
        return 'PHP-PageRegions demo';
    }

    public function action()
    {
        $html = '';

        $html .= '<div class="panel panel-default"><div class="panel-heading">head</div><div class="panel-body">';
        $html .= PageRegions::renderRegion('head'); // TODO: replace with actual region ID
        $html .= '</div></div>';

        AdminLayoutSelector::render($html);
    }
}