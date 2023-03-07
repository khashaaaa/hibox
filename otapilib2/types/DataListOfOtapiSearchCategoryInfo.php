<?php

class DataListOfOtapiSearchCategoryInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiSearchCategoryInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiSearchCategoryInfo($value);
    }
}