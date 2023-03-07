<?php

class OtapiArrayOfTransactionInfoAdministration extends BaseOtapiType{
    /**
     * @return OtapiTransactionInfoAdministration[]
     */
    public function GetTransactionInfo(){
        return isset($this->xmlData->TransactionInfo) ? new UnboundedElementsIterator(
                $this->xmlData->TransactionInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiTransactionInfoAdministration'
                )
            ) : array();
    }
}