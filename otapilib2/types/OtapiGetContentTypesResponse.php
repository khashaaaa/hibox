<?php

class OtapiGetContentTypesResponse extends BaseOtapiType{
    /**
     * @return OtapiContentTypesAnswer
     */
    public function GetGetContentTypesResult(){
        $value = isset($this->xmlData->GetContentTypesResult) ? $this->xmlData->GetContentTypesResult : false;
        return new OtapiContentTypesAnswer($value);
    }
}