<?php

class OtapiGetSubAreaListResponse extends BaseOtapiType{
    /**
     * @return OtapiAreaListAnswer
     */
    public function GetGetSubAreaListResult(){
        $value = isset($this->xmlData->GetSubAreaListResult) ? $this->xmlData->GetSubAreaListResult : false;
        return new OtapiAreaListAnswer($value);
    }
}