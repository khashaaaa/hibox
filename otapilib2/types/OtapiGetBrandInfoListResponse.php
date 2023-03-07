<?php

class OtapiGetBrandInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiBrandInfoListAnswer
     */
    public function GetGetBrandInfoListResult(){
        $value = isset($this->xmlData->GetBrandInfoListResult) ? $this->xmlData->GetBrandInfoListResult : false;
        return new OtapiBrandInfoListAnswer($value);
    }
}