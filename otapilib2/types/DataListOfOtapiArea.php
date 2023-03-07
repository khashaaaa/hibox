<?php

class DataListOfOtapiArea extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiArea
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiArea($value);
    }
}