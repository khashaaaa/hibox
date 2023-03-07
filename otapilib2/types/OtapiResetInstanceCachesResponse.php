<?php

class OtapiResetInstanceCachesResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetResetInstanceCachesResult(){
        $value = isset($this->xmlData->ResetInstanceCachesResult) ? $this->xmlData->ResetInstanceCachesResult : false;
        return new VoidOtapiAnswer($value);
    }
}