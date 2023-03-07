<?php

class OtapiGetVendorItemInfoSortedListFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiItemInfoListFrameAnswer
     */
    public function GetGetVendorItemInfoSortedListFrameResult(){
        $value = isset($this->xmlData->GetVendorItemInfoSortedListFrameResult) ? $this->xmlData->GetVendorItemInfoSortedListFrameResult : false;
        return new OtapiItemInfoListFrameAnswer($value);
    }
}