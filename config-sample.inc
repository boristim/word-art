<?php

// Database config
define('DB_NAME', 'word-art');
define('DB_USER', 'word-art');
define('DB_PASS', 'word-art');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_PORT', 3306);

// Memcache config
define('MEMCACHE_HOST', 'localhost');
define('MEMCACHE_PORT', 11211);
define('MEMCACHE_PERIOD', 60);

// Base url for parsed site
define('PARSER_BASE_URL', 'http://www.world-art.ru/cinema/');
// Items per query on top rated
define('PARSER_ITEMS_PER_QUERY', 10);
// Url for full film information
define('PARSER_FILM_URL', 'cinema.php?id=%d');
// Url for history position
define('PARSER_FILM_HISTORY', 'votes_history.php?id=%d');

// CLI key for calling parser via cron
define('CRON_KEY', '111');

// Cache directory
define('CACHE_PARSER_DIRECTORY', __DIR__ . '/cache/');
// Parser files cache period, in seconds
define('CACHE_PARSER_PERIOD', 3600 * 24 * 7);
//define('CACHE_PARSER_PERIOD', 0);

// Images
define('IMAGE_DIRECTORY', __DIR__ . '/images/');
// HTML templates
define('TEMPLATE_DIRECTORY', __DIR__ . '/tpl/');
// images web
define('WEB_IMAGE_DIRECTORY', '/images/');
