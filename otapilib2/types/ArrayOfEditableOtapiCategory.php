<?php

class ArrayOfEditableOtapiCategory extends BaseOtapiType{
    /**
     * @return EditableOtapiCategory[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'EditableOtapiCategory'
                )
            ) : array();
    }
}