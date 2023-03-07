<?php
OTBase::import('system.lib.service.for_generator_2_0.BaseElement');

class BaseEntityOfOtapiEntityErrorCode extends BaseElement {
    public function GetId(){
        return isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
    }

    public function GetErrorCode(){
        return isset($this->xmlData->ErrorCode) ? (string)$this->xmlData->ErrorCode : false;
    }

    /**
     * TODO: Для булева типа на сервисах не добавлять приставку Get для методов
     * @return bool|string
     */
    public function HasError(){
        return isset($this->xmlData->HasError) ? (string)$this->xmlData->HasError : false;
    }
}