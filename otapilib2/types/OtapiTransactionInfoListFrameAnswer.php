<?php

class OtapiTransactionInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return DataSubListOfOtapiTransactionInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataSubListOfOtapiTransactionInfo($value);
    }
}