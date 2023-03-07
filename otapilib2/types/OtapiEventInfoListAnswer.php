<?php

class OtapiEventInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfEventInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfEventInfo($value);
    }
}