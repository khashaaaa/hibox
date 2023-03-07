<?php

class OtapiArrayOfPackagePriceInfo extends BaseOtapiType{
    /**
     * @return OtapiPackagePriceInfo[]
     */
    public function GetPackagePriceInfo(){
        return isset($this->xmlData->PackagePriceInfo) ? new UnboundedElementsIterator(
                $this->xmlData->PackagePriceInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiPackagePriceInfo'
                )
            ) : array();
    }
}