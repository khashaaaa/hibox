<?php

class OtapiArrayOfPaymentFormParameter extends BaseOtapiType{
    /**
     * @return OtapiPaymentFormParameter[]
     */
    public function GetParameter(){
        return isset($this->xmlData->Parameter) ? new UnboundedElementsIterator(
                $this->xmlData->Parameter,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiPaymentFormParameter'
                )
            ) : array();
    }
}