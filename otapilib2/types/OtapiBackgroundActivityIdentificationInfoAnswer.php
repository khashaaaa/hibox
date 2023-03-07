<?php

class OtapiBackgroundActivityIdentificationInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiBackgroundActivityIdentificationInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiBackgroundActivityIdentificationInfo($value);
    }
}