<?php

class OtapiContentMenuItemInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataSubListOfContentMenuItemInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataSubListOfContentMenuItemInfo($value);
    }
}