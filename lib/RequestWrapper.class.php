<?php

class RequestWrapper
{
    /**
     * @var Метод запроса POST|GET
     */
    private $method;

    /**
     * @var Хранит в себе сам запрос
     */
    private $request;

    private $referrer;

    public function __construct($predefinedRequest = array()){
        $this->method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $this->request = $predefinedRequest ? $predefinedRequest :
            ($this->method == 'POST' ? $_POST : $_GET);
        $this->referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }

    public static function path($qs = false)
    {
        return $qs ? self::env('REQUEST_URI') : preg_replace('/\?.*$/', '', self::env('REQUEST_URI'));
    }

    public static function getUriPart($index)
    {
        $path = trim(self::path(), '/ ');
        $parts = $path !== "" ? explode('/', $path) : array();
        return array_key_exists($index, $parts) ? $parts[$index] : null;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function isPost()
    {
        return ($this->getMethod() == 'POST') ? true : false;
    }

    /**
     * @param $key
     * @return bool|mixed
     */
    public function getValue($key, $default = null)
    {
        return array_key_exists($key, $this->request) ? $this->request[$key] : $default;
    }

    public function valueExists($key)
    {
        return array_key_exists($key, $this->request);
    }

    public static function getValueSafe($key){
        return isset($_GET[$key]) ? trim(htmlspecialchars(stripslashes(strip_tags($_GET[$key])))) : false;
    }
    
    public static function getRequestValueSafe($key){
        return isset($_REQUEST[$key]) ? trim(htmlspecialchars(stripslashes(strip_tags($_REQUEST[$key])))) : false;
    }

    public function escapeValue($value){
        return trim(htmlspecialchars(stripslashes(strip_tags($value))));
    }

    public function getAll(){
        return array_merge($_GET, $_POST);
    }

    public function delete($key){
        if($this->method == 'GET')
            unset($_GET[$key]);
        else
            unset($_POST[$key]);
    }

    public static function get($key, $default = null)
    {
        return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
    }

    public static function getParamExists($key)
    {
        return array_key_exists($key, $_GET);
    }

    public static function allPost()
    {
        return $_POST;
    }
    
    public static function post($key, $default = null)
    {
        return array_key_exists($key, $_POST) ? $_POST[$key] : $default;
    }

    public static function request($key, $default = null)
    {
        return array_key_exists($key, $_REQUEST) ? $_REQUEST[$key] : $default;
    }

    public static function env($key, $default = null)
    {
        return array_key_exists($key, $_SERVER) ? $_SERVER[$key] : $default;
    }

    public static function isAjax()
    {
        return strtolower(self::env('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest';
    }

    public static function LocationRedirect($url, $statusCode = 302) {
        header("Location: $url", true, $statusCode);
        die();
    }

    public function RedirectToReferrer(){
        header("Location: ".$this->referrer);
        die();
    }

    public function getReferrer(){
        return $this->referrer;
    }

    public function set($key, $value)
    {
        $this->request[$key] = $value;
        $_REQUEST[$key] = $value;
    }

    /**
     * @return string
     */
    public static function getProtocol()
    {
        if (isset($_SERVER['HTTP_SCHEME'])) {
            $scheme = $_SERVER['HTTP_SCHEME'];
        } elseif (
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            ($_SERVER['SERVER_PORT'] == 443)
        ) {
            $scheme ='https';
        } else {
            $scheme ='http';
        }

        return $scheme;
    }
    
    public static function getClientIp()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif(isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = false;
        }
        return $ipaddress;
    }
}