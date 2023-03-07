<?php

class OtapiRemoveAllItemRatingListResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveAllItemRatingListResult(){
        $value = isset($this->xmlData->RemoveAllItemRatingListResult) ? $this->xmlData->RemoveAllItemRatingListResult : false;
        return new VoidOtapiAnswer($value);
    }
}