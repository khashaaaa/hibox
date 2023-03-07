<?php

OTBase::import('system.lib.cache.Key');
OTBase::import('system.lib.cache.adapter.*');

class Cache
{
    private $defaultAdapter = 'SessionAdapter';

    private $adapter;
    private $key;

    public function __construct($key, $ttl = null)
    {
        $this->key = $this->key($key);
    }

    public function drop($key = null)
    {
        $this->getAdapter()->drop($this->key($key));
    }

    public function set($data, $key = null, $ttl = null)
    {
        $this->getAdapter()->set($data, $this->key($key), $ttl);
    }

    public function setAdapter($adapter)
    {
        if (! ($adapter instanceof CacheAdapterInterface)) {
            throw new Exception('Invalid cache adapter given. Cache adapter must implement CacheAdapterInterface.');
        }
        $this->adapter = $adapter;
    }

    public function get($key = null)
    {
        return $this->getAdapter()->get($this->key($key));
    }

    public function getAdapter()
    {
        if (is_null($this->adapter)) {
            $adapterClass = $this->defaultAdapter;
            $this->adapter = new $adapterClass();
        }
        return $this->adapter;
    }

    public function has($key = null)
    {
        return $this->getAdapter()->has($this->key($key));
    }

    protected function key($key = null)
    {
        if (is_null($this->key) && $key) {
            if (! ($key instanceof Key)) {
                $key = new Key($key);
            }
            $this->key = $key->value();
        }
        return $this->key;
    }
}
