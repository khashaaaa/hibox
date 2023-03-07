<?php

class OtapiDeleteMultipleOrderLinesResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetDeleteMultipleOrderLinesResult(){
        $value = isset($this->xmlData->DeleteMultipleOrderLinesResult) ? $this->xmlData->DeleteMultipleOrderLinesResult : false;
        return new VoidOtapiAnswer($value);
    }
}