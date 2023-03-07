<?php

class OtapiImportItemReviewsInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiImportItemReviewsInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiImportItemReviewsInfo($value);
    }
}