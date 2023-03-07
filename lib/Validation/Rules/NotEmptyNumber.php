<?php

/**
 * Правило NotEmptyNumber для валидатора.
**/
class NotEmptyNumber extends Number
{
    protected $message;

    public function test($value)
    {
        return !empty($value) && parent::test($value);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
