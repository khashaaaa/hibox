<?php

class OtapiArrayOfCategoryImportInfo extends BaseOtapiType{
    /**
     * @return OtapiCategoryImportInfo[]
     */
    public function GetCategoryImportInfo(){
        return isset($this->xmlData->CategoryImportInfo) ? new UnboundedElementsIterator(
                $this->xmlData->CategoryImportInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiCategoryImportInfo'
                )
            ) : array();
    }
}