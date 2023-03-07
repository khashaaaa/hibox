<?php

class DateISO extends Regexp
{
    protected $regexp;

    public function __construct()
    {
        //rule from jquery.validation
        $this->regexp = '/^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/';
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
