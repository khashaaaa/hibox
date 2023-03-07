<?php

class OtapiArrayOfActionInfo extends BaseOtapiType{
    /**
     * @return OtapiActionInfo[]
     */
    public function GetActionInfo(){
        return isset($this->xmlData->ActionInfo) ? new UnboundedElementsIterator(
                $this->xmlData->ActionInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiActionInfo'
                )
            ) : array();
    }
}