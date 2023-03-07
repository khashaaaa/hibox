<?php

class OtapiBasketCheckingResultAnswer extends OtapiAnswer{
    /**
     * @return OtapiBasketCheckingResult
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiBasketCheckingResult($value);
    }
}