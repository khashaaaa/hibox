<?php

class OtapiRemoveElementsSetRatingListResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveElementsSetRatingListResult(){
        $value = isset($this->xmlData->RemoveElementsSetRatingListResult) ? $this->xmlData->RemoveElementsSetRatingListResult : false;
        return new VoidOtapiAnswer($value);
    }
}