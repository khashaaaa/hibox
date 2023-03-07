<?php

class OtapiPrintPackageReceiptResponse extends BaseOtapiType{
    /**
     * @return OtapiPrintPackageReceiptAnswer
     */
    public function GetPrintPackageReceiptResult(){
        $value = isset($this->xmlData->PrintPackageReceiptResult) ? $this->xmlData->PrintPackageReceiptResult : false;
        return new OtapiPrintPackageReceiptAnswer($value);
    }
}