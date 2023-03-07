<?php

OTBase::import('system.lib.service.ServiceRecord');

class PackageRecord extends ServiceRecord
{
    private $preparedAddressParts;
    private $preparedDeliveryAddress;
    private $preparedShipmentDate;
    private $recipientName;

    public function __construct($data)
    {
        $this->addConverters(array(
            'creationdate' => function ($a) {
                return str_replace("T", " ", $a);
            },
        ));
        parent::__construct($data);
    }

    public function getDeliveryAddress($withRecipientName = true, $asArray = false, $withCountry = true)
    {
        if (is_null($this->preparedAddressParts) || $asArray) {
            $this->preparedAddressParts = array();
            if ($withRecipientName) {
                $this->preparedAddressParts[] = $this->getRecipientName();
            }
            if (! empty($this->deliverycountry) && $withCountry) {
                $this->preparedAddressParts[] = $this->deliverycountry;
            }
            if (! empty($this->deliverypostalcode) && $this->deliverypostalcode != '000000') {
                $this->preparedAddressParts[] = $this->deliverypostalcode;
            }
            if (! empty($this->deliveryregionname)) {
                $this->preparedAddressParts[] = $this->deliveryregionname;
            }
            if (! empty($this->deliverycity)) {
                $this->preparedAddressParts[] = $this->deliverycity;
            }
            if (! empty($this->deliveryaddress)) {
                $this->preparedAddressParts[] = $this->deliveryaddress;
            }
        }
        if (is_null($this->preparedDeliveryAddress)) {
            $this->preparedDeliveryAddress = implode(', ', $this->preparedAddressParts);
        }
        if ($asArray) {
            return $this->preparedAddressParts;
        }
        return $this->preparedDeliveryAddress;
    }

    public function getRecipientName()
    {
        if (is_null($this->recipientName)) {
            $this->recipientName = implode(' ', array_filter(array_map('trim', array(
                $this->deliverycontactlastname,
                $this->deliverycontactfirstname,
                $this->deliverycontactmiddlename,
            ))));
        }
        return $this->recipientName;
    }

    public function getShipmentDate()
    {
        if (is_null($this->preparedShipmentDate)) {
            if (! empty($this->shipmentdate) && strtotime($this->shipmentdate)) {
                $this->preparedShipmentDate = date('d.m.Y', strtotime($this->shipmentdate));
            } else {
                $this->preparedShipmentDate = '';
            }
        }
        return $this->preparedShipmentDate;
    }

    public function asArray()
    {
        return array_merge(parent::asArray(), array(
            'preparedDeliveryAddress'   => $this->getDeliveryAddress(false),
            'preparedShipmentDate'      => $this->getShipmentDate(),
            'recipientName'             => $this->getRecipientName(),
        ));
    }
}
