<?php

class ArrayOfOtapiItemPicture extends BaseOtapiType{
    /**
     * @return OtapiItemPicture[]
     */
    public function GetItemPicture(){
        return isset($this->xmlData->ItemPicture) ? new UnboundedElementsIterator(
                $this->xmlData->ItemPicture,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiItemPicture'
                )
            ) : array();
    }
}