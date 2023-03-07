<?php

interface CacheAdapterInterface
{
    public function drop($key);
    public function get($key);
    public function has($key);
    public function set($data, $key, $ttl);
}
