<?php

OTBase::import('system.admin.lib.RightsManager');


class Debugger
{
    public $type;
    public $startText;
    public $text;
    public $link;
    public $method;
    public $arguments;
    public $startTime;
    public $runTime;
    public $transferTime;
    public $callTime;
    public $additionalText;
    public $traceStr;

    private static $logs;

    const LOG_MYSQL_TYPE = 'LOG_MYSQL_TYPE';
    const LOG_OTAPILIB_TYPE = 'LOG_OTAPILIB_TYPE';
    const LOG_OTHER_TYPE = 'LOG_OTHER_TYPE';

    public function __construct() {}

    public static function getMemoryUsage()
    {
        $memoryUsageStr = '';

        if (isset($GLOBALS['memory_usage'])) {
            $memoryUsage = memory_get_usage() - $GLOBALS['memory_usage'];
            $memoryUsage = $memoryUsage / 1048576; /* 1024 * 1024 */
            $memoryUsageStr = (string) $memoryUsage;
        }

        return $memoryUsageStr;
    }

    /**
     * Создание счетчика таймера
     *
     * @param array $parameters параметры запроса, который логируем
     * @return $this объект immutable
     */
    public function start($logType = '', $parameters = [])
    {
        $type = ($logType === self::LOG_MYSQL_TYPE) ? self::LOG_MYSQL_TYPE : false;
        $type = ($logType === self::LOG_OTAPILIB_TYPE && !$type) ? self::LOG_OTAPILIB_TYPE : $type;
        $type = $type ? $type : self::LOG_OTHER_TYPE;

        $arguments = isset($parameters['arguments']) ? $parameters['arguments'] : [];
        unset($arguments['instanceKey']);
        $method = isset($parameters['method']) ? $parameters['method'] : '';
        $this->method = $method;

        $link = '';
        if ($type === self::LOG_OTAPILIB_TYPE) {
            /* если лог сервисный */

            $serviceUrl = OTAPILib2::getServerUrl();
            /* получаем instanceKey, если нужно */
            $instanceKey = Session::get('sid') && RightsManager::isSuperAdmin() ? CFG_SERVICE_INSTANCEKEY : '';

            /* добавляем instanceKey в массив аргументов и удаляем xml теги */
            $urlArguments = array_map(
                function ($a) {
                    return preg_replace("#<\?xml[^>]*>|\n#si", "", $a);
                },
                $arguments + ['instanceKey' => $instanceKey]
            );
            /* формируем url запрса в сервисы */
            $url = $serviceUrl . $method . '?' . http_build_query($urlArguments);

            /* ссылка для запроса в сервисы */
            $link = ' (<a target="_blank" href="' . $url . '">' . Lang::get('link') . '</a>) ';
        }

        /* сохраняем данные лога */
        $this->type = $type;
        $this->startText = isset($parameters['startText']) ? $parameters['startText'] : '';
        $this->link = $link;
        $this->startTime = $this->getTime();
        $this->arguments = $arguments ? htmlspecialchars(json_encode($arguments)) : '';
        $this->traceStr = $this->renderTraceStr();

        return $this;
    }

    public function end($text = '', $additionalInfo = [])
    {
        $this->text = $text;
        $this->runTime = $this->getTime() - $this->startTime;
        $this->callTime = date('Y-m-d H:i:s');

        if (isset($additionalInfo['realTime'])) {
            $this->transferTime = $this->runTime - $additionalInfo['realTime'];
        }

        if (isset($additionalInfo['additionalText'])) {
            $this->additionalText = $additionalInfo['additionalText'];
        }

        self::$logs[] = $this;

        return $this->runTime;
    }
    /**
     * @return Debugger[]
     */
    public static function getAll()
    {
        return self::$logs;
    }

    /**
     * @param string $type
     * @return Debugger[]
     */
    public static function getAllByType($type)
    {
        $logs = [];

        foreach (self::$logs as $log) {
            if ($log->type === $type) {
                $logs[] = $log;
            }
        }

        return $logs;
    }

    /**
     * @return string
     */
    public static function getRender()
    {
        $templateUniqId = md5(time() . rand(0, 99999));
        $templatePath = CFG_APP_ROOT . '/views';

        return General::viewFetch('main/debug', [
            'path' => $templatePath,
            'vars' => [
                'uniqId' => $templateUniqId,
                'logsAll' => self::getAll(),
                'logs' => [
                    self::LOG_OTAPILIB_TYPE => self::getAllByType(self::LOG_OTAPILIB_TYPE),
                    self::LOG_MYSQL_TYPE => self::getAllByType(self::LOG_MYSQL_TYPE),
                    self::LOG_OTHER_TYPE => self::getAllByType(self::LOG_OTHER_TYPE),
                ]
            ]
        ]);
    }

    /**
     * @param Debugger[] $logs
     * @return int
     */
    public static function calculateTime($logs)
    {
        $time = 0;

        foreach ($logs as $log) {
            $time += $log->runTime;
        }

        return $time;
    }

    /**
     * @return float время с микросекундами
     */
    private function getTime()
    {
        return microtime(true);
    }

    /**
     * @return string
     */
    private function renderTraceStr()
    {
        $traceArr = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        /* first element is always that class because method is private */
        array_shift($traceArr);

        $traceStr = '';
        foreach ($traceArr as $key => $call) {
            $traceStrCall = '';
            $traceStrCall .= isset($call['class']) ? $call['class'] : '';
            $traceStrCall .= isset($call['type']) ? $call['type'] : '';
            $traceStrCall .= isset($call['function']) ? $call['function'] : '';

            $traceStrFile = '';
            $traceStrFile .= isset($call['file']) ? $call['file'] : '';
            $traceStrFile .= isset($call['file']) && isset($call['line']) ? ':' : '';
            $traceStrFile .= isset($call['line']) ? $call['line'] : '';

            $traceStr .= '[' . $key . '] ';
            $traceStr .= $traceStrFile ? 'in ' . $traceStrFile . ' ' : '';
            $traceStr .= $traceStrCall ? 'call ' . $traceStrCall . '() ' : '';
            $traceStr .= '<br>';
        }

        return $traceStr;
    }
}