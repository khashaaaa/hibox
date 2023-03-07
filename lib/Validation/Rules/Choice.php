<?php

/**
 * Проверка принадлежности введенного значения предопределенному списку
 */

class Choice implements IRule
{
    protected $value;

    public function __construct($values)
    {
        $this->values = (array) $values;
    }

    public function test($value)
    {
        foreach($this->values as $v) {
            if ((string) $value === (string) $v) {
                return true;
            }
        }

        return false;
    }

    public function getMessage()
    {
        return Lang::get('Value_is_out_of_range');
    }
}
