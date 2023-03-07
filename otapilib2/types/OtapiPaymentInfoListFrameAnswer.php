<?php

class OtapiPaymentInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return DataSubListOfOtapiPaymentInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataSubListOfOtapiPaymentInfo($value);
    }
}