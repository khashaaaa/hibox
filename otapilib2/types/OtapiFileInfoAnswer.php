<?php

class OtapiFileInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiFileInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiFileInfo($value);
    }
}