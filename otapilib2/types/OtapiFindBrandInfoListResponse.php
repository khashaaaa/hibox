<?php

class OtapiFindBrandInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiBrandInfoListAnswer
     */
    public function GetFindBrandInfoListResult(){
        $value = isset($this->xmlData->FindBrandInfoListResult) ? $this->xmlData->FindBrandInfoListResult : false;
        return new OtapiBrandInfoListAnswer($value);
    }
}