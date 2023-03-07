<?php

class OtapiConfigurationDescription extends BaseOtapiType{
    /**
     * @return OtapiConfiguratorDescription[]
     */
    public function GetConfigurator(){
        return isset($this->xmlData->Configurator) ? new UnboundedElementsIterator(
                $this->xmlData->Configurator,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiConfiguratorDescription'
                )
            ) : array();
    }
}