<?php

class OtapiGetDeliveryCountryInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiCountryInfoListAnswer
     */
    public function GetGetDeliveryCountryInfoListResult(){
        $value = isset($this->xmlData->GetDeliveryCountryInfoListResult) ? $this->xmlData->GetDeliveryCountryInfoListResult : false;
        return new OtapiCountryInfoListAnswer($value);
    }
}