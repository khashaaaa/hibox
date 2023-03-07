<?php

class OtapiAddPricesResponse extends BaseOtapiType{
    /**
     * @return OtapiBasePriceAnswer
     */
    public function GetAddPricesResult(){
        $value = isset($this->xmlData->AddPricesResult) ? $this->xmlData->AddPricesResult : false;
        return new OtapiBasePriceAnswer($value);
    }
}