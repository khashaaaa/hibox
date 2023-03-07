<?php

class OtapiChangeSalesOrderLinePurchaseInfoForOperatorResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetChangeSalesOrderLinePurchaseInfoForOperatorResult(){
        $value = isset($this->xmlData->ChangeSalesOrderLinePurchaseInfoForOperatorResult) ? $this->xmlData->ChangeSalesOrderLinePurchaseInfoForOperatorResult : false;
        return new VoidOtapiAnswer($value);
    }
}