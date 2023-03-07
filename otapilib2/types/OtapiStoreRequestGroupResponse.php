<?php

class OtapiStoreRequestGroupResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetStoreRequestGroupResult(){
        $value = isset($this->xmlData->StoreRequestGroupResult) ? $this->xmlData->StoreRequestGroupResult : false;
        return new VoidOtapiAnswer($value);
    }
}