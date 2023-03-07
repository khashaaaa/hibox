<?php

class OtapiArrayOfSalesStatusInfo extends BaseOtapiType{
    /**
     * @return OtapiSalesStatusInfo[]
     */
    public function GetSalesStatusInfo(){
        return isset($this->xmlData->SalesStatusInfo) ? new UnboundedElementsIterator(
                $this->xmlData->SalesStatusInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSalesStatusInfo'
                )
            ) : array();
    }
}