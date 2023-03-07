<?php

class OtapiCategoryListAnswer extends OtapiAnswer{
    /**
     * @return DataListOfOtapiCategory
     */
    public function GetCategoryInfoList(){
        $value = isset($this->xmlData->CategoryInfoList) ? $this->xmlData->CategoryInfoList : false;
        return new DataListOfOtapiCategory($value);
    }
}