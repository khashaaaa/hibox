<?php

class OtapiGetLanguageInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiNamedPropertyListAnswer
     */
    public function GetGetLanguageInfoListResult(){
        $value = isset($this->xmlData->GetLanguageInfoListResult) ? $this->xmlData->GetLanguageInfoListResult : false;
        return new OtapiNamedPropertyListAnswer($value);
    }
}