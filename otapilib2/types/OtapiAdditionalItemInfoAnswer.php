<?php

class OtapiAdditionalItemInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiAdditionalItemInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiAdditionalItemInfo($value);
    }
}