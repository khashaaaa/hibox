<?php

class OtapiActionInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfActionInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfActionInfo($value);
    }
}