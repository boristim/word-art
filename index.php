<?php

require_once 'config.inc';
require_once 'inc/functions.inc';
require_once 'inc/Database.php';


use Web\View, Cli\Parser;

if ((php_sapi_name() === 'cli') && ($opt = getopt('k::')) && isset($opt['k']) && ($opt['k'] === CRON_KEY)) {
    _log('Start parsing', 1);
    require_once 'inc/ModelParser.php';
    require_once 'inc/Browser.php';
    require_once 'inc/Parser.php';
    try {
        (new Parser())->parse();
    } catch (Exception $exception) {
        _log($exception->getMessage(),1);
    }
} else {
    require_once 'inc/ModelView.php';
    require_once 'inc/View.php';
    (new View())->response();
}