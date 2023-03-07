<?php

class OtapiItemReviewInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataSubListOfItemReviewInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataSubListOfItemReviewInfo($value);
    }
}