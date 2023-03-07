<?php

class OtapiArrayOfContentList extends BaseOtapiType{
    /**
     * @return OtapiContentList[]
     */
    public function GetContentList(){
        return isset($this->xmlData->ContentList) ? new UnboundedElementsIterator(
                $this->xmlData->ContentList,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiContentList'
                )
            ) : array();
    }
}