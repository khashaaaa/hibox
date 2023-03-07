<?php

class OtapiGetItemTotalCostResponse extends BaseOtapiType{
    /**
     * @return OtapiPriceAnswer
     */
    public function GetGetItemTotalCostResult(){
        $value = isset($this->xmlData->GetItemTotalCostResult) ? $this->xmlData->GetItemTotalCostResult : false;
        return new OtapiPriceAnswer($value);
    }
}