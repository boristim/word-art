<?php

namespace Cli;

use Db\ModelParser;
use Exception;

class Parser
{
    use ModelParser;
    use Browser;

    const TOP_10_FIELDS = ['position', 'name', 'calc_ball', 'votes', 'avg_ball'];

    function __construct()
    {
        try {
            $this->initBrowser();
            $this->initDatabase();
        } catch (Exception $exception) {
            throw new Exception(sprintf('Parser error: %s', $exception->getMessage()), $exception->getCode(), $exception);
        }
        if (!is_dir(IMAGE_DIRECTORY)) {
            if (!is_dir(IMAGE_DIRECTORY)) {
                _log('Creating images directory ' . IMAGE_DIRECTORY);
                if (!mkdir(IMAGE_DIRECTORY)) {
                    throw new Exception(sprintf('Parser error: Unable to create images directory %s', IMAGE_DIRECTORY));
                }
                file_put_contents(CACHE_PARSER_DIRECTORY . '.htaccess', 'php_flag engine off' . PHP_EOL);
            }
        }
    }

    public function parse()
    {
        foreach ($this->newLoad() as $filmTypeId => $row) {
            $this->parseTopFilms(sprintf($row['url'], PARSER_ITEMS_PER_QUERY), $row['loadId'], $filmTypeId);
        }
    }

    private function parseTopFilms(string $url, int $loadId, int $filmTypeId)
    {
        $page = $this->getPage($url, false);
        $fieldNo = 0;

        if (preg_match_all('/<td bgcolor=#D7D7D7.*?>(.*?)<\/td>/', $page, $matches)) {
            foreach ($matches[1] as $npp => $match) {
                $item[self::TOP_10_FIELDS[$fieldNo++]] = $match;
                if (($npp + 1) % 5 === 0) {
                    $fieldNo = 0;
                    if (preg_match('/\[(\d{4}?)]/', $item['name'], $match)) {
                        $item['year'] = $match[1];
                    }
                    if (preg_match('/cinema\.php\?id=(\d+)/', $item['name'], $matches)) {
                        $item['word_art_id'] = $matches[1];
                    }
                    $item['name'] = preg_replace('/\[(\d{4}?)]/', '', $item['name']);
                    $item = array_map(
                        function ($value) {
                            return trim(strip_tags($value));
                        },
                        $item
                    );
                    if (!$item['film_id'] = $this->getFilmId($item)) {
                        $this->parseFilm($item);
                        $item['film_id'] = $this->saveFilm($item);
                    }
                    $item['load_id'] = $loadId;
                    $item['film_type_id'] = $filmTypeId;
                    $rateId = $this->saveRate($item);
                    _log('Saved rate id: ' . $rateId);
                }
            }
        }
    }

    private function fetchImage($url)
    {
        $cacheFile = $this->getPage($url, true);
        $imgFile = pathinfo($cacheFile, PATHINFO_BASENAME);
        copy($cacheFile, IMAGE_DIRECTORY . $imgFile);
        return $imgFile;
    }

    private function parseFilm(array &$item)
    {
        _log('Parsing film: ' . $item['word_art_id']);
        $page = $this->getPage(sprintf(PARSER_FILM_URL, $item['word_art_id']), false);
        if (preg_match('/<p align=justify class=\'review\'>(.*?)<\/p>/', $page, $matches)) {
            $item['description'] = $matches[1];
        }
        if (preg_match('/<img src=\'(img\/\d+\/' . $item['word_art_id'] . '\/\d+.jpg)\' width=300 border=1 alt=/', $page, $matches)) {
            $item['cover'] = $this->fetchImage($matches[1]);
        }
//        _log('Parsing history info for film: '.$item['word_art_id']);
//        $page = $this->getPage(sprintf(PARSER_FILM_HISTORY, $item['word_art_id']), false);
    }
}