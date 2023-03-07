<?php

class OtapiArrayOfItemReviewAnswerInfo extends BaseOtapiType{
    /**
     * @return OtapiItemReviewAnswerInfo[]
     */
    public function GetAnswer(){
        return isset($this->xmlData->Answer) ? new UnboundedElementsIterator(
                $this->xmlData->Answer,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiItemReviewAnswerInfo'
                )
            ) : array();
    }
}