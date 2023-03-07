<?php

class OtapiDemoObjectAnswer extends OtapiAnswer{
    /**
     * @return OtapiDemoObject
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDemoObject($value);
    }
}