<?php

class OtapiRemoveItemRatingListResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveItemRatingListResult(){
        $value = isset($this->xmlData->RemoveItemRatingListResult) ? $this->xmlData->RemoveItemRatingListResult : false;
        return new VoidOtapiAnswer($value);
    }
}