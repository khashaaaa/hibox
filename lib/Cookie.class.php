<?php
/**
 * Класс для работы с куками
 */
class Cookie
{
    public static function get($key, $default = null)
    {
        return array_key_exists($key, $_COOKIE) ? $_COOKIE[$key] : $default;
    }

    public static function set($key, $value, $expire = 0, $path = '/', $domain = null)
    {
        $_COOKIE[$key] = $value;
        if (! headers_sent())
            setcookie($key, $value, $expire, $path, $domain);
    }

    public static function store($key, $value)
    {
        $_COOKIE[$key] = $value;
    }

    public static function asArray()
    {
        if (func_num_args()) {
            $data = array();
            foreach (func_get_args() as $key) {
                $data[$key] = self::get($key);
            }

            return $data;
        }

        return $_COOKIE;
    }

    public static function clear($key)
    {
        return self::set($key, '');
    }
}
