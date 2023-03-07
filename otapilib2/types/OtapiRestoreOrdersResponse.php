<?php

class OtapiRestoreOrdersResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRestoreOrdersResult(){
        $value = isset($this->xmlData->RestoreOrdersResult) ? $this->xmlData->RestoreOrdersResult : false;
        return new VoidOtapiAnswer($value);
    }
}