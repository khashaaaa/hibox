<?php

class ServiceRecord extends ArrayObject
{
    protected $converters = array();
    protected $rawData = array();
    protected $isEmpty = false;

    // public //

    public function __construct($rawData, $storeRawData = false)
    {
        if (! is_array($rawData) || empty($rawData)) {
            $this->isEmpty = true;
        } else {
            $this->rawData = $rawData;
        }

        foreach ($this->rawData as $key => $value) {
            if (is_array($value)) {
                $value = new self($value);
            }
            $key = strtolower($key);
            if (! empty($this->converters[$key])) {
                $value = $this->converters[$key]($value);
            }
            $this->offsetSet($key, $value);
        }

        if (! $storeRawData) {
            $this->rawData = array();
        }
    }

    public function __get($attribute)
    {
        return $this->offsetGet($attribute);
    }

    public function __isset($attribute)
    {
        return $this->offsetExists($attribute);
    }

    public function __set($attribute, $value)
    {
        $this->offsetSet($attribute, $value);
    }

    public function __unset($attribute)
    {
        return $this->offsetUnset($attribute);
    }

    public function offsetExists($attribute)
    {
        return parent::offsetExists(strtolower($attribute));
    }

    public function offsetGet($attribute)
    {
        return parent::offsetGet(strtolower($attribute));
    }

    public function offsetSet($attribute, $value)
    {
        parent::offsetSet(strtolower($attribute), $value);
    }

    public function offsetUnset($attribute)
    {
        parent::offsetUnset(strtolower($attribute));
    }

    public function asArray()
    {
        return array_merge((array)$this, $this->rawData);
    }

    public function isEmpty($rawDataOnly = true)
    {
        return $rawDataOnly ? $this->isEmpty : ! count((array)$this);
    }

    // protected //

    protected function addConverters(array $converters)
    {
        $this->converters = array_merge($this->converters, $converters);
    }
}
