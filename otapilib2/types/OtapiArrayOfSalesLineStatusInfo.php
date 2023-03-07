<?php

class OtapiArrayOfSalesLineStatusInfo extends BaseOtapiType{
    /**
     * @return OtapiSalesLineStatusInfo[]
     */
    public function GetSalesLineStatusInfo(){
        return isset($this->xmlData->SalesLineStatusInfo) ? new UnboundedElementsIterator(
                $this->xmlData->SalesLineStatusInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSalesLineStatusInfo'
                )
            ) : array();
    }
}