<?php

class IsDateTime implements IRule {
    protected $message;

    public function test($value)
    {
        if (!($value instanceof DateTime)) {
            $this->message = Lang::get('Value_is_not_date_object');
            return false;
        } else {
            return true;
        }
    }

    public function getMessage()
    {
        return $this->message;
    }
}
