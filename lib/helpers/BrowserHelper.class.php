<?php

class BrowserHelper
{
    public static function isOpera()
    {
        return !empty($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false);
    }

    public static function isJsonAcceptable()
    {
        return !empty($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
}
