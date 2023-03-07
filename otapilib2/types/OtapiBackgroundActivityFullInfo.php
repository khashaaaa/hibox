<?php

class OtapiBackgroundActivityFullInfo extends OtapiBackgroundActivityInfo{
    /**
     * @return OtapiArrayOfBackgroundActivityStepInfo
     */
    public function GetSteps(){
        $value = isset($this->xmlData->Steps) ? $this->xmlData->Steps : false;
        return new OtapiArrayOfBackgroundActivityStepInfo($value);
    }
}