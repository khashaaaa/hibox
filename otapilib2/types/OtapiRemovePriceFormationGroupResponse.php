<?php

class OtapiRemovePriceFormationGroupResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemovePriceFormationGroupResult(){
        $value = isset($this->xmlData->RemovePriceFormationGroupResult) ? $this->xmlData->RemovePriceFormationGroupResult : false;
        return new VoidOtapiAnswer($value);
    }
}