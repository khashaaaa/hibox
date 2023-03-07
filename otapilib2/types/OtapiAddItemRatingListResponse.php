<?php

class OtapiAddItemRatingListResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetAddItemRatingListResult(){
        $value = isset($this->xmlData->AddItemRatingListResult) ? $this->xmlData->AddItemRatingListResult : false;
        return new VoidOtapiAnswer($value);
    }
}