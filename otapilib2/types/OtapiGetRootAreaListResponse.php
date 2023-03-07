<?php

class OtapiGetRootAreaListResponse extends BaseOtapiType{
    /**
     * @return OtapiAreaListAnswer
     */
    public function GetGetRootAreaListResult(){
        $value = isset($this->xmlData->GetRootAreaListResult) ? $this->xmlData->GetRootAreaListResult : false;
        return new OtapiAreaListAnswer($value);
    }
}