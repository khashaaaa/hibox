<?php

class OtapiItemInfoListAnswer extends OtapiAnswer{
    /**
     * @return DataListOfOtapiItemInfo
     */
    public function GetOtapiItemInfoList(){
        $value = isset($this->xmlData->OtapiItemInfoList) ? $this->xmlData->OtapiItemInfoList : false;
        return new DataListOfOtapiItemInfo($value);
    }
}