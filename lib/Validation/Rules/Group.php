<?php

class Group implements IRule
{
    const REQUIRED = 1;
    const EVERYONE = 2;
    const NOTEMPTY = 3;

    protected $rules = array();
    protected $message;
    protected $match = self::REQUIRED;

    public function __construct($match = self::REQUIRED)
    {
        $this->match = $match;
    }

    public function add($rule)
    {
        $this->rules[] = $rule;
        return $this;
    }

    public function test($value)
    {
        if (empty($this->rules)) {
            throw new Exception("No rules are specified");
        }

        if ($this->match == self::NOTEMPTY && !$value) {
            return true;
        }

        $totalResult = false;
        switch ($this->match) {
            case self::NOTEMPTY:
            case self::REQUIRED:
                $totalResult = true;
        }

        foreach($this->rules as $rule) {
            /** @var IRule $rule  */
            $result =  $rule->test($value);
            switch($this->match) {
                case self::NOTEMPTY:
                case self::REQUIRED:
                    if(!$result) {
                        return false;
                    }
                    break;
                case self::EVERYONE:
                    if($result) {
                        return true;
                    }
                    break;
            }
        }

        return $totalResult;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
