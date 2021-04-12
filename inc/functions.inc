<?php

/**
 * @param $msg
 * @param bool $showMessages
 */
function _log($msg, bool $showMessages = false)
{
    static $firstMessage = true, $messages;
    $msg = date('Y-m-d H:i:s ') . str_repeat('=', 60) . PHP_EOL . var_export($msg, 1) . PHP_EOL;
    if ($showMessages) {
        $messages = $showMessages;
    }
    if($messages) {
        print $msg;
    }
    file_put_contents(realpath(__DIR__ . '/../') . '/log.log', $msg, $firstMessage ? 0 : FILE_APPEND);
    $firstMessage = false;
}