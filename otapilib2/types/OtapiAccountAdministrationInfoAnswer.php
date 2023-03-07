<?php

class OtapiAccountAdministrationInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiAccountAdministrationInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiAccountAdministrationInfo($value);
    }
}