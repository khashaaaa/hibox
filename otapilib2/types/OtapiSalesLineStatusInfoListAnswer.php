<?php

class OtapiSalesLineStatusInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiArrayOfSalesLineStatusInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiArrayOfSalesLineStatusInfo($value);
    }
}