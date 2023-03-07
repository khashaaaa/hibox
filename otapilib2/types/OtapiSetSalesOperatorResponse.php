<?php

class OtapiSetSalesOperatorResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetSetSalesOperatorResult(){
        $value = isset($this->xmlData->SetSalesOperatorResult) ? $this->xmlData->SetSalesOperatorResult : false;
        return new VoidOtapiAnswer($value);
    }
}