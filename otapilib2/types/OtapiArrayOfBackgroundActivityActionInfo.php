<?php

class OtapiArrayOfBackgroundActivityActionInfo extends BaseOtapiType{
    /**
     * @return OtapiBackgroundActivityActionInfo[]
     */
    public function GetAction(){
        return isset($this->xmlData->Action) ? new UnboundedElementsIterator(
                $this->xmlData->Action,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiBackgroundActivityActionInfo'
                )
            ) : array();
    }
}