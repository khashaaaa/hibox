<?php

class OtapiEditOperatorInfoResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditOperatorInfoResult(){
        $value = isset($this->xmlData->EditOperatorInfoResult) ? $this->xmlData->EditOperatorInfoResult : false;
        return new VoidOtapiAnswer($value);
    }
}