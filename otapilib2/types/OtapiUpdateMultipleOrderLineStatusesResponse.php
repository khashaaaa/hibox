<?php

class OtapiUpdateMultipleOrderLineStatusesResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateMultipleOrderLineStatusesResult(){
        $value = isset($this->xmlData->UpdateMultipleOrderLineStatusesResult) ? $this->xmlData->UpdateMultipleOrderLineStatusesResult : false;
        return new VoidOtapiAnswer($value);
    }
}