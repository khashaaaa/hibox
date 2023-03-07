<?php

class OtapiOrderHistoryItem extends BaseOtapiType{
    /**
     * @return int
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'int';
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
     * @return OtapiStatusInfoBase
     */
    public function GetOldStatus(){
        $value = isset($this->xmlData->OldStatus) ? $this->xmlData->OldStatus : false;
        return new OtapiStatusInfoBase($value);
    }
    /**
     * @return OtapiStatusInfoBase
     */
    public function GetNewStatus(){
        $value = isset($this->xmlData->NewStatus) ? $this->xmlData->NewStatus : false;
        return new OtapiStatusInfoBase($value);
    }
    /**
     * @return dateTime
     */
    public function GetDate(){
        $value = isset($this->xmlData->Date) ? (string)$this->xmlData->Date : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiBaseUserInfo
     */
    public function GetUserInfo(){
        $value = isset($this->xmlData->UserInfo) ? $this->xmlData->UserInfo : false;
        return new OtapiBaseUserInfo($value);
    }
}