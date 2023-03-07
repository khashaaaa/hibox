<?php

class OtapiDeliveryModeListAnswer extends OtapiAnswer{
    /**
     * @return OtapiArrayOfDeliveryMode
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiArrayOfDeliveryMode($value);
    }
}