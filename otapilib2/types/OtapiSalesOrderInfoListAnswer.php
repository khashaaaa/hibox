<?php

class OtapiSalesOrderInfoListAnswer extends OtapiAnswer{
    /**
     * @return ArrayOfOtapiOrderInfo1
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new ArrayOfOtapiOrderInfo1($value);
    }
}