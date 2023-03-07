<?php

class OtapiSalesProcLogInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiArrayOfSalesProcLogInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiArrayOfSalesProcLogInfo($value);
    }
}