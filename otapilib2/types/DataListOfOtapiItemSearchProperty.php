<?php

class DataListOfOtapiItemSearchProperty extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiItemSearchProperty
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiItemSearchProperty($value);
    }
}