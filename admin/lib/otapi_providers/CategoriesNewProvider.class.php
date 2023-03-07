<?php

class CategoriesNewProvider 
{
    /**
     * @var providerSearchMethodInfoList
     */
    private $providerSearchMethodInfoList = null;
    
    /**
     * @var answerEditCategoryInfo
     */
    private $answerEditCategoryInfo = null;
    
    /**
     * @var answerAddCategoryInfo
     */
    private $answerAddCategoryInfo = null;
    
    /**
     * @return providerSearchMethodInfoList
     */
    public function getProviderSearchMethodInfoList()
    {
        return $this->providerSearchMethodInfoList;
    }
    
    /**
     * @return providerSearchMethodInfoList
     */
    public function getAnswerEditCategoryInfo()
    {
        return $this->answerEditCategoryInfo;
    }
    
    /**
     * @return answerAddCategoryInfo
     */
    public function getAnswerAddCategoryInfo()
    {
        return $this->answerAddCategoryInfo;
    }
    
    /**
    * @var $answers providerSearchMethodInfoList
    */
    public function initGetProviderSearchMethodInfoList($language)
    {
        OTAPILib2::GetProviderSearchMethodInfoList($language, $this->providerSearchMethodInfoList);
    }
    
    /**
    * @var $answers answerAddCategoryInfo
    */
    public function initAddCategoryInfo($language, $inputLanguage, $xmlCategoryInfo)
    {
        OTAPILib2::AddCategoryInfo(Session::get('sid'), $language, $inputLanguage, $xmlCategoryInfo, $this->answerAddCategoryInfo);
    }
    
    /**
    * @var $answers answerEditCategoryInfo
    */
    public function initEditCategoryInfo($language, $inputLanguage, $xmlCategoryInfo)
    {
        OTAPILib2::EditCategoryInfo(Session::get('sid'), $language, $inputLanguage, $xmlCategoryInfo, $this->answerEditCategoryInfo);
    }
    

    
    public function doRequests()
    {
        OTAPILib2::makeRequests();
    }

    public function MoveCategories($language, $sessionId, $categoryIds, $parentCategoryId, $index)
    {
        OTAPILib2::MoveCategories($language, $sessionId, $categoryIds, $parentCategoryId, $index, $request);
        OTAPILib2::makeRequests();

        return $request;
    }
}

