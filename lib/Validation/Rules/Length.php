<?php

class Length implements IRule
{
    protected $length = null;
    protected $max = null;

    public function __construct($length, $max = null)
    {
        $this->length = $length;
        $this->max = $max;
    }

    public function test($value)
    {
        if (empty($value)) {
            return false;
        }

        if ($this->max) {
            if (is_string($value) && mb_strlen($value) > $this->length && mb_strlen($value) <= $this->max) {
                return true;
            } else if (is_array($value) && count($value) > $this->length && count($value) <= $this->max) {
                return true;
            }
        } else {
            if (is_string($value) && mb_strlen($value) == $this->length) {
                return true;
            } else if (is_array($value) && count($value) == $this->length) {
                return true;
            }
        }

        return true;
    }

    public function getMessage()
    {
        return Lang::get('Value_must_not_be_empty');
    }
}
