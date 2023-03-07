<?php

class OtapiArrayOfTransactionInfoAdministration1 extends BaseOtapiType{
    /**
     * @return OtapiTransactionInfoAdministration[]
     */
    public function GetTransInfo(){
        return isset($this->xmlData->TransInfo) ? new UnboundedElementsIterator(
                $this->xmlData->TransInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiTransactionInfoAdministration'
                )
            ) : array();
    }
}