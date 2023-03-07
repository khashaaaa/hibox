<?php

/**
 * Класс для итерирования по данным, полученных с сервисов
 * Class UnboundedElementsIterator
 */
class UnboundedElementsIterator implements Iterator  {
    /**
     * @var SimpleXMLElement[]
     */
    protected $xmlList;
    protected $elementsType;
    private $position;
    /**
     * @param SimpleXMLElement $xmlList
     * @param array $elementsType
     */
    public function __construct($xmlList, $elementsType){
        $this->xmlList = $xmlList;
        $this->elementsType = $elementsType;

        $this->position = 0;
    }


    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        $className = $this->elementsType['name'];
        if ($this->elementsType['type'] == 'scalarType') {
            return $this->elementsType['name'] == 'boolean' ? (string)$this->xmlList[$this->position] == 'true' : (string)$this->xmlList[$this->position];
        } else {
            return new $className($this->xmlList[$this->position]);
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->xmlList[$this->position]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    public function toArray(){
        $className = $this->elementsType['name'];
        $elements = array();
        foreach($this->xmlList as $element){
            if ($this->elementsType['type'] == 'scalarType') {
                $elements[] = $this->elementsType['name'] == 'boolean' ? (string)$element == 'true' : (string)$element;
            } else {
                $elements[] = new $className($element);
            }
        }
        return $elements;
    }
}