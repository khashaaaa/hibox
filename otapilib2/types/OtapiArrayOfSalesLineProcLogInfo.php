<?php

class OtapiArrayOfSalesLineProcLogInfo extends BaseOtapiType{
    /**
     * @return OtapiSalesLineProcLogInfo[]
     */
    public function GetSalesLineProcLogInfo(){
        return isset($this->xmlData->SalesLineProcLogInfo) ? new UnboundedElementsIterator(
                $this->xmlData->SalesLineProcLogInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSalesLineProcLogInfo'
                )
            ) : array();
    }
}