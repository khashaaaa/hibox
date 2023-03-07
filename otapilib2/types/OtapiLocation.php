<?php

class OtapiLocation extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetCity(){
        $value = isset($this->xmlData->City) ? (string)$this->xmlData->City : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetState(){
        $value = isset($this->xmlData->State) ? (string)$this->xmlData->State : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCountry(){
        $value = isset($this->xmlData->Country) ? (string)$this->xmlData->Country : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDistrict(){
        $value = isset($this->xmlData->District) ? (string)$this->xmlData->District : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetZip(){
        $value = isset($this->xmlData->Zip) ? (string)$this->xmlData->Zip : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetAddress(){
        $value = isset($this->xmlData->Address) ? (string)$this->xmlData->Address : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetAreaId(){
        $value = isset($this->xmlData->AreaId) ? (string)$this->xmlData->AreaId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}