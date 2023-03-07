<?php

/**
 * Правило NotEmptyString для валидатора.
**/
class NotEmptyString implements IRule
{
    protected $message;

    public function test($value)
    {
        $regexp = new Regexp('/^(\s|&nbsp;)*$/isu');
        return is_string($value) && !$regexp->test($value);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
