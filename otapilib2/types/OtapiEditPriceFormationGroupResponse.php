<?php

class OtapiEditPriceFormationGroupResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditPriceFormationGroupResult(){
        $value = isset($this->xmlData->EditPriceFormationGroupResult) ? $this->xmlData->EditPriceFormationGroupResult : false;
        return new VoidOtapiAnswer($value);
    }
}