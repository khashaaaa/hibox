<?php

class OtapiArrayOfPackageItem extends BaseOtapiType{
    /**
     * @return OtapiPackageItem[]
     */
    public function GetPackageItem(){
        return isset($this->xmlData->PackageItem) ? new UnboundedElementsIterator(
                $this->xmlData->PackageItem,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiPackageItem'
                )
            ) : array();
    }
}