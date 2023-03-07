<?php

class OtapiChangeSalesOrderLinePurchaseInfoResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetChangeSalesOrderLinePurchaseInfoResult(){
        $value = isset($this->xmlData->ChangeSalesOrderLinePurchaseInfoResult) ? $this->xmlData->ChangeSalesOrderLinePurchaseInfoResult : false;
        return new VoidOtapiAnswer($value);
    }
}