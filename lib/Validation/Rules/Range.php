<?php

/**
 * Проверка принадлежности диапазону значений
 */

class Range implements IRule
{
    protected $min = null;
    protected $max = null;

    public function __construct($min = null, $max = null)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function test($value)
    {
        if (!is_null($this->min)) {
            if ($value < $this->min) {
                return false;
            }
        }

        if (!is_null($this->max)) {
            if ($value > $this->max) {
                return false;
            }
        }

        return true;
    }

    public function getMessage()
    {
        return Lang::get('Value_must_be_from_to') .
               (is_null($this->min)?'':' '.Lang::get('from').' ' . $this->min) .
               (is_null($this->max)?'':' '.Lang::get('to').' ' . $this->max);
    }
}