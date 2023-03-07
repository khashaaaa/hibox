<?php

class DateRus extends Regexp
{
    protected $regexp;

    public function __construct()
    {
        //rule from jquery.validation
        $this->regexp = '/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}$/';
    }

    public function test($value)
    {
        return (bool)preg_match($this->regexp, $value);
    }

    public function getMessage()
    {
        return Lang::get('Incorrect_date');
    }
}
