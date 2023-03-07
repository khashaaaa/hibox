<?php
/**
 * Правило Translit для валидатора.
**/
class Translit implements IRule
{
    protected $message;

    public function test($value)
    {
        $regexp1 = new Regexp('/[а-яё ]/isu');
        $regexp2 = new Regexp('/^[a-z0-9_-]+$/isu');
        return !$regexp1->test($value) && $regexp2->test($value);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
