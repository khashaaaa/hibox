<?php

class OtapiGetPriceFormationGroupListResponse extends BaseOtapiType{
    /**
     * @return OtapiPriceFormationGroupInfoListAnswer
     */
    public function GetGetPriceFormationGroupListResult(){
        $value = isset($this->xmlData->GetPriceFormationGroupListResult) ? $this->xmlData->GetPriceFormationGroupListResult : false;
        return new OtapiPriceFormationGroupInfoListAnswer($value);
    }
}