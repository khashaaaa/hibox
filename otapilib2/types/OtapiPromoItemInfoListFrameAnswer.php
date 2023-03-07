<?php

class OtapiPromoItemInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return DataSubListOfOtapiPromoItemInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataSubListOfOtapiPromoItemInfo($value);
    }
}