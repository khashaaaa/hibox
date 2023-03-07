<?php

class OtapiOrderLineInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return DataSubListOfOtapiOrderLineInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataSubListOfOtapiOrderLineInfo($value);
    }
}