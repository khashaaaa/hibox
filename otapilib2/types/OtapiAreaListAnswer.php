<?php

class OtapiAreaListAnswer extends OtapiAnswer{
    /**
     * @return DataListOfOtapiArea
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataListOfOtapiArea($value);
    }
}