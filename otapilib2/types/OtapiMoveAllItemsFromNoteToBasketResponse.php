<?php

class OtapiMoveAllItemsFromNoteToBasketResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetMoveAllItemsFromNoteToBasketResult(){
        $value = isset($this->xmlData->MoveAllItemsFromNoteToBasketResult) ? $this->xmlData->MoveAllItemsFromNoteToBasketResult : false;
        return new VoidOtapiAnswer($value);
    }
}