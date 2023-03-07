<?php
OTBase::import('system.admin.lib.RightsManager');

class timer {

    protected $start_time = 0;
    protected $text ='';
    protected static $debug = array();

    const MYSQL_QUERY_TYPE = 'MYSQL_QUERY';

    const ROUNDING_2 = 2;
    const ROUNDING_4 = 4;
    const ROUNDING_8 = 8;

    static public function getInstance()
    {
        static $obj_server;
        if (!isset($obj_server)){
            $obj_server = new self();
        }
        return $obj_server;
    }

    protected function count()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    static function isMysqlQuery ($text) {
        return (strpos($text, self::MYSQL_QUERY_TYPE) !== false);
    }

    protected function getRoundingValue () {
        return self::isMysqlQuery($this->text) ? self::ROUNDING_4 : self::ROUNDING_2;
    }

    public function start($text = '', $params = array(), $backtrace = true)
    {
        global $otapilib;
        $serviceUrl = defined('CFG_SERVICE_URL') ? CFG_SERVICE_URL : $otapilib->_server;

        $params = ! is_array($params) ? array() : $params;
        $params['instanceKey'] = $serviceUrl=='http://dev.otapi.net/OtapiWebService2.asmx/' || (Session::get('sid') && RightsManager::isSuperAdmin()) ? CFG_SERVICE_INSTANCEKEY : '';
        $urlParams = $params;
        unset($params['instanceKey']);

        $url = $serviceUrl . preg_replace('#(MultiRequest:\s*)?OTAPIlib[0-9]*\-\>#si', '', $text);
        $urlParams = array_map(function ($a) {
            return preg_replace("#<\?xml[^>]*>|\n#si", "", $a);
        }, $urlParams);
        $url = $url . '?' . http_build_query($urlParams);
        $this->text = $text; 
        if (! $this->isMysqlQuery($this->text)) {
            $this->text .= ' (<a target="_blank" href="'.$url.'">'.Lang::get('link').'</a>) '; 
        }
        $this->text .= ' '.Lang::get('Parameters').': '.htmlspecialchars(json_encode($params));

        if ($backtrace && ! self::$debug) {
            $debug = debug_backtrace();
            if (! empty($debug[2])) {
                self::$debug[] = $debug[2];
                unset($debug);
            }
        }

        $this->start_time = $this->count();
    }

    public function end($text = '', $mcount = null)
    {
        $run_time = $this->count() - $this->start_time;
        $this->log($run_time, $text, $mcount);
        return $run_time;
    }

    public function addMultiCurlQueueDebug($key, array $debug)
    {
        self::$debug[$key] = $debug;
    }

    protected function log($run_time, $text = '', $mcount = null)
    {
        $someContent = '['.date('Y-m-d H:i:s').'] — <!--time-->'.round($run_time, $this->getRoundingValue()).'<!--/time--> secs. ' . $this->text;
        if (strpos($this->text, 'OTAPIlib->MultiRequest') === false) {
            if (! is_null($mcount) && ! empty(self::$debug[$mcount])) {
                $debug = self::$debug[$mcount];
                unset(self::$debug[$mcount]);
            } else {
                $debug = array_shift(self::$debug);
            }
            if (!empty($debug) && !empty($debug['file']) && !empty($debug['line'])) {
                $someContent .= '<br/>Called in file <b>'.$debug['file'].'</b> at line <b>'.$debug['line'].'</b>.';
            }
        }
        $someContent .= "\n";
        $GLOBALS['trace'][(string)$run_time.rand(100000, 999999)] = $someContent.' '.$text;
    }

    static function logXML($method, $params, $text)
    {
        $GLOBALS['traceXML'][] = array(
            'method' => $method,
            'info'    => $text,
            'params'  => $params
        );
    }

    public function endBlockLog($text = '')
    {
        //
    }
}
