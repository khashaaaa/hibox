<?php

class OtapiGetGlobalBrandInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiBrandInfoListAnswer
     */
    public function GetGetGlobalBrandInfoListResult(){
        $value = isset($this->xmlData->GetGlobalBrandInfoListResult) ? $this->xmlData->GetGlobalBrandInfoListResult : false;
        return new OtapiBrandInfoListAnswer($value);
    }
}