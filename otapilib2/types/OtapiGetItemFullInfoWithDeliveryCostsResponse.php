<?php

class OtapiGetItemFullInfoWithDeliveryCostsResponse extends BaseOtapiType{
    /**
     * @return OtapiItemFullInfoAnswer
     */
    public function GetGetItemFullInfoWithDeliveryCostsResult(){
        $value = isset($this->xmlData->GetItemFullInfoWithDeliveryCostsResult) ? $this->xmlData->GetItemFullInfoWithDeliveryCostsResult : false;
        return new OtapiItemFullInfoAnswer($value);
    }
}