<?php

OTBase::import('system.lib.service.ServiceRecord');

class BasketRecord extends ServiceRecord
{
    private $providerItems = array();
    private $taoItems = array();
    private $taoItemsTotalCost;
    private $whItems = array();
    private $whItemsTotalCost;
    private $currentItems;

    public function __construct($data)
    {
        parent::__construct($data, true);
        if (! empty($this->elements)) {
            $data = $this->elements;
        } else {
            $data = $this;
        }
        foreach ($data as $key => $item) {
            $this->providerItems[$item['ProviderType']][] = $item;
        }
    }

    public function __call($method, $arguments)
    {
        if (preg_match('#get(.*)Items#', $method, $m)) {
            $method = str_replace($m[1], '', $method);
            if (! method_exists($this, $method)) {
                throw new Exception('Unknown method "' . $method . '" requested in class ' . __CLASS__);
            }
            $this->currentItems = $this->providerItems[$m[1]];
            return call_user_func_array(array($this, $method), $arguments);
        }
        throw new Exception('Unknown method "' . $method . '" requested in class ' . __CLASS__);
    }

    protected function getItems($offset = null, $limit = null)
    {
        if (!is_null($offset) || !is_null($limit)) {
            $items = array_slice($this->currentItems, (int)$offset, (int)$limit);
        } else {
            $items = $this->currentItems;
        }
        return $items;
    }

    protected function getItemsCount()
    {
        return count($this->currentItems);
    }

    protected function getItemsTotalCost()
    {
        $cost = 0;
        foreach ($this->currentItems as $item) {
            $cost += $item->cost;
        }
        return $cost;
    }

    public function count()
    {
        $count = 0;
        foreach ($this->providerItems as $items) {
           $count = $count + count($items);
        }
        return $count;
    }

    public function asArray()
    {
        return array_merge(parent::asArray(), array(
            'items' => $this->providerItems
        ));
    }

    public function getItemProviders()
    {
        $return = array();
        foreach ($this->providerItems as $name => $items) {
            if (! in_array($name, $return)) {
                $return[] = $name;
            }
        }
        return $return;
    }
}
