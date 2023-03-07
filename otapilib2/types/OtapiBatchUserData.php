<?php

class OtapiBatchUserData extends OtapiBatchResultOfGeneralErrorCode{
    /**
     * @return OtapiUserStatusData
     */
    public function GetStatus(){
        $value = isset($this->xmlData->Status) ? $this->xmlData->Status : false;
        return new OtapiUserStatusData($value);
    }
    /**
     * @return OtapiCollectionInfo
     */
    public function GetNote(){
        $value = isset($this->xmlData->Note) ? $this->xmlData->Note : false;
        return new OtapiCollectionInfo($value);
    }
    /**
     * @return OtapiCollectionSummaryInfo
     */
    public function GetNoteSummary(){
        $value = isset($this->xmlData->NoteSummary) ? $this->xmlData->NoteSummary : false;
        return new OtapiCollectionSummaryInfo($value);
    }
    /**
     * @return OtapiCollectionInfo
     */
    public function GetBasket(){
        $value = isset($this->xmlData->Basket) ? $this->xmlData->Basket : false;
        return new OtapiCollectionInfo($value);
    }
    /**
     * @return OtapiCollectionSummaryInfo
     */
    public function GetBasketSummary(){
        $value = isset($this->xmlData->BasketSummary) ? $this->xmlData->BasketSummary : false;
        return new OtapiCollectionSummaryInfo($value);
    }
    /**
     * @return DataListOfOtapiCategory
     */
    public function GetSearchCategories(){
        $value = isset($this->xmlData->SearchCategories) ? $this->xmlData->SearchCategories : false;
        return new DataListOfOtapiCategory($value);
    }
    /**
     * @return OtapiCollectionInfo
     */
    public function GetFavoriteVendors(){
        $value = isset($this->xmlData->FavoriteVendors) ? $this->xmlData->FavoriteVendors : false;
        return new OtapiCollectionInfo($value);
    }
    /**
     * @return OtapiCollectionSummaryInfo
     */
    public function GetFavoriteVendorsSummary(){
        $value = isset($this->xmlData->FavoriteVendorsSummary) ? $this->xmlData->FavoriteVendorsSummary : false;
        return new OtapiCollectionSummaryInfo($value);
    }
}