<?php

class OtapiDemoObject extends BaseOtapiType{
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
    /**
     * @return OtapiNumericDemoObject
     */
    public function GetNumerics(){
        $value = isset($this->xmlData->Numerics) ? $this->xmlData->Numerics : false;
        return new OtapiNumericDemoObject($value);
    }
    /**
     * @return OtapiTextDemoObject
     */
    public function GetTexts(){
        $value = isset($this->xmlData->Texts) ? $this->xmlData->Texts : false;
        return new OtapiTextDemoObject($value);
    }
    /**
     * @return OtapiMultilineTextDemoObject
     */
    public function GetMultilineTexts(){
        $value = isset($this->xmlData->MultilineTexts) ? $this->xmlData->MultilineTexts : false;
        return new OtapiMultilineTextDemoObject($value);
    }
    /**
     * @return OtapiHtmlDemoObject
     */
    public function GetHtmls(){
        $value = isset($this->xmlData->Htmls) ? $this->xmlData->Htmls : false;
        return new OtapiHtmlDemoObject($value);
    }
    /**
     * @return OtapiMoneyDemoObject
     */
    public function GetMoneys(){
        $value = isset($this->xmlData->Moneys) ? $this->xmlData->Moneys : false;
        return new OtapiMoneyDemoObject($value);
    }
    /**
     * @return OtapiLinkDemoObject
     */
    public function GetLinks(){
        $value = isset($this->xmlData->Links) ? $this->xmlData->Links : false;
        return new OtapiLinkDemoObject($value);
    }
    /**
     * @return OtapiDateDemoObject
     */
    public function GetDates(){
        $value = isset($this->xmlData->Dates) ? $this->xmlData->Dates : false;
        return new OtapiDateDemoObject($value);
    }
    /**
     * @return OtapiManyTextsDemoObject
     */
    public function GetManyTexts(){
        $value = isset($this->xmlData->ManyTexts) ? $this->xmlData->ManyTexts : false;
        return new OtapiManyTextsDemoObject($value);
    }
    /**
     * @return OtapiSelectOneDemoObject
     */
    public function GetSelectOnes(){
        $value = isset($this->xmlData->SelectOnes) ? $this->xmlData->SelectOnes : false;
        return new OtapiSelectOneDemoObject($value);
    }
    /**
     * @return OtapiSelectManyDemoObject
     */
    public function GetSelectManys(){
        $value = isset($this->xmlData->SelectManys) ? $this->xmlData->SelectManys : false;
        return new OtapiSelectManyDemoObject($value);
    }
    /**
     * @return OtapiSelectTreeDemoObject
     */
    public function GetSelectTrees(){
        $value = isset($this->xmlData->SelectTrees) ? $this->xmlData->SelectTrees : false;
        return new OtapiSelectTreeDemoObject($value);
    }
    /**
     * @return OtapiPasswordDemoObject
     */
    public function GetPasswords(){
        $value = isset($this->xmlData->Passwords) ? $this->xmlData->Passwords : false;
        return new OtapiPasswordDemoObject($value);
    }
    /**
     * @return OtapiListDemoObject
     */
    public function GetLists(){
        $value = isset($this->xmlData->Lists) ? $this->xmlData->Lists : false;
        return new OtapiListDemoObject($value);
    }
}