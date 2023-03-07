<?php

class OtapiGetSalesOperatorInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiOperatorInfoListAnswer
     */
    public function GetGetSalesOperatorInfoListResult(){
        $value = isset($this->xmlData->GetSalesOperatorInfoListResult) ? $this->xmlData->GetSalesOperatorInfoListResult : false;
        return new OtapiOperatorInfoListAnswer($value);
    }
}