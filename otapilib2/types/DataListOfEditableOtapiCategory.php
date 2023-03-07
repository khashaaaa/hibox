<?php

class DataListOfEditableOtapiCategory extends BaseOtapiType{
    /**
     * @return ArrayOfEditableOtapiCategory
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfEditableOtapiCategory($value);
    }
}