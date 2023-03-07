<?php

class OtapiDataListOfSimplifiedItemReview extends BaseOtapiType{
    /**
     * @return OtapiArrayOfSimplifiedItemReview
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfSimplifiedItemReview($value);
    }
}