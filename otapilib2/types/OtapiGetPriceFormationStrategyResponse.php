<?php

class OtapiGetPriceFormationStrategyResponse extends BaseOtapiType{
    /**
     * @return OtapiStrategyInfoAnswer
     */
    public function GetGetPriceFormationStrategyResult(){
        $value = isset($this->xmlData->GetPriceFormationStrategyResult) ? $this->xmlData->GetPriceFormationStrategyResult : false;
        return new OtapiStrategyInfoAnswer($value);
    }
}