<?php

class OtapiArrayOfFieldPlaceholderMetaInfo extends BaseOtapiType{
    /**
     * @return OtapiFieldPlaceholderMetaInfo[]
     */
    public function GetPlaceholder(){
        return isset($this->xmlData->Placeholder) ? new UnboundedElementsIterator(
                $this->xmlData->Placeholder,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiFieldPlaceholderMetaInfo'
                )
            ) : array();
    }
}