<?php

class OtapiDiscountGroupUserInfo extends OtapiBaseUserInfo{
    /**
     * @return boolean
     */
    public function IsAutomateSetted(){
        $value = isset($this->xmlData->IsAutomateSetted) ? (string)$this->xmlData->IsAutomateSetted : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}