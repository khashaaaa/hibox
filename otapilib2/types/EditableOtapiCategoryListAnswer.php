<?php

class EditableOtapiCategoryListAnswer extends OtapiAnswer{
    /**
     * @return DataListOfEditableOtapiCategory
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataListOfEditableOtapiCategory($value);
    }
}