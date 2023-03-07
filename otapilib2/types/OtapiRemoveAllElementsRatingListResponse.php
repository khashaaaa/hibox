<?php

class OtapiRemoveAllElementsRatingListResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveAllElementsRatingListResult(){
        $value = isset($this->xmlData->RemoveAllElementsRatingListResult) ? $this->xmlData->RemoveAllElementsRatingListResult : false;
        return new VoidOtapiAnswer($value);
    }
}