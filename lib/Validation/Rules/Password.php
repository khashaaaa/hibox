<?php

/**
 * Проверка совпадения значения
 */

class Password implements IRule
{
    protected $confirm;
    protected $regex;
    protected $message;
    
    public function __construct($confirm, $regex = null)
    {
        $this->confirm = $confirm;
        $this->regex = $regex;
    }

    public function test($value)
    {
        if(!trim($value)) {
            $this->message = Lang::get('Password_cannot_be_empty');
            return false;
        }
        
        if(!is_null($this->regex) && !preg_match($this->regex, $value)) {
            $this->message = Lang::get('Incorrect_password_format');
            return false;
        }
        
        if((string) $value !== (string) $this->confirm) {
            $this->message = Lang::get('Passwords_dont_match');
            return false;
        }
        
        return true;
    }

    public function getMessage()
    {
        return $this->message;
    }
}