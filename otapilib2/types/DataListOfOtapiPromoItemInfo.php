<?php

class DataListOfOtapiPromoItemInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiPromoItemInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiPromoItemInfo($value);
    }
}