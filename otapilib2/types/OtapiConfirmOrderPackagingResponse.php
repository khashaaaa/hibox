<?php

class OtapiConfirmOrderPackagingResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetConfirmOrderPackagingResult(){
        $value = isset($this->xmlData->ConfirmOrderPackagingResult) ? $this->xmlData->ConfirmOrderPackagingResult : false;
        return new VoidOtapiAnswer($value);
    }
}