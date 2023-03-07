<?php

class OtapiGetPriceFormationGroupResponse extends BaseOtapiType{
    /**
     * @return OtapiPriceFormationGroupInfoAnswer
     */
    public function GetGetPriceFormationGroupResult(){
        $value = isset($this->xmlData->GetPriceFormationGroupResult) ? $this->xmlData->GetPriceFormationGroupResult : false;
        return new OtapiPriceFormationGroupInfoAnswer($value);
    }
}