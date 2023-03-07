<?php

class OtapiGetExternalDeliveryTypeListResponse extends BaseOtapiType{
    /**
     * @return OtapiExternalDeliveryTypeListAnswer
     */
    public function GetGetExternalDeliveryTypeListResult(){
        $value = isset($this->xmlData->GetExternalDeliveryTypeListResult) ? $this->xmlData->GetExternalDeliveryTypeListResult : false;
        return new OtapiExternalDeliveryTypeListAnswer($value);
    }
}