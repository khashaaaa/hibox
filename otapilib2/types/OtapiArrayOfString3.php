<?php

class OtapiArrayOfString3 extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetImageUrl(){
        return isset($this->xmlData->ImageUrl) ? new UnboundedElementsIterator(
            $this->xmlData->ImageUrl,
            array(
                'type' => 'scalarType',
                'name' => 'string'
            )
        ) : array();
    }
}