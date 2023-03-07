<?php

class OtapiSalesStatusInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiArrayOfSalesStatusInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiArrayOfSalesStatusInfo($value);
    }
}