<?php

require_once 'vendor/autoload.php';

\OLOG\ConfWrapper::assignConfig(\PageRegionsDemo\PageRegionsDemoInitConfig::getOldConfig());
\PageRegionsDemo\PageRegionsDemoInitConfig::initConfig();

\OLOG\Model\CLI\CLIMenu::run();
