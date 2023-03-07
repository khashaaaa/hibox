<?php

class OtapiMergeOrdersResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetMergeOrdersResult(){
        $value = isset($this->xmlData->MergeOrdersResult) ? $this->xmlData->MergeOrdersResult : false;
        return new VoidOtapiAnswer($value);
    }
}