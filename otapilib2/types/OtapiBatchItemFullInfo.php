<?php

class OtapiBatchItemFullInfo extends OtapiBatchResultOfGeneralErrorCode{
    /**
     * @return OtapiItemFullInfo
     */
    public function GetItem(){
        $value = isset($this->xmlData->Item) ? $this->xmlData->Item : false;
        return new OtapiItemFullInfo($value);
    }
    /**
     * @return OtapiBatchErrorInfoOfGeneralErrorCode
     */
    public function GetPromotionsError(){
        $value = isset($this->xmlData->PromotionsError) ? $this->xmlData->PromotionsError : false;
        return new OtapiBatchErrorInfoOfGeneralErrorCode($value);
    }
    /**
     * @return OtapiBatchErrorInfoOfGeneralErrorCode
     */
    public function GetDeliveryCostsError(){
        $value = isset($this->xmlData->DeliveryCostsError) ? $this->xmlData->DeliveryCostsError : false;
        return new OtapiBatchErrorInfoOfGeneralErrorCode($value);
    }
    /**
     * @return OtapiVendorInfo
     */
    public function GetVendor(){
        $value = isset($this->xmlData->Vendor) ? $this->xmlData->Vendor : false;
        return new OtapiVendorInfo($value);
    }
    /**
     * @return OtapiBatchErrorInfoOfGeneralErrorCode
     */
    public function GetVendorError(){
        $value = isset($this->xmlData->VendorError) ? $this->xmlData->VendorError : false;
        return new OtapiBatchErrorInfoOfGeneralErrorCode($value);
    }
    /**
     * @return DataListOfOtapiCategory
     */
    public function GetRootPath(){
        $value = isset($this->xmlData->RootPath) ? $this->xmlData->RootPath : false;
        return new DataListOfOtapiCategory($value);
    }
    /**
     * @return OtapiBatchErrorInfoOfGeneralErrorCode
     */
    public function GetRootPathError(){
        $value = isset($this->xmlData->RootPathError) ? $this->xmlData->RootPathError : false;
        return new OtapiBatchErrorInfoOfGeneralErrorCode($value);
    }
    /**
     * @return DataSubListOfOtapiItemInfo
     */
    public function GetVendorItems(){
        $value = isset($this->xmlData->VendorItems) ? $this->xmlData->VendorItems : false;
        return new DataSubListOfOtapiItemInfo($value);
    }
    /**
     * @return OtapiBatchErrorInfoOfGeneralErrorCode
     */
    public function GetVendorItemsError(){
        $value = isset($this->xmlData->VendorItemsError) ? $this->xmlData->VendorItemsError : false;
        return new OtapiBatchErrorInfoOfGeneralErrorCode($value);
    }
}