<?php

namespace OLOG\PageRegions;

class RegisterRoutes
{
    static public function registerRoutes(){
        \OLOG\Router::processAction(\OLOG\PageRegions\Admin\BlocksListAction::class, 0);
        \OLOG\Router::processAction(\OLOG\PageRegions\Admin\BlockEditAction::class, 0);
        \OLOG\Router::processAction(\OLOG\PageRegions\Admin\RegionBlocksListAction::class, 0);
        \OLOG\Router::processAction(\OLOG\PageRegions\Admin\SearchAjax::class, 0);
    }
}