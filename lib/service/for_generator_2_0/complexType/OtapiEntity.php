<?php
OTBase::import('system.lib.service.for_generator_2_0.complexType.BaseEntityOfOtapiEntityErrorCode');

class OtapiEntity extends BaseEntityOfOtapiEntityErrorCode {
    public function GetProviderType(){
        return isset($this->xmlData->ProviderType) ? (string)$this->xmlData->ProviderType : false;
    }
}