<?php

class OtapiGetVendorItemSimpleInfoSortedListFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiItemInfoListFrameAnswer
     */
    public function GetGetVendorItemSimpleInfoSortedListFrameResult(){
        $value = isset($this->xmlData->GetVendorItemSimpleInfoSortedListFrameResult) ? $this->xmlData->GetVendorItemSimpleInfoSortedListFrameResult : false;
        return new OtapiItemInfoListFrameAnswer($value);
    }
}