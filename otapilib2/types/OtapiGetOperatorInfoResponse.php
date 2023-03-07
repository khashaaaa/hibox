<?php

class OtapiGetOperatorInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiOperatorInfoAnswer
     */
    public function GetGetOperatorInfoResult(){
        $value = isset($this->xmlData->GetOperatorInfoResult) ? $this->xmlData->GetOperatorInfoResult : false;
        return new OtapiOperatorInfoAnswer($value);
    }
}