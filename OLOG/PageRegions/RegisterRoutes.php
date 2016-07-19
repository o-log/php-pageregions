<?php

namespace OLOG\PageRegions;

class RegisterRoutes
{
    static public function registerRoutes(){
        \OLOG\Router::matchAction(\OLOG\PageRegions\Admin\BlocksListAction::class, 0);
        \OLOG\Router::matchAction(\OLOG\PageRegions\Admin\BlockEditAction::class, 0);
    }
}