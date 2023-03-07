<?php

class OtapiOrderTHSInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetLogisticianInstanceKey(){
        $value = isset($this->xmlData->LogisticianInstanceKey) ? (string)$this->xmlData->LogisticianInstanceKey : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetAgentInstanceKey(){
        $value = isset($this->xmlData->AgentInstanceKey) ? (string)$this->xmlData->AgentInstanceKey : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetAgentOrderId(){
        $value = isset($this->xmlData->AgentOrderId) ? (string)$this->xmlData->AgentOrderId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetAgentInstanceName(){
        $value = isset($this->xmlData->AgentInstanceName) ? (string)$this->xmlData->AgentInstanceName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLogisticianInstanceName(){
        $value = isset($this->xmlData->LogisticianInstanceName) ? (string)$this->xmlData->LogisticianInstanceName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLogisticianOrderId(){
        $value = isset($this->xmlData->LogisticianOrderId) ? (string)$this->xmlData->LogisticianOrderId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}