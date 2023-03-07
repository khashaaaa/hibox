<?php

class DataListOfOtapiBrandInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiBrandInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiBrandInfo($value);
    }
}