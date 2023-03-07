<?php

class OtapiElementIdAnswer extends OtapiAnswer{
    /**
     * @return OtapiElementId
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiElementId($value);
    }
}