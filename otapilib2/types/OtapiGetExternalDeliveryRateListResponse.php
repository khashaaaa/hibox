<?php

class OtapiGetExternalDeliveryRateListResponse extends BaseOtapiType{
    /**
     * @return OtapiExternalDeliveryRateListAnswer
     */
    public function GetGetExternalDeliveryRateListResult(){
        $value = isset($this->xmlData->GetExternalDeliveryRateListResult) ? $this->xmlData->GetExternalDeliveryRateListResult : false;
        return new OtapiExternalDeliveryRateListAnswer($value);
    }
}