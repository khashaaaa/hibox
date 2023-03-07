<?php

class OtapiSalesOrderInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return DataSubListOfOtapiOrderInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataSubListOfOtapiOrderInfo($value);
    }
}