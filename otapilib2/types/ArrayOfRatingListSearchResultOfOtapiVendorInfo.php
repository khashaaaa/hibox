<?php

class ArrayOfRatingListSearchResultOfOtapiVendorInfo extends BaseOtapiType{
    /**
     * @return RatingListSearchResultOfOtapiVendorInfo[]
     */
    public function GetRatingList(){
        return isset($this->xmlData->RatingList) ? new UnboundedElementsIterator(
                $this->xmlData->RatingList,
                array(
                    'type' => 'complexType',
                    'name' => 'RatingListSearchResultOfOtapiVendorInfo'
                )
            ) : array();
    }
}