<?php

class OtapiMessageTemplateInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiMessageTemplateInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiMessageTemplateInfo($value);
    }
}