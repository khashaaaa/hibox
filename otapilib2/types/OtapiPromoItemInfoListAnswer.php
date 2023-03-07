<?php

class OtapiPromoItemInfoListAnswer extends OtapiAnswer{
    /**
     * @return DataListOfOtapiPromoItemInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataListOfOtapiPromoItemInfo($value);
    }
}