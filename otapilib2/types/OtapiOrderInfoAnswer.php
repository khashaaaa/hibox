<?php

class OtapiOrderInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiOrderInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiOrderInfo($value);
    }
}