<?php

class OtapiItemRatingCollectionListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfItemRatingCollection
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfItemRatingCollection($value);
    }
}