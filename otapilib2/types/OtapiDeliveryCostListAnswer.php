<?php

class OtapiDeliveryCostListAnswer extends OtapiAnswer{
    /**
     * @return DataListOfOtapiDeliveryCost
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataListOfOtapiDeliveryCost($value);
    }
}