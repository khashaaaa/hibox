<?php

class OtapiCategoryListFrameAnswer extends OtapiAnswer{
    /**
     * @return DataSubListOfOtapiCategory
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataSubListOfOtapiCategory($value);
    }
}