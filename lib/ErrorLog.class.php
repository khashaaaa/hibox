<?php

class ErrorLog {

    public static function WriteErrorLog($method,$params,$message, $code){
        $f = fopen(dirname(dirname(__FILE__)).'/logs/errorLog.dat', 'a+');
        if (is_writable(dirname(dirname(__FILE__)).'/logs/errorLog.dat')){
            $data = array(
                $code,
                (string)$message,
                $method,
                $params
            );
            fwrite($f, json_encode($data)."\n");
        }
        fclose($f);

    }

}
