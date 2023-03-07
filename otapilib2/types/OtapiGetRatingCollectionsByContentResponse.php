<?php

class OtapiGetRatingCollectionsByContentResponse extends BaseOtapiType{
    /**
     * @return OtapiItemRatingCollectionListAnswer
     */
    public function GetGetRatingCollectionsByContentResult(){
        $value = isset($this->xmlData->GetRatingCollectionsByContentResult) ? $this->xmlData->GetRatingCollectionsByContentResult : false;
        return new OtapiItemRatingCollectionListAnswer($value);
    }
}