<?php

class OtapiContentTypesAnswer extends OtapiAnswer{
    /**
     * @return OtapiArrayOfString1
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiArrayOfString1($value);
    }
}