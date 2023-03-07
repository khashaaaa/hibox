<?php

class Key
{
    private $key;
    private $ttl;

    public function __construct()
    {
        if (func_num_args()) {
            $args = array();
            foreach (func_get_args() as $arg) {
                $args[] = trim($arg, '/');
            }
            if (empty($args)) {
                throw new Exception('No arguments supplied for cache key.');
            }
            $this->key = implode('/', $args);
        } else {
            throw new Exception('No arguments supplied for cache key.');
        }
    }

    public function value()
    {
        return $this->key;
    }

    public function __toString()
    {
        return $this->value();
    }

    public function ttl($ttl = null)
    {
        if (! is_null($ttl)) {
            $this->ttl = $ttl;
        } else {
            return $this->ttl;
        }
    }
}

