<?php

class OtapiLogEntryBaseData extends OtapiBaseSelfErrorCheck{
    /**
     * @return int
     */
    public function GetInstanceUserId(){
        $value = isset($this->xmlData->InstanceUserId) ? (string)$this->xmlData->InstanceUserId : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOperationType(){
        $value = isset($this->xmlData->OperationType) ? (string)$this->xmlData->OperationType : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetOrderId(){
        $value = isset($this->xmlData->OrderId) ? (string)$this->xmlData->OrderId : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetOrderLineId(){
        $value = isset($this->xmlData->OrderLineId) ? (string)$this->xmlData->OrderLineId : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetCustomerId(){
        $value = isset($this->xmlData->CustomerId) ? (string)$this->xmlData->CustomerId : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}