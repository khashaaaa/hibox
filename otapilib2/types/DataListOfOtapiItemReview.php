<?php

class DataListOfOtapiItemReview extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiItemReview
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiItemReview($value);
    }
}