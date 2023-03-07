<?php

class OtapiArrayOfSalesLine extends BaseOtapiType{
    /**
     * @return OtapiSalesLine[]
     */
    public function GetSalesLine(){
        return isset($this->xmlData->SalesLine) ? new UnboundedElementsIterator(
                $this->xmlData->SalesLine,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSalesLine'
                )
            ) : array();
    }
}