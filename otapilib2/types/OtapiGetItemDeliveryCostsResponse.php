<?php

class OtapiGetItemDeliveryCostsResponse extends BaseOtapiType{
    /**
     * @return OtapiDeliveryCostListAnswer
     */
    public function GetGetItemDeliveryCostsResult(){
        $value = isset($this->xmlData->GetItemDeliveryCostsResult) ? $this->xmlData->GetItemDeliveryCostsResult : false;
        return new OtapiDeliveryCostListAnswer($value);
    }
}