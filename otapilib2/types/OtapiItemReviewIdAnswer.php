<?php

class OtapiItemReviewIdAnswer extends OtapiAnswer{
    /**
     * @return OtapiItemReviewId
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiItemReviewId($value);
    }
}