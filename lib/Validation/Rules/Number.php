<?php

/**
 * Правило Number для валидатора.
**/
class Number implements IRule
{
    protected $message;

    protected $min = null;
    protected $max = null;

    public function __construct($min = null, $max = null)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function test($value)
    {
        if (!is_null($this->min) || !is_null($this->max)) {
            $rangeRule = new Range($this->min, $this->max);
            $rangeResult = $rangeRule->test($value);
        }

        $result = is_numeric($value) &&
            (($value == intval($value)) || ($value == floatval($value)));

        return isset($rangeResult) ? $rangeResult && $result : $result;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
