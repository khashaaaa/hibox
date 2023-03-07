<?php

class OtapiSetUserBanResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetSetUserBanResult(){
        $value = isset($this->xmlData->SetUserBanResult) ? $this->xmlData->SetUserBanResult : false;
        return new VoidOtapiAnswer($value);
    }
}