<?php

class OtapiPaymentModeListAnswer extends OtapiAnswer{
    /**
     * @return OtapiArrayOfPaymentMode
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiArrayOfPaymentMode($value);
    }
}