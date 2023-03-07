<?php

class OtapiBasePriceAnswer extends OtapiAnswer{
    /**
     * @return OtapiBasePrice
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiBasePrice($value);
    }
}