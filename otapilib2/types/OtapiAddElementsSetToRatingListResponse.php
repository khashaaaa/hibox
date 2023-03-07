<?php

class OtapiAddElementsSetToRatingListResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetAddElementsSetToRatingListResult(){
        $value = isset($this->xmlData->AddElementsSetToRatingListResult) ? $this->xmlData->AddElementsSetToRatingListResult : false;
        return new VoidOtapiAnswer($value);
    }
}