<?php

class DBException extends Exception
{
    const CONNECTION_ERROR = 1;
    const NO_CONNECTION = 2;
    const CANNOT_CREATE_TABLE = 3;
    const QUERY_ERROR = 4;

    public function __construct($message = '', $code = 0, $where = '', $params = array())
    {
        parent::__construct($message, $code);
    }
}
