<?php

class OtapiArrayOfProviderSearchFeatureInfo extends BaseOtapiType{
    /**
     * @return OtapiProviderSearchFeatureInfo[]
     */
    public function GetFeature(){
        return isset($this->xmlData->Feature) ? new UnboundedElementsIterator(
                $this->xmlData->Feature,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiProviderSearchFeatureInfo'
                )
            ) : array();
    }
}