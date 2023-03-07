<?php

class OtapiGetDeliveryServiceSystemInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiDeliveryServiceSystemInfoListAnswer
     */
    public function GetGetDeliveryServiceSystemInfoListResult(){
        $value = isset($this->xmlData->GetDeliveryServiceSystemInfoListResult) ? $this->xmlData->GetDeliveryServiceSystemInfoListResult : false;
        return new OtapiDeliveryServiceSystemInfoListAnswer($value);
    }
}