<?php

class OtapiCreateWarehouseItemAnswer extends OtapiAnswer{
    /**
     * @return long
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? (string)$this->xmlData->Result : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}