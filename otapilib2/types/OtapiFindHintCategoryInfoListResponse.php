<?php

class OtapiFindHintCategoryInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListAnswer
     */
    public function GetFindHintCategoryInfoListResult(){
        $value = isset($this->xmlData->FindHintCategoryInfoListResult) ? $this->xmlData->FindHintCategoryInfoListResult : false;
        return new OtapiCategoryListAnswer($value);
    }
}