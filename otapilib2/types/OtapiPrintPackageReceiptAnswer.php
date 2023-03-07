<?php

class OtapiPrintPackageReceiptAnswer extends OtapiAnswer{
    /**
     * @return base64Binary
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? (string)$this->xmlData->Result : false;
        $propertyType = 'base64Binary';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}