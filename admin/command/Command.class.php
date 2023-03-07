<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dima
 * Date: 06.05.13
 * Time: 7:25
 * To change this template use File | Settings | File Templates.
 */

class Command {
    public static function Read ($message) {
        print $message;

        $fp=fopen("php://stdin", "r");
        $in=fgets($fp,4094);
        fclose($fp);

        (PHP_OS == "WINNT") ? ($read = str_replace("\r\n", "", $in)) : ($read = str_replace("\n", "", $in));

        return $read;
    }
}