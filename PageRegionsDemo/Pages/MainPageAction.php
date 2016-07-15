<?php

namespace PageRegionsDemo\Pages;

use OLOG\BT\LayoutBootstrap;
use OLOG\PageRegions\PageRegions;

class MainPageAction
{
    static public function getUrl(){
        return "/";
    }
    
    public function action(){
        $html = '';

        $html .= '<div class="panel panel-default"><div class="panel-heading">head</div><div class="panel-body">';
        $html .= PageRegions::renderRegion('head'); // TODO: replace with actual region ID
        $html .= '</div></div>';
        
        LayoutBootstrap::render($html);
    }
}