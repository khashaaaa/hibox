<?php

class OtapiClearOrdersHistoryResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetClearOrdersHistoryResult(){
        $value = isset($this->xmlData->ClearOrdersHistoryResult) ? $this->xmlData->ClearOrdersHistoryResult : false;
        return new VoidOtapiAnswer($value);
    }
}