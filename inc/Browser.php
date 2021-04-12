<?php

namespace Cli;

use Exception;

/**
 * Trait Browser
 * @package Cli
 */
trait Browser
{
    /**
     * @throws Exception
     */
    public function initBrowser(): void
    {
        if (!is_dir(CACHE_PARSER_DIRECTORY)) {
            _log('Creating cache directory ' . CACHE_PARSER_DIRECTORY);
            if (!mkdir(CACHE_PARSER_DIRECTORY)) {
                throw new Exception(sprintf('Browser error: Unable to create cache parser directory %s', CACHE_PARSER_DIRECTORY));
            }
            file_put_contents(CACHE_PARSER_DIRECTORY . '.htaccess', 'Deny from all' . PHP_EOL . 'php_flag engine off' . PHP_EOL);
        }
    }

    /**
     * @param string $url
     * @param bool $is_binary
     * @return string
     */
    public function getPage(string $url, bool $is_binary): string
    {
        $cacheFile = CACHE_PARSER_DIRECTORY . str_replace([':', '/', '\\', '&', '?', '#'], '_', $url);
        $result = null;
        if (file_exists($cacheFile) && (filectime($cacheFile) > (time() - CACHE_PARSER_PERIOD))) {
            $result = file_get_contents($cacheFile);
            _log("Url $url is cached");
        } else {
            $url = PARSER_BASE_URL . $url;
            _log("Fetching $url");
            $result = $this->fetchUrl($url, $is_binary);
            if (!$is_binary) {
                $result = html_entity_decode($result);
                $result = str_replace(html_entity_decode('&nbsp;'), ' ', $result);
            }
            file_put_contents($cacheFile, $result);
        }
        if ($is_binary) {
            return $cacheFile;
        } else {
            return $result;
        }
    }

    /**
     * @param string $url
     * @param bool $is_binary
     * @param array $options
     * @return string
     */
    public function fetchUrl(string $url, bool $is_binary, array $options = []): string
    {
        static $prev_url;

        $timeout = 30;
        $ch = curl_init();
        if (isset($options[CURLOPT_REFERER])) {
            $prev_url = $options[CURLOPT_REFERER];
        } elseif (!isset($prev_url)) {
            $page_arr = range('a', 'z');
            shuffle($page_arr);
            $prev_url = 'http://' . implode('', array_slice($page_arr, 0, rand(5, 20))) . '.ru';
        }
        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.3,en;q=0.3',
            'Cache-Control: max-age=0',
            'Connection: keep-alive',
            'Cache-Control: max-age=0',
            'Referer: ' . $prev_url,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',
        ];
        $prev_url = $url;
        $default_options = array(
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_VERBOSE => 0,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_AUTOREFERER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_COOKIEFILE => CACHE_PARSER_DIRECTORY . 'cookie.txt',
            CURLOPT_COOKIEJAR => CACHE_PARSER_DIRECTORY . 'cookie.txt',
            CURLOPT_URL => $url,
            CURLOPT_HEADER => !$is_binary
        );
        if (count($options)) {
            foreach ($options as $key => $val) {
                $default_options[$key] = $val;
            }
        }
        curl_setopt_array($ch, $default_options);
        $result = curl_exec($ch);
        if (!$is_binary) {
            $sections = explode("\x0d\x0a\x0d\x0a", $result, 2);
            while (!strncmp($sections[1], 'HTTP/', 5)) {
                $sections = explode("\x0d\x0a\x0d\x0a", $sections[1], 2);
            }
            $headers = $sections[0];
            $result = $sections[1];
            if (preg_match('/^Content-Encoding: gzip/mi', $headers)) {
                $result = $this->gunzip($result);
            }
            if (preg_match('/^Content-Type:.*charset=(.*)\r\n/mi', $headers, $matches)) {
                $result = mb_convert_encoding($result, 'utf-8', $matches[1]);
            } else {
                $result = mb_convert_encoding($result, 'utf-8');
            }
        }
        if (curl_errno($ch) < 400) {
            return $result;
        }

        return false;
    }

    /**
     * @param $zipped
     * @return false|string
     */
    private function gunzip($zipped)
    {
        $offset = 0;
        if (substr($zipped, 0, 2) == "\x1f\x8b") {
            $offset = 2;
        }
        if (substr($zipped, $offset, 1) == "\x08") {
            return gzinflate(substr($zipped, $offset + 8));
        }

        return false;
    }
}

