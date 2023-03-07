<?php

class OtapiPriceAnswer extends OtapiAnswer{
    /**
     * @return OtapiPrice
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiPrice($value);
    }
}