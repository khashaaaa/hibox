<?php

class OtapiGetPriceFormationStrategyListResponse extends BaseOtapiType{
    /**
     * @return OtapiStrategyInfoListAnswer
     */
    public function GetGetPriceFormationStrategyListResult(){
        $value = isset($this->xmlData->GetPriceFormationStrategyListResult) ? $this->xmlData->GetPriceFormationStrategyListResult : false;
        return new OtapiStrategyInfoListAnswer($value);
    }
}