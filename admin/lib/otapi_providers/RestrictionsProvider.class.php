<?php

class RestrictionsProvider
{    
    /**
     * @var contentTypes
     */
    private $contentTypes = null;
   
    /**
     * @var OtapiContentListListAnswer blackListContents
     */
    private $blackListContents = null;
    
    /**
     * @var blackListContentsEditedResult
     */
    private $blackListContentsEditedResult = null;
   
   /**
     * @var itemInfoListResult
     */
    private $itemInfoList = null;
   
   /**
     * @var categoryInfoResult
     */
    private $categoryInfo = null;
   
   /**
     * @var brandInfoResult
     */
    private $brandInfo = null;
   
    /**
     * @return ContentTypes
     */
    public function getContentTypes()
    {
        return $this->contentTypes;
    }
    
    /**
     * @return OtapiContentListListAnswer
     */
    public function getBlackListContents()
    {
        return $this->blackListContents;
    }
    
    /**
     * @return blackListContentsEditedResult
     */
    public function getBlackListContentsEditResult()
    {
        return $this->blackListContentsEditedResult;
    }    
    
    /**
     * @return itemInfoListResult
     */
    public function getItemInfoListResult()
    {
        return $this->itemInfoList;
    }  
    
    /**
     * @return categoryInfoResult
     */
    public function getCategoryInfoResult()
    {
        return $this->categoryInfo;
    } 

    /**
     * @return categoryInfoResult
     */
    public function getGetBrandInfoResult()
    {
        return $this->brandInfo;
    } 
    
    /**
    * @var $answers GetContentTypes
    */
    public function initGetContentTypes()
    {
        OTAPILib2::GetContentTypes(Session::get('sid'), $this->contentTypes);
    }
    
    /**
    * @var $answers blackListContents
    */
    public function initGetBlackListContents()
    {
        OTAPILib2::GetBlackListContents(Session::get('sid'), $this->blackListContents);
    }
    
    /**
    * @var $answers blackListContentsEditedResult
    */
    public function initEditBlackListContents($xmlContentsList)
    {
        OTAPILib2::EditBlackListContents(Session::get('sid'), $xmlContentsList, $this->blackListContentsEditedResult);
    }
    
    /**
     * @var $answers AddBlackListContents
     */
    public function AddBlackListContents($xmlContentsList)
    {
        $result = false;
        OTAPILib2::AddBlackListContents(Session::get('sid'), $xmlContentsList, $result);
    }

    /**
     * @var $answers AddBlackListContents
     */
    public function DeleteBlackListContents($xmlContentsList)
    {
        OTAPILib2::DeleteBlackListContents(Session::getActiveAdminLang(), Session::get('sid'), $xmlContentsList, $result);
    }
    
    /**
    * @var $answers GetItemInfoListResult
    */
    public function initGetItemInfoList($idsList)
    {
        OTAPILib2::GetItemInfoList(Session::getActiveAdminLang(), $idsList, $this->itemInfoList);
    }
    
    /**
    * @var $answers GetCategoryInfotResult
    */
    public function initGetCategoryInfo($cid)
    {
        OTAPILib2::GetCategoryInfo(Session::getActiveAdminLang(), $cid, $this->categoryInfo);
    }
    
    /**
    * @var $answers GetBrandInfoResult
    */
    public function initGetBrandInfo($id)
    {
        OTAPILib2::GetBrandInfo(Session::getActiveAdminLang(), $id, $this->brandInfo);
    }
    
    public function doRequests()
    {
        OTAPILib2::makeRequests();
    }
}