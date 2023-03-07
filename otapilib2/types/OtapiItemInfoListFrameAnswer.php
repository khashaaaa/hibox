<?php

class OtapiItemInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return DataSubListOfOtapiItemInfo
     */
    public function GetOtapiItemInfoSubList(){
        $value = isset($this->xmlData->OtapiItemInfoSubList) ? $this->xmlData->OtapiItemInfoSubList : false;
        return new DataSubListOfOtapiItemInfo($value);
    }
}