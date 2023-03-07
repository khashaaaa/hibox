<?php

class ArrayOfOtapiOrderInfo1 extends BaseOtapiType{
    /**
     * @return OtapiOrderInfo[]
     */
    public function GetSalesOrderInfo(){
        return isset($this->xmlData->SalesOrderInfo) ? new UnboundedElementsIterator(
                $this->xmlData->SalesOrderInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiOrderInfo'
                )
            ) : array();
    }
}