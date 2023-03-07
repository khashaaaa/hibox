<?php
/**
 * Правило FloatValidation для валидатора.
**/
class FloatValidation implements IRule
{
    protected $message;

    public function test($value)
    {
        if (! is_numeric($value) || !($value == floatval($value))) {
            $this->message = Lang::get('Value_is_not_float');
        }

        return is_null($this->message);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
