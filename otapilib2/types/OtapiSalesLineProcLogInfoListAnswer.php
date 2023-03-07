<?php

class OtapiSalesLineProcLogInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiArrayOfSalesLineProcLogInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiArrayOfSalesLineProcLogInfo($value);
    }
}