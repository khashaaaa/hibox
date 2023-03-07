<?php

class OtapiDeliveryModeListFrameAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataSubListOfDeliveryMode
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataSubListOfDeliveryMode($value);
    }
}