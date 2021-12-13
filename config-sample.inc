<?php

// Database config
const DB_NAME = 'word-art';
const DB_USER = 'word-art';
const DB_PASS = 'word-art';
const DB_HOST = 'localhost';
const DB_CHARSET = 'utf8';
const DB_PORT = 3306;

// Memcache config
const MEMCACHE_HOST = 'localhost';
const MEMCACHE_PORT = 11211;
const MEMCACHE_PERIOD = 60;

// Base url for parsed site
const PARSER_BASE_URL = 'http://www.world-art.ru/cinema/';
// Items per query on top rated
const PARSER_ITEMS_PER_QUERY = 10;
// Url for full film information
const PARSER_FILM_URL = 'cinema.php?id=%d';
// Url for history position
const PARSER_FILM_HISTORY = 'votes_history.php?id=%d';

// CLI key for calling parser via cron
const CRON_KEY = '111';

// Cache directory
const CACHE_PARSER_DIRECTORY = __DIR__ . '/cache/';
// Parser files cache period, in seconds
const CACHE_PARSER_PERIOD = 3600 * 24 * 7;
//define('CACHE_PARSER_PERIOD', 0);

// Images
define('IMAGE_DIRECTORY', __DIR__ . '/images/');
// HTML templates
define('TEMPLATE_DIRECTORY', __DIR__ . '/tpl/');
// images web
define('WEB_IMAGE_DIRECTORY', '/images/');
