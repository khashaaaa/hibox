<?php

class OtapiGetOperationTypesResponse extends BaseOtapiType{
    /**
     * @return OtapiOperationTypeInfoListAnswer
     */
    public function GetGetOperationTypesResult(){
        $value = isset($this->xmlData->GetOperationTypesResult) ? $this->xmlData->GetOperationTypesResult : false;
        return new OtapiOperationTypeInfoListAnswer($value);
    }
}