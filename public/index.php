<?php

require_once "../vendor/autoload.php";

\PageRegionsDemo\PageRegionsDemoInitConfig::initConfig();

\OLOG\Auth\RegisterRoutes::registerRoutes();
\OLOG\PageRegions\RegisterRoutes::registerRoutes();

\OLOG\Router::matchAction(\PageRegionsDemo\Pages\MainPageAction::class, 0);
