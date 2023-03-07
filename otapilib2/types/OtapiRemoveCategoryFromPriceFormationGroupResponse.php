<?php

class OtapiRemoveCategoryFromPriceFormationGroupResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveCategoryFromPriceFormationGroupResult(){
        $value = isset($this->xmlData->RemoveCategoryFromPriceFormationGroupResult) ? $this->xmlData->RemoveCategoryFromPriceFormationGroupResult : false;
        return new VoidOtapiAnswer($value);
    }
}