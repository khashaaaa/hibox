<?php

class OtapiAccountInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiAccountInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiAccountInfo($value);
    }
}