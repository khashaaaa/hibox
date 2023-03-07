<?php

class OtapiSearchPromoteItemsFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiPromoItemInfoListFrameAnswer
     */
    public function GetSearchPromoteItemsFrameResult(){
        $value = isset($this->xmlData->SearchPromoteItemsFrameResult) ? $this->xmlData->SearchPromoteItemsFrameResult : false;
        return new OtapiPromoItemInfoListFrameAnswer($value);
    }
}