<?php

class OtapiArrayOfBackgroundActivityStepInfo extends BaseOtapiType{
    /**
     * @return OtapiBackgroundActivityStepInfo[]
     */
    public function GetStep(){
        return isset($this->xmlData->Step) ? new UnboundedElementsIterator(
                $this->xmlData->Step,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiBackgroundActivityStepInfo'
                )
            ) : array();
    }
}