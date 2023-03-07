<?php

class OtapiArrayOfSalesProcLogInfo extends BaseOtapiType{
    /**
     * @return OtapiSalesProcLogInfo[]
     */
    public function GetSalesProcLogInfo(){
        return isset($this->xmlData->SalesProcLogInfo) ? new UnboundedElementsIterator(
                $this->xmlData->SalesProcLogInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSalesProcLogInfo'
                )
            ) : array();
    }
}