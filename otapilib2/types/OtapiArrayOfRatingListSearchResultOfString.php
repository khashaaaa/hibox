<?php

class OtapiArrayOfRatingListSearchResultOfString extends BaseOtapiType{
    /**
     * @return OtapiRatingListSearchResultOfString[]
     */
    public function GetRatingList(){
        return isset($this->xmlData->RatingList) ? new UnboundedElementsIterator(
                $this->xmlData->RatingList,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiRatingListSearchResultOfString'
                )
            ) : array();
    }
}