<?php

class NotEmpty implements IRule
{
    protected $min = null;
    protected $max = null;

    public function __construct()
    {
    }

    public function test($value)
    {
        if (empty($value)) {
            return false;
        }

        return true;
    }

    public function getMessage()
    {
        return Lang::get('Value_must_not_be_empty');
    }
}
