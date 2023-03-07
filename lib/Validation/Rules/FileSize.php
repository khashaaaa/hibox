<?php

class FileSize implements IRule
{
    protected $size;
    protected $func;
    
    public function __construct($size, $func = null)
    {
        $this->size = $size;
        $this->func = is_callable($func) ? $func : null;
    }
    
    public function test($value)
    {
        if (is_callable($this->func))
            return call_user_func($this->func, filesize($value), $this->size);
        else
            return $this->defaultTest(filesize($value), $this->size);
    }

    function defaultTest ($real, $user) {
        return $real > $user;
    }

    public function getMessage()
    {
        return Lang::get('Incorrect_filesize');
    }
}