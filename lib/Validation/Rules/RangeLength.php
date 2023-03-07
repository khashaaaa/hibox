<?php

/**
 * Проверка принадлежности диапазону значений длины строки
 */
class RangeLength implements IRule
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
            if (mb_strlen($value) < $this->min) {
                return false;
            }
        }

        if (!is_null($this->max)) {
            if (mb_strlen($value) > $this->max) {
                return false;
            }
        }

        return true;
    }

    public function getMessage()
    {
        return Lang::get('String_length_must_be_from_to') . (is_null($this->min)?'':' '.Lang::get('from').' ' . $this->min) . (is_null($this->max)?'':' '.Lang::get('to').' ' . $this->max);
    }
}