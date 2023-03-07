<?php

class OtapiDataListOfItemReviewInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfItemReviewInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfItemReviewInfo($value);
    }
}