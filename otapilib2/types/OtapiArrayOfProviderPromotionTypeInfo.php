<?php

class OtapiArrayOfProviderPromotionTypeInfo extends BaseOtapiType{
    /**
     * @return OtapiProviderPromotionTypeInfo[]
     */
    public function GetProviderPromotionTypeInfo(){
        return isset($this->xmlData->ProviderPromotionTypeInfo) ? new UnboundedElementsIterator(
                $this->xmlData->ProviderPromotionTypeInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiProviderPromotionTypeInfo'
                )
            ) : array();
    }
}