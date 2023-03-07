<?php

class OtapiDataListOfItemRatingCollection extends BaseOtapiType{
    /**
     * @return OtapiArrayOfItemRatingCollection
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfItemRatingCollection($value);
    }
}