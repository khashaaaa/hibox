<?php

class OtapiGetItemInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiItemInfoListAnswer
     */
    public function GetGetItemInfoListResult(){
        $value = isset($this->xmlData->GetItemInfoListResult) ? $this->xmlData->GetItemInfoListResult : false;
        return new OtapiItemInfoListAnswer($value);
    }
}