<?php

class OtapiArrayOfMoney extends BaseOtapiType{
    /**
     * @return OtapiMoney[]
     */
    public function GetMoney(){
        return isset($this->xmlData->Money) ? new UnboundedElementsIterator(
                $this->xmlData->Money,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiMoney'
                )
            ) : array();
    }
}