<?php

class OtapiUpdateItemRatingCategoryIdResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateItemRatingCategoryIdResult(){
        $value = isset($this->xmlData->UpdateItemRatingCategoryIdResult) ? $this->xmlData->UpdateItemRatingCategoryIdResult : false;
        return new VoidOtapiAnswer($value);
    }
}