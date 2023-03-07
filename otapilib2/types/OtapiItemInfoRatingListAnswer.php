<?php

class OtapiItemInfoRatingListAnswer extends OtapiAnswer{
    /**
     * @return DataListOfOtapiItemInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new DataListOfOtapiItemInfo($value);
    }
}