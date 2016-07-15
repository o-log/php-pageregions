<?php

require_once "../vendor/autoload.php";

\OLOG\ConfWrapper::assignConfig(\PageRegionsDemo\PageRegionsDemoInitConfig::getOldConfig());
\PageRegionsDemo\PageRegionsDemoInitConfig::initConfig();

\OLOG\Router::matchAction(\PageRegionsDemo\Pages\MainPageAction::class, 0);

\OLOG\Router::matchAction(\OLOG\PageRegions\Admin\BlocksListAction::class, 0);
\OLOG\Router::matchAction(\OLOG\PageRegions\Admin\BlockEditAction::class, 0);

