<?php

class DataListOfOtapiItemPromotion extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiItemPromotion1
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiItemPromotion1($value);
    }
}