<?php

class OtapiRemoveExternalDeliveryTypeResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveExternalDeliveryTypeResult(){
        $value = isset($this->xmlData->RemoveExternalDeliveryTypeResult) ? $this->xmlData->RemoveExternalDeliveryTypeResult : false;
        return new VoidOtapiAnswer($value);
    }
}