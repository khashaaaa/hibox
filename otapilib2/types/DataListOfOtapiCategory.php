<?php

class DataListOfOtapiCategory extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiCategory
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiCategory($value);
    }
}