<?php

class ArrayOfRatingListSearchResultOfOtapiItemInfo extends BaseOtapiType{
    /**
     * @return RatingListSearchResultOfOtapiItemInfo[]
     */
    public function GetRatingList(){
        return isset($this->xmlData->RatingList) ? new UnboundedElementsIterator(
                $this->xmlData->RatingList,
                array(
                    'type' => 'complexType',
                    'name' => 'RatingListSearchResultOfOtapiItemInfo'
                )
            ) : array();
    }
}