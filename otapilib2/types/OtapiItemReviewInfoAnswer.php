<?php

class OtapiItemReviewInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiItemReviewInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiItemReviewInfo($value);
    }
}