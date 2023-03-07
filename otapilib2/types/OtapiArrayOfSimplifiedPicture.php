<?php

class OtapiArrayOfSimplifiedPicture extends BaseOtapiType{
    /**
     * @return OtapiSimplifiedPicture[]
     */
    public function GetPicture(){
        return isset($this->xmlData->Picture) ? new UnboundedElementsIterator(
                $this->xmlData->Picture,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSimplifiedPicture'
                )
            ) : array();
    }
}