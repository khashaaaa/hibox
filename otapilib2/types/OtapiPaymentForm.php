<?php

class OtapiPaymentForm extends BaseOtapiType{
    /**
     * @return boolean
     */
    public function IsImmmediate(){
        $value = isset($this->xmlData->IsImmmediate) ? (string)$this->xmlData->IsImmmediate : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetRequestMethod(){
        $value = isset($this->xmlData->RequestMethod) ? (string)$this->xmlData->RequestMethod : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetRequestUrl(){
        $value = isset($this->xmlData->RequestUrl) ? (string)$this->xmlData->RequestUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsIFrame(){
        $value = isset($this->xmlData->IsIFrame) ? (string)$this->xmlData->IsIFrame : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfPaymentFormParameter
     */
    public function GetParameters(){
        $value = isset($this->xmlData->Parameters) ? $this->xmlData->Parameters : false;
        return new OtapiArrayOfPaymentFormParameter($value);
    }
    /**
     * @return boolean
     */
    public function IsNewWindow(){
        $value = isset($this->xmlData->IsNewWindow) ? (string)$this->xmlData->IsNewWindow : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}