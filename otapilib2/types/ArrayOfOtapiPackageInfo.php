<?php

class ArrayOfOtapiPackageInfo extends BaseOtapiType{
    /**
     * @return OtapiPackageInfo[]
     */
    public function GetSalesShippingInfo(){
        return isset($this->xmlData->SalesShippingInfo) ? new UnboundedElementsIterator(
                $this->xmlData->SalesShippingInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiPackageInfo'
                )
            ) : array();
    }
}