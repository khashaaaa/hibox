<?php

class OtapiGetBlackListContentsResponse extends BaseOtapiType{
    /**
     * @return OtapiContentListListAnswer
     */
    public function GetGetBlackListContentsResult(){
        $value = isset($this->xmlData->GetBlackListContentsResult) ? $this->xmlData->GetBlackListContentsResult : false;
        return new OtapiContentListListAnswer($value);
    }
}