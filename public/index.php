<?php

require_once "../vendor/autoload.php";

\PageRegionsDemo\PageRegionsDemoInitConfig::initConfig();

\OLOG\Auth\RegisterRoutes::registerRoutes();
\OLOG\PageRegions\RegisterRoutes::registerRoutes();

\OLOG\Router::processAction(\PageRegionsDemo\Pages\MainPageAction::class, 0);
\OLOG\Router::matchAction(\PageRegionsDemo\Pages\DemoCSGOPageAction::class, 0);
