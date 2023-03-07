<?php
class UserData {
    /**
     * @var OTAPIlib
     */
    protected $otapilib;

    /**
     * @var FileAndMysqlMemoryCache
     */
    protected $fileMysqlMemoryCache;

    public function __construct()
    {
        $this->otapilib = new OTAPIlib();
        $this->otapilib->setErrorsAsExceptionsOn();

        $cms = new CMS();
        $cms->Check();
        $this->fileMysqlMemoryCache = new FileAndMysqlMemoryCache($cms);
    }

    public function getUserData()
    {
        $userFullInfo = array();
		$userFullInfo['UserData'] = $this->assignBatchGetUserData();        
		$userFullInfo['AccountData'] = $this->assignGetAccountInfo($userFullInfo['UserData']['Status']);
		$userFullInfo['DiscountGroup'] = $this->assignDiscountGroup();
		return $userFullInfo;
    }

    public function BatchGetUserDataDecorator()
    {
        $blocks = 'UserStatus,BasketSummary,NoteSummary';        
        $sessionId = Session::getUserOrGuestSession();
        return $this->otapilib->BatchGetUserData($sessionId, $blocks);
    }
	
	public function assignBatchGetUserData()
    {
        $sessionId = Session::getUserOrGuestSession();		
        if($this->fileMysqlMemoryCache->Exists('BatchGetUserData:'.$sessionId)) {
            $userDataXMLRow = $this->fileMysqlMemoryCache->GetCacheEl('BatchGetUserData:'.$sessionId);
        } else{
            $this->otapilib->setResultInXMLOn();
            $userData = $this->BatchGetUserDataDecorator();
            if (empty($userData)) {
                return false;
            }
            $userDataXMLRow = $userData->asXML();
            $this->otapilib->setResultInXMLOff();
            $lifeTime = defined('CFG_OTAPI_CACHE_LIFETIME') ? CFG_OTAPI_CACHE_LIFETIME : 600;
            $this->fileMysqlMemoryCache->AddCacheEl('BatchGetUserData:'.$sessionId, $lifeTime, $userDataXMLRow);
        }
		return $this->otapilib->BatchGetUserData('', '', $userDataXMLRow);
    }
    
    public function assignDiscountGroup() 
    {
        $sessionId = Session::getUserOrGuestSession();
        if($this->fileMysqlMemoryCache->Exists('GetDiscountGroup:'.$sessionId)) {
            $discountGroupXMLRow = $this->fileMysqlMemoryCache->GetCacheEl('GetDiscountGroup:'.$sessionId);
        } else{
            $this->otapilib->setResultInXMLOn();
            $discountGroup = $this->otapilib->GetDiscountGroup($sessionId);
            if (empty($discountGroup)) {
                return false;
            }
            $discountGroupXMLRow = $discountGroup->asXML();
            $this->otapilib->setResultInXMLOff();
            $lifeTime = defined('CFG_OTAPI_CACHE_LIFETIME') ? CFG_OTAPI_CACHE_LIFETIME : 600;
            $this->fileMysqlMemoryCache->AddCacheEl('GetDiscountGroup:'.$sessionId, $lifeTime, $discountGroupXMLRow);
        }
        return $this->otapilib->GetDiscountGroup($sessionId, $discountGroupXMLRow);
    }
    
    public function assignBatchGetBasketNoteVendorsData()
    {
        $sessionId = Session::getUserOrGuestSession();		
        if($this->fileMysqlMemoryCache->Exists('BatchGetBasketNoteVendorData:'.$sessionId)) {
            $basketNoteDataVendorXMLRow = $this->fileMysqlMemoryCache->GetCacheEl('BatchGetBasketNoteVendorData:'.$sessionId);
        } else {
            $this->otapilib->setResultInXMLOn();
            $basketNoteVendorData =  $this->otapilib->BatchGetUserData($sessionId, 'Basket,Note,FavoriteVendors');
            $basketNoteDataVendorXMLRow = $basketNoteVendorData->asXML();
            $this->otapilib->setResultInXMLOff();
            $lifeTime = defined('CFG_OTAPI_CACHE_LIFETIME') ? CFG_OTAPI_CACHE_LIFETIME : 600;
            $this->fileMysqlMemoryCache->AddCacheEl('BatchGetBasketNoteVendorData:'.$sessionId, $lifeTime, $basketNoteDataVendorXMLRow);
        }
		return $this->otapilib->BatchGetUserData('', '', $basketNoteDataVendorXMLRow);
    }
    
    public function getUserStatus()
    {        
        $isAccount = true;
        $this->otapilib->setErrorsAsExceptionsOn();
        try {
            $userStatus = $this->GetUserStatusInfoDecorator();            
        } catch(ServiceException $e){
            if ($e->getErrorCode() == 'SessionExpired') {                
                Session::clearUserData();
                $isAccount = false;
            }
        } catch(Exception $e){
            if ($e->getCode() ==  -1) {
                $isAccount = false;
            }
        }
        if ($isAccount) {
            Session::set(Session::getHttpHost() . 'isMayAuthenticated', true);
        }
        return array(
            'isAccount' => $isAccount,
            'userStatus' => isset($userStatus) ? $userStatus : ''
        );
    }

    private function assignGetAccountInfo($userStatus)
    {
    	if (User::getObject()->isAuthenticated()) {
    		Session::set(Session::getHttpHost() . 'isMayAuthenticated', true);
    		$xml = User::getObject()->getAccountInfo()->asXML();
    		$xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $xml);
    		$xml = str_replace('<?xml version="1.0"?>', '', $xml);
    		$xml = '<AccountInfoAnswer>' . $xml . '</AccountInfoAnswer>';
    		return array(
    			'accountInfo' => $this->otapilib->GetAccountInfo('', $xml),
				'userStatus' => $userStatus
    		);
        } else {
            Session::clear(Session::getHttpHost() . 'isMayAuthenticated');
            return '';
        }
    }
    
    private function GetUserStatusInfoDecorator()
    {
        $sessionId = Session::getUserOrGuestSession();        
        return $this->otapilib->GetUserStatusInfo($sessionId);
    }
    
    private function GetAccountInfoDecorator()
    {
        $sessionId = Session::getUserOrGuestSession();
        return $this->otapilib->GetAccountInfo($sessionId);        
    }

    public function ClearUserDataCache()
    {
        $sessionId = Session::getUserOrGuestSession();
        $this->fileMysqlMemoryCache->DelCacheEl('BatchGetUserData:'.$sessionId);
        $this->fileMysqlMemoryCache->DelCacheEl('BatchGetBasketNoteVendorData:'.$sessionId);
        $this->fileMysqlMemoryCache->DelCacheEl('GetDiscountGroup:'.$sessionId);
        User::getObject()->clearUserDataCache();
    }

    public function ClearAccountInfoCache()
    {
        $userStatus = $this->getUserStatus();
        if ($userStatus['isAccount']) {            
            $keyCache = 'GetAccountInfo:' . $userStatus['userStatus']['login'];
            $this->fileMysqlMemoryCache->DelCacheEl($keyCache);
        }
        User::getObject()->clearAccountInfoCache();
    }

    public function ClearAccountInfoCacheByLogin($login)
    {
        $keyCache = 'GetAccountInfo:' . $login;
        $this->fileMysqlMemoryCache->DelCacheEl($keyCache);
        User::getObject()->clearAccountInfoCacheByLogin($login);
    }
}