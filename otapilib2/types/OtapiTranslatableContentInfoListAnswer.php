<?php

class OtapiTranslatableContentInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfTranslatableContentInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfTranslatableContentInfo($value);
    }
}