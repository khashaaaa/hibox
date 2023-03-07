<?php
OTBase::import('system.lib.service.for_generator_2_0.complexType.OtapiEntity');
OTBase::import('system.lib.service.for_generator_2_0.complexType.DataListOfOtapiCategory');

class OtapiCategory extends OtapiEntity {
    public function IsHidden(){
        return isset($this->xmlData->IsHidden) ? (string)$this->xmlData->IsHidden : false;
    }

    public function IsVirtual(){
        return isset($this->xmlData->IsVirtual) ? (string)$this->xmlData->IsVirtual : false;
    }

    public function GetExternalId(){
        return isset($this->xmlData->ExternalId) ? (string)$this->xmlData->ExternalId : false;
    }

    public function GetName(){
        return isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
    }

    public function IsParent(){
        return isset($this->xmlData->IsParent) ? (string)$this->xmlData->IsParent : false;
    }

    public function GetParentId(){
        return isset($this->xmlData->ParentId) ? (string)$this->xmlData->ParentId : false;
    }

    public function GetApproxWeight(){
        return isset($this->xmlData->ApproxWeight) ? (string)$this->xmlData->ApproxWeight : false;
    }
}