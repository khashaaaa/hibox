<?php

class OtapiTranslationInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataSubListOfTranslationInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataSubListOfTranslationInfo($value);
    }
}