<?php

class GetOtapiCallStatisticResponse extends BaseOtapiType{
    /**
     * @return OtapiCallStatisticAnswer
     */
    public function GetGetOtapiCallStatisticResult(){
        $value = isset($this->xmlData->GetOtapiCallStatisticResult) ? $this->xmlData->GetOtapiCallStatisticResult : false;
        return new OtapiCallStatisticAnswer($value);
    }
}