<?php

class ArrayOfRatingListSearchResultOfOtapiBrandInfo extends BaseOtapiType{
    /**
     * @return RatingListSearchResultOfOtapiBrandInfo[]
     */
    public function GetRatingList(){
        return isset($this->xmlData->RatingList) ? new UnboundedElementsIterator(
                $this->xmlData->RatingList,
                array(
                    'type' => 'complexType',
                    'name' => 'RatingListSearchResultOfOtapiBrandInfo'
                )
            ) : array();
    }
}