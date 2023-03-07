<?php
class ScriptIncluder {
    private static $jsScripts = array();
    
    public static function AddCustomScript($scriptName){
        self::$jsScripts[] = $scriptName;
    }

    public static function GetCustomScripts(){
        return self::$jsScripts;
    }
}