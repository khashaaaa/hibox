<?php

class OtapiBatchSimplifiedItemConfigurationInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiBatchSimplifiedItemConfigurationInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiBatchSimplifiedItemConfigurationInfo($value);
    }
}