<?php

class Curl
{
    protected $_header = array(
        "Cache-Control: max-age=0",
        "Connection: keep-alive",
        "Keep-Alive: 300",
        "Accept-Charset: utf-8;q=0.7,*;q=0.7",
        "Accept-Language: en-us,en;q=0.5"
    );
    protected $_useragent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1';
    protected $_url;
    protected $_followlocation;
    protected $_timeout;
    protected $_maxRedirects;
    protected $_cookieFileLocation = './cookie.txt';
    protected $_post;
    protected $_postFields;
    protected $_referer;
    protected $_session;
    protected $_webpage;
    protected $_includeHeader;
    protected $_noBody;
    protected $_status;
    protected $_proxy;
    protected $_proxyauth;
    protected $_info = array();
    protected $_binaryTransfer;
    public $authentication = 0;
    public $auth_name = '';
    public $auth_pass = '';
    // cURL resource
    public $_resource = null;
    public $_error = 0;

    public function __construct($url, $timeOut = 60, $followlocation = false, $maxRedirecs = 10, $binaryTransfer = false, $includeHeader = false, $noBody = false)
    {
        $this->_url = $url;
        $this->_followlocation = $followlocation;
        $this->_timeout = $timeOut;
        $this->_maxRedirects = $maxRedirecs;
        $this->_noBody = $noBody;
        $this->_includeHeader = $includeHeader;
        $this->_binaryTransfer = $binaryTransfer;

        $additionalHeader = array();
        if (RequestWrapper::getClientIp()) {
            $additionalHeader[] = 'X-OT-User-IP: ' . RequestWrapper::getClientIp();
        }
        if (isset($_SERVER["REMOTE_ADDR"])) {
            $additionalHeader[] = 'X-OT-ip: ' . $_SERVER["REMOTE_ADDR"];
        }
        $this->setHeader($additionalHeader);
        //$this->_cookieFileLocation = dirname(__FILE__).'/cookie.txt';
    }

    public function useAuth($use)
    {
        $this->authentication = 0;
        $this->authentication = $use == true ? 1 : 0;
        return $this;
    }

    public function setName($name)
    {
        $this->auth_name = $name;
        return $this;
    }

    public function setPass($pass)
    {
        $this->auth_pass = $pass;
        return $this;
    }

    /**
     * Установка хэдэров
     *
     * @param array $headers - массив хэдэров
     * @param boolen $rewrite - перезаписывает значение хэдэров
     */
    public function setHeader(array $headers = array(), $rewrite = false)
    {
        $this->_header = $rewrite ? $headers : array_merge($this->_header, $headers);
        return $this;
    }

    public function setReferer($referer)
    {
        $this->_referer = $referer;
        return $this;
    }

    public function setCookiFileLocation($path)
    {
        $this->_cookieFileLocation = $path;
        return $this;
    }

    /**
     * Установка пост данных
     *
     * @param array or string $postFields - данные
     * @param boolean $as_json - конвертить в json
     */
    public function setPost($postFields, $as_json = true)
    {
        if (is_array($postFields)) {
            if ($as_json)
                $postFields = array('data' => json_encode($postFields));
            $postFields = http_build_query($postFields);
        }

        $this->_post = true;
        $this->_postFields = $postFields;
        return $this;
    }

    public function setUserAgent($userAgent)
    {
        $this->_useragent = $userAgent;
        return $this;
    }

    public function setProxy($proxy, $auth)
    {
        $this->_proxy = $proxy;
        $this->_proxyauth = $auth;
    }

    public function init()
    {
        $this->_resource = curl_init();

        curl_setopt($this->_resource, CURLOPT_URL, $this->_url);
        curl_setopt($this->_resource, CURLOPT_HTTPHEADER, $this->_header);
        curl_setopt($this->_resource, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($this->_resource, CURLOPT_CONNECTTIMEOUT, $this->_timeout);
        curl_setopt($this->_resource, CURLOPT_MAXREDIRS, $this->_maxRedirects);
        curl_setopt($this->_resource, CURLOPT_RETURNTRANSFER, true);

        if (function_exists('curl_version')) {
            $curlInfo = curl_version();
            if (! empty($curlInfo['libz_version'])) {
                curl_setopt($this->_resource, CURLOPT_ENCODING, 'gzip,deflate');
            }
        }


        if (!ini_get('safe_mode') && !ini_get('open_basedir') && !defined('CFG_NO_CURLOPT_FOLLOWLOCATION')) {
            curl_setopt($this->_resource, CURLOPT_FOLLOWLOCATION, $this->_followlocation);
        }
        //curl_setopt($this->_resource, CURLOPT_COOKIEJAR,$this->_cookieFileLocation);
        //curl_setopt($this->_resource, CURLOPT_COOKIEFILE,$this->_cookieFileLocation);
        // Игнор SSL верификации
        curl_setopt($this->_resource, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($this->_resource, CURLOPT_SSL_VERIFYHOST, 2);

        if ($this->authentication == 1) {
            curl_setopt($this->_resource, CURLOPT_USERPWD, $this->auth_name . ':' . $this->auth_pass);
        }

        if ($this->_post) {
            curl_setopt($this->_resource, CURLOPT_POST, true);
            curl_setopt($this->_resource, CURLOPT_POSTFIELDS, $this->_postFields);
        }

        if ($this->_includeHeader) {
            curl_setopt($this->_resource, CURLOPT_HEADER, true);
        }

        if ($this->_noBody) {
            curl_setopt($this->_resource, CURLOPT_NOBODY, true);
        }

        if ($this->_proxy) {
            curl_setopt($this->_resource, CURLOPT_PROXY, $this->_proxy);
            if ($this->_proxyauth) {
                curl_setopt($this->_resource, CURLOPT_PROXYUSERPWD, $this->_proxyauth);
            }
        } else {
            curl_setopt($this->_resource, CURLOPT_PROXY, '');
            curl_setopt($this->_resource, CURLOPT_PROXYUSERPWD, '');
        }

        /*
          if($this->_binary)
          {
          curl_setopt($this->_resource, CURLOPT_BINARYTRANSFER,true);
          }
         */

        curl_setopt($this->_resource, CURLOPT_USERAGENT, $this->_useragent);
        curl_setopt($this->_resource, CURLOPT_REFERER, $this->_referer);

        return $this->_resource;
    }

    public function connect($url = '')
    {
        if ($url != '') {
            $this->_url = $url;
        }

        if (!function_exists('curl_init')) {
            //new Lib_Errors_Exception_Php('undefined function curl_init', 0, __FILE__, __LINE__);
            die('CURL not installed');
            return false;
        }

        $this->init();

        $this->_webpage = curl_exec($this->_resource);
        $this->_status = curl_getinfo($this->_resource, CURLINFO_HTTP_CODE);
        $this->_info = curl_getinfo($this->_resource);
        $this->_error = curl_error($this->_resource);

        curl_close($this->_resource);

        return $this->_webpage === false ? false : true;
    }

    public function setInfo()
    {
        $this->_info = curl_getinfo($this->_resource);
        return $this;
    }

    public function setHttpStatus()
    {
        $this->_status = curl_getinfo($this->_resource, CURLINFO_HTTP_CODE);
        return $this;
    }

    public function setSource($source)
    {
        $this->_webpage = $source;
        return $this;
    }

    /**
     * Get information regarding a specific transfer
     * @return array
     */
    public function getInfo()
    {
        return $this->_info;
    }

    public function getWebPage()
    {
        return $this->_webpage;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function getHttpStatus()
    {
        return $this->_status;
    }

    public function __tostring()
    {
        return $this->_status . ' ' . $this->_webpage;
    }

    public function getResource()
    {
        return $this->_resource;
    }
}
