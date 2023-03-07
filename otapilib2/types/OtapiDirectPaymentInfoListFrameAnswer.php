<?php

class OtapiDirectPaymentInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataSubListOfDirectPaymentInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataSubListOfDirectPaymentInfo($value);
    }
}