<?php

class OtapiArrayOfBackgroundActivityStepActionInfo extends BaseOtapiType{
    /**
     * @return OtapiBackgroundActivityStepActionInfo[]
     */
    public function GetAction(){
        return isset($this->xmlData->Action) ? new UnboundedElementsIterator(
                $this->xmlData->Action,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiBackgroundActivityStepActionInfo'
                )
            ) : array();
    }
}