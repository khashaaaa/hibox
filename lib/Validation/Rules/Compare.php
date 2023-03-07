<?php

/**
 * Проверка совпадения значения
 */

class Compare implements IRule
{
    protected $value;

    /**
     * false - сравниваем значения на равенство
     * true - сравниваем значения на неравенство
     * @var bool
     */
    protected $not_eq;

    public function __construct($value, $not_eq = false)
    {
        $this->value = $value;
        $this->not_eq = (bool)$not_eq;
    }

    public function test($value)
    {
        if ($this->not_eq) {
            $result = (string) $value !== (string) $this->value;
        } else {
            $result = (string) $value === (string) $this->value;
        }

        return $result;
    }

    public function getMessage()
    {
        if ($this->not_eq) {
            $result = Lang::get('Values_match');
        } else {
            $result = Lang::get('Values_dont_match');
        }

        return $result;
    }
}
