<?php
/**
 * Правило DataTimeCompatible для валидатора.
**/
class DataTimeCompatible implements IRule
{
    protected $message;

    public function test($value)
    {
        try {
            new DateTime($value);
        } catch (Exception $e) {
            $this->message = Lang::get('Value_is_not_correct_date');
        }

        return is_null($this->message);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
