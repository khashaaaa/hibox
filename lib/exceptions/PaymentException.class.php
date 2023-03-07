<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dima
 * Date: 04.12.12
 * Time: 19:42
 * To change this template use File | Settings | File Templates.
 */
class PaymentException extends Exception
{
    public function __construct($message = "", $params = array(), $code = 0, Exception $previous = null){
        parent::__construct($message, $code, $previous);
    }
}
