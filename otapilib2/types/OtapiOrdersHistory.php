<?php

class OtapiOrdersHistory extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiOrderHistoryItem
     */
    public function GetItems(){
        $value = isset($this->xmlData->Items) ? $this->xmlData->Items : false;
        return new ArrayOfOtapiOrderHistoryItem($value);
    }
}