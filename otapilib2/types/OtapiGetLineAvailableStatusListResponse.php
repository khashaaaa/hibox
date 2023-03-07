<?php

class OtapiGetLineAvailableStatusListResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesLineStatusInfoListAnswer
     */
    public function GetGetLineAvailableStatusListResult(){
        $value = isset($this->xmlData->GetLineAvailableStatusListResult) ? $this->xmlData->GetLineAvailableStatusListResult : false;
        return new OtapiSalesLineStatusInfoListAnswer($value);
    }
}