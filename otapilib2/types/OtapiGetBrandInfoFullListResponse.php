<?php

class OtapiGetBrandInfoFullListResponse extends BaseOtapiType{
    /**
     * @return OtapiBrandInfoListAnswer
     */
    public function GetGetBrandInfoFullListResult(){
        $value = isset($this->xmlData->GetBrandInfoFullListResult) ? $this->xmlData->GetBrandInfoFullListResult : false;
        return new OtapiBrandInfoListAnswer($value);
    }
}