<?php

class OtapiSalesShippingInfoListAnswer extends OtapiAnswer{
    /**
     * @return ArrayOfOtapiPackageInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new ArrayOfOtapiPackageInfo($value);
    }
}