<?php
require_once CFG_APP_ROOT . '/logs/Log.class.php';

class LogController {
    /**
     * @var Log
     */
    static $log;

    public static function onPageStartLoad(){
        self::$log = new Log();
        self::$log->Start();
    }

    public static function onLogMethodCall($methodCallInfo){
        try{
            if(self::$log)
                self::$log->AddMethod($methodCallInfo);
        }
        catch(Exception $e){

        }
    }

    public static function onPageCompleteLoad(){
        self::$log->Stop();
        self::$log->Write();
        if(self::$log->Size() > 100){
            self::$log->Release();
        }
    }
}