<?php

OTBase::import('system.lib.service.ServiceRecord');

class OrderRecord extends ServiceRecord
{
    private $numericId;
    private $itemsGrouppedByStatus;
    private $itemsPackages = array();

    public function getNumericId()
    {
        if (is_null($this->numericId)) {
            if (preg_match('#[1-9]+#si', $this->id, $m, PREG_OFFSET_CAPTURE) && !empty($m[0]) && isset($m[0][1])) {
                $this->numericId = substr($this->id, $m[0][1]);
            } else {
                $this->numericId = $this->id;
            }
        }
        return $this->numericId;
    }

    public function getFormattedTotalAmount($withDelivery = false)
    {
        $totalAmount = $this->GoodsAmount;
        /*
        if ($this->items) {
            // TODO: костыль, убрать, когда сервисы сами начнут считать TotalAmount правильно
            $totalAmount = 0;
            foreach ($this->items as $item) {
                $itemPrice = (float)$item->newPriceCust ? $item->newPriceCust : $item->priceCust;
                $totalAmount += $itemPrice * $item->qty;
            }
        }*/
        if ($withDelivery) {
            $totalAmount = $this->TotalAmount;
        }
        return TextHelper::formatPrice($totalAmount, $this->currencysign);
    }

    public function getPaidAmount($format = true)
    {
        return TextHelper::formatPrice($this->totalamount - $this->remainamount, $this->currencysign);
    }

    public function getItemsGrouppedByStatus()
    {
        if (is_null($this->itemsGrouppedByStatus)) {
            $groupped = array();
            foreach ($this->items as $item) {
                if (! isset($groupped[$item->statusName])) {
                    $groupped[$item->statusName] = 0;
                }
                $groupped[$item->statusName]++;
            }
            $this->itemsGrouppedByStatus = $groupped;
        }
        return $this->itemsGrouppedByStatus;
    }

    public function getItemPackage($itemId)
    {
        if (is_null($this->itemsPackages[$itemId])) {
            $package = null;
            $this->itemsPackages[$itemId] = null;
            foreach ($this->packages as $package) {
                $item = array_filter(
                    (array)$package->items,
                    function ($a) {
                        return $a->orderLineId == '.$itemId.';
                    }
                );
                if (! empty($item)) {
                    $item = array_shift($item);
                    if ($item && $item->orderLineId) {
                        $this->itemsPackages[$itemId] = $package;
                    }
                    break;
                }
            }
        }
        return $this->itemsPackages[$itemId];
    }

    public function asArray()
    {
        return array_merge(parent::asArray(), array(
            'numericId'             => $this->getNumericId(),
            'formattedTotalAmount'  => $this->getFormattedTotalAmount(),
        ));
    }
}
