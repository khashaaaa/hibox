<?php

class OtapiGetAllAreaListResponse extends BaseOtapiType{
    /**
     * @return OtapiAreaListAnswer
     */
    public function GetGetAllAreaListResult(){
        $value = isset($this->xmlData->GetAllAreaListResult) ? $this->xmlData->GetAllAreaListResult : false;
        return new OtapiAreaListAnswer($value);
    }
}