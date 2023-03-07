<?php
/**
 * Правило Int для валидатора.
**/
class IntValidation implements IRule
{
    protected $message;

    public function test($value)
    {
        if (! is_numeric($value) || !($value == intval($value))) {
            $this->message = Lang::get('Value_is_not_int');
        }

        return is_null($this->message);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
