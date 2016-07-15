<?php

require_once 'vendor/autoload.php';

\OLOG\ConfWrapper::assignConfig(\PageRegionsDemo\PageRegionsDemoConfig::get());

\OLOG\Model\CLI\CLIMenu::run();
