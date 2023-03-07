<?php

class ValidationError
{
    public $message;
    public $key;
    public $value;

    public function __construct($message, $key, $value = null)
    {
        $this->message = $message;
        $this->key = $key;
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->message;
    }
}