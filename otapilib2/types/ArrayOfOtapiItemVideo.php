<?php

class ArrayOfOtapiItemVideo extends BaseOtapiType{
    /**
     * @return OtapiItemVideo[]
     */
    public function GetVideo(){
        return isset($this->xmlData->Video) ? new UnboundedElementsIterator(
                $this->xmlData->Video,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiItemVideo'
                )
            ) : array();
    }
}