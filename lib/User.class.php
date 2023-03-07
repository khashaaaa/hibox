<?php

OTBase::import('system.lib.FileAndMysqlMemoryCache');

class User
{
    private static $_object = null; // Статическая переменная, в которой мы храним экземпляр класса

    /**
     * @var CMS
     */
    protected $cms;

    /**
     * @var FileAndMysqlMemoryCache
     */
    protected $fileMysqlMemoryCache;

    /**
     * @var Boolean
     */
    private $isAuthenticated = null;

    /**
     * @var Boolean
     */
    private $isFirstVisit = false;

    /**
     * @var String
     */
    private $sid;

    /**
     * @var Array
     */
    private $registry;

    /**
     * Возвращает единственный экземпляр класса
     *
     * @return User
     */
    public static function getObject()
    {
        // проверяем актуальность экземпляра
        if (null === self::$_object) {
            // создаем новый экземпляр
            self::$_object = new self();
        }
        // возвращаем созданный или существующий экземпляр
        return self::$_object;
    }

    private function __clone()
    {
    }

    private function __construct()
    {
        $this->cms = General::getCms();
        $this->fileMysqlMemoryCache = new FileAndMysqlMemoryCache($this->cms);
        $this->defineSidAndIsAuthenticated();
    }

    private function defineSidAndIsAuthenticated()
    {
        $sid = null;
        $this->isAuthenticated = false;

        // ищем sid в сессии или из кук если пользователь выбрал "Запомнить" при входе
        $data = Session::get(self::getKeySession());
        if (! empty($data) && array_key_exists('sid', $data)) {
            $sid = $data['sid'];
        } elseif (Cookie::get(self::getKeyCookieForServiceSid())) {
            $sid = Cookie::get(self::getKeyCookieForServiceSid());
        }

        // если сессия есть - продливаем сессию
        if ($sid !== null) {
            $info = $this->getStatusInfo($sid);
            // если сессия истекла
            if ($info === false) {
                $sid = null;
            } else {
                $this->isAuthenticated = true;
                // если пользователь успешно авторизован удаляем старый sid для НЕ залогиненного покупателя
                Cookie::clear(self::getKeyCookieForNotAuthSid());
            }
        } elseif (Cookie::get(self::getKeyCookieForNotAuthSid())) {
            // проверяем наличие sid для НЕ залогиненного покупателя
            $sid = Cookie::get(self::getKeyCookieForNotAuthSid());
        }

        if ($sid === null) {
            // создаем новую сессию с чистыми данными
            $this->isFirstVisit = true;
            $sid = session_id();
            $this->sid = $sid; // заранее устаналиваем sid для очистки данных в кеше
            $this->logout();
            Cookie::set(self::getKeyCookieForNotAuthSid(), $sid, time() + (60 * 60 * 24 * 60));
        }

        $this->sid = $sid;
    }

    /*
     * Ключ для $_SESSION в котором находится sid выданное сервисом otapi при логине
     * 
     * @return string
     */
    public static function getKeySession()
    {
        return self::generateHash() . 'loginUserData';
    }

    /*
     * Ключ для $_COOKIE в котором находится sid выданное сервисом otapi при логине (если выбирали галочку "Запомнить")
     * 
     * @return string
     */
    public static function getKeyCookieForServiceSid()
    {
        return self::generateHash() . 'Auth';
    }

    /*
     * Ключ для $_COOKIE в котором находится sid для НЕ залогиненного покупателя
     * 
     * @return string
     */
    public static function getKeyCookieForNotAuthSid()
    {
        return self::generateHash() . 'ServiceAuth';
    }

    private static function generateHash()
    {
        return str_replace(array(':8080', ' ', ',', ';', '=', '.'), '', $_SERVER['HTTP_HOST']);
    }

    /*
     * @return String
     */
    public function getSid()
    {
        return $this->sid;
    }

    /*
     * @return Boolean
     */
    public function isAuthenticated()
    {
        return $this->isAuthenticated;
    }

    /*
     * @return String
     */
    public function getLogin()
    {
        if (! $this->isAuthenticated()) {
            return null;
        }

        $info = $this->getStatusInfo($this->sid);
        return $info->GetLogin();
    }
    
    public function getEmail()
    {
    	if (! $this->isAuthenticated()) {
    		return null;
    	}
    
    	$info = $this->getUserInfo();
    	
    	return $info->GetEmail();
    }
    
    public function getFirstName()
    {
    	if (! $this->isAuthenticated()) {
    		return null;
    	}
    	$info = $this->getUserInfo();
    	return $info->GetFirstName();
    }
    
    public function getLastName()
    {
    	if (! $this->isAuthenticated()) {
    		return null;
    	}
    	$info = $this->getUserInfo();
    	return $info->GetLastName();
    }
    
    public function getMiddleName()
    {
    	if (! $this->isAuthenticated()) {
    		return null;
    	}
    	$info = $this->getUserInfo();
    	return $info->GetMiddleName();
    }
    
    public function getFIO()
    {
    	if (! $this->isAuthenticated()) {
    		return null;
    	}
    	$fio = array();
    	$tmp = $this->getFirstName();
    	if (! empty($tmp) ) {
    		$fio[] = $tmp;
    	}
    	$tmp = $this->getMiddleName();
        if (! empty($tmp) ) {
    		$fio[] = $tmp;
    	}
    	$tmp = $this->getLastName();
    	if (! empty($tmp) ) {
    		$fio[] = $tmp;
    	}
    	return implode(' ', $fio);
    }
    

    /*
     * @return String
     */
    public function getId()
    {
        if (! $this->isAuthenticated()) {
            return null;
        }

        $info = $this->getStatusInfo($this->sid);
        return $info->GetRawData()->Id;
    }

    /*
     * @return mixed если пользователь авторизован вернет OtapiUserStatusInfo иначе false
     */
    private function getStatusInfo($sid)
    {
        $cacheKey = "getUserStatus:" . $sid;
        if (! empty($this->registry[$cacheKey])) {
            return $this->registry[$cacheKey];
        }

        $userStatus = false;
        try {
            OTAPILib2::GetUserStatusInfo(Session::getActiveLang(), $sid, $userStatus);
            OTAPILib2::makeRequests();
            $userStatus = ($userStatus) ? $userStatus->GetUserStatusInfo() : false;
        } catch (ServiceException $e) {
            // если сессия истекла
            if ($e->getErrorCode() == 'SessionExpired') {
                $userStatus = false;
            } else {
                throw $e;
            }
        }

        $this->registry[$cacheKey] = $userStatus;
        return $userStatus;
    }

    /*
     * Получить информацию о лицевом счете. Информация кешируется по логину пользователя
     * 
     * @return mixed если isAuthenticated то OtapiAccountInfo, иначе null
     */
    public function getAccountInfo()
    {
        if (! $this->isAuthenticated()) {
            return null;
        }

        $cacheKey = "GetAccountInfo:" . $this->getLogin();
        if (! empty($this->registry[$cacheKey])) {
            return $this->registry[$cacheKey];
        }

        if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
            $cache = simplexml_load_string(json_decode($this->fileMysqlMemoryCache->GetCacheEl($cacheKey), true));
            $accountData = new OtapiAccountInfo($cache);
            $this->registry[$cacheKey] = $accountData;
        } else {
            OTAPILib2::GetAccountInfo(Session::getActiveLang(), $this->sid, $accountData);
            OTAPILib2::makeRequests();
            if ($accountData && $accountData->GetResult()) {
                $accountData = $accountData->GetResult();
                $this->fileMysqlMemoryCache->AddCacheEl($cacheKey, 600, json_encode($accountData->asXML()));
            }
            $this->registry[$cacheKey] = $accountData;
        }

        return $accountData;
    }

    public function getDiscountGroupName() 
    {
        if (! $this->isAuthenticated()) {
            return null;
        }

        $cacheKey = "GetDiscountGroup:" . $this->getLogin();
        if (! empty($this->registry[$cacheKey])) {
            return $this->registry[$cacheKey];
        }
        
        if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
            $cache = simplexml_load_string(json_decode($this->fileMysqlMemoryCache->GetCacheEl($cacheKey), true));
            $discountData = new OtapiDiscountGroupInfo($cache);
            $this->registry[$cacheKey] = $discountData;
        } else {
            OTAPILib2::GetDiscountGroup($this->sid, $discountData);
            OTAPILib2::makeRequests();
            if ($discountData && $discountData->GetResult()) {
                $discountData = $discountData->GetResult();
                $this->fileMysqlMemoryCache->AddCacheEl($cacheKey, 3600, json_encode($discountData->asXML()));
            }
            $this->registry[$cacheKey] = $discountData;
        }
        if ($discountData->GetName()) {
            return $discountData->GetName();
        }
        return null;
    }

    public function clearAccountInfoCache()
    {
        if (! $this->isAuthenticated()) {
            return null;
        }
    
        $this->clearCache('GetAccountInfo:'.$this->getLogin());
    }
    
    
    public function clearDiscountDataCache()
    {
        if (! $this->isAuthenticated()) {
            return null;
        }

        $this->clearCache('GetDiscountGroup:'.$this->getLogin());
    }

    public function clearAccountInfoCacheByLogin($login)
    {
        $this->clearCache('GetAccountInfo:'.$login);
    }

    /*
     * @return string
     */
    public function getAvailableAmountAsText()
    {
        if (! $this->isAuthenticated()) {
            return null;
        }

        return TextHelper::formatPrice(
            $this->getAccountInfo()->GetAvailableAmount(),
            $this->getAccountInfo()->GetCurrencySign()
        );
    }

    /*
     * @return int
     */
    public function getCountInNote()
    {
        return $this->getBatchSummaryData()->GetNoteSummary()->GetTotalCount();
    }

    /*
     * @return int
     */
    public function getCountInBasket()
    {
        return $this->getBatchSummaryData()->GetBasketSummary()->GetTotalCount();
    }

    /*
     * @return string
     */
    public function getTotalPriceInNote()
    {
        if ($this->getCountInNote() != 0) {
            $price = $this->getBatchSummaryData()->GetNoteSummary()->GetTotalPrice()->GetConvertedPriceList()->GetInternal()->asString();
            $sign = $this->getBatchSummaryData()->GetNoteSummary()->GetTotalPrice()->GetConvertedPriceList()->GetInternal()->GetSignAttribute();
        } else {
            // TODO: вывести валюту выбранную юзером
            $price = 0;
            $sign = '';
        }
        return TextHelper::formatPrice($price, $sign);
    }

    /*
     * @return string
     */
    public function getTotalPriceInBasket()
    {
        if ($this->getCountInBasket() != 0) {
            $price = $this->getBatchSummaryData()->GetBasketSummary()->GetTotalPrice()->GetConvertedPriceList()->GetInternal()->asString();
            $sign = $this->getBatchSummaryData()->GetBasketSummary()->GetTotalPrice()->GetConvertedPriceList()->GetInternal()->GetSignAttribute();
        } else {
            // TODO: вывести валюту выбранную юзером
            $price = 0;
            $sign = '';
        }
        return TextHelper::formatPrice($price, $sign);
    }

    /*
    * @return OtapiBatchUserData
    */
    public function getBatchSummaryData()
    {
        $cacheKey = "getBatchSummaryData:" . $this->sid;
        if (! empty($this->registry[$cacheKey])) {
            return $this->registry[$cacheKey];
        }

        if (!$this->isFirstVisit && $this->fileMysqlMemoryCache->Exists($cacheKey)) {
            $cache = simplexml_load_string(json_decode($this->fileMysqlMemoryCache->GetCacheEl($cacheKey), true));
            $userData = new OtapiBatchUserData($cache);
            $this->registry[$cacheKey] = $userData;
        } else {
            OTAPILib2::BatchGetUserData(Session::getActiveLang(), $this->sid, 'BasketSummary,NoteSummary', $userData);
            OTAPILib2::makeRequests();
            if ($userData && $userData->GetResult()) {
                $userData = $userData->GetResult();
                if (!$this->isFirstVisit) {
                    $this->fileMysqlMemoryCache->AddCacheEl($cacheKey, 3600, json_encode($userData->asXML()));
                }
            }
            $this->registry[$cacheKey] = $userData;
        }

        return $userData;
    }

    public function clearUserDataCache()
    {
        $this->clearCache('getBatchSummaryData:' . $this->sid);
        $this->clearCache('BatchGetUserData:' . $this->sid);
    }

    public function logout()
    {
        // clear cache
        $this->clearAccountInfoCache();
        $this->clearUserDataCache();
        $this->clearDiscountDataCache();

        Cookie::clear(self::getKeyCookieForServiceSid());
        Cookie::clear(self::getKeyCookieForNotAuthSid());
        Session::clear(self::getKeySession());
    }

    private function clearCache($cacheKey)
    {
        $this->fileMysqlMemoryCache->DelCacheEl($cacheKey);
        unset($this->registry[$cacheKey]);
    }

    public function getProvider()
    {
        if (Cookie::get('Provider')) {
            return Cookie::get('Provider');
        }

        $defaultProviderType = InstanceProvider::getObject()->getDefaultProviderType(Session::getActiveLang());
        $default = InstanceProvider::getObject()->GetAliasByProviderName(Session::getActiveLang(), $defaultProviderType);
        Cookie::set('Provider', $default);
        return $default;
    }

    public function setProvider($providerAlias)
    {
        Cookie::set('Provider', $providerAlias);
    }

    public function getReferralKey($id = null, $login = null)
    {
        $id = (! is_null($id)) ? $id : $this->getId();
        $login = (! is_null($login)) ? $login : $this->getLogin();

        return ReferalSystem::generateReferralKey($id, $login);
    }

    /**
     * Возвращает реферальную ссылку
     * для пользователя с указанными
     * id и login
     *
     * @param null $id id пользователя
     * @param null $login login пользователя
     * @return string реферальная ссылка
     */
    public function getReferralUrl($id = null, $login = null)
    {
        $referralKey = $this->getReferralKey($id, $login);
        $referrerPrefix = ReferalSystem::getReferrerKey();

        if (CMS::IsFeatureEnabled('Seo2')) {
            $referalUrl = UrlGenerator::getProtocol() . '://'.IDN::decodeIDN($_SERVER['HTTP_HOST']) . '/register?' . $referrerPrefix . '=' . $referralKey;
        } else {
            $referalUrl = UrlGenerator::getProtocol() . '://'.IDN::decodeIDN($_SERVER['HTTP_HOST']) . '/?p=register&' . $referrerPrefix . '=' . $referralKey;
        }

        return $referalUrl;
    }

    /*
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->getUserPreferences()->GetCurrencyCode();
    }

    /*
     * @return string
     */
    public function getCurrencySign()
    {
        return $this->getUserPreferences()->GetCurrencySign();
    }

    /*
     * @return string
     */
    public function getCountryCode()
    {
        return $this->getUserPreferences()->GetCountryCode();
    }

    /*
     * @return string
     */
    public function getCountryName()
    {
        return $this->getUserPreferences()->GetCountryName();
    }
    
    /*
     * @return string
     */
    public function getActiveLang()
    {
    	$language = $this->getUserPreferences()->GetLanguage();
    	if ($language) {
    		Session::setActiveLang($language);
    		return $language;
    	} else {
    		return Session::getActiveLang();
    	}
    }

    /*
     * @return string
     */
    public function getCountryFlagImageUrl()
    {
        return $this->getUserPreferences()->GetCountryFlagImageUrl();
    }

    public function setExternalDeliveryId($deliveryId) {
    	try { 
	    	$result = false;
	    	$xmlUpdate = '<UserPreferencesUpdateData>';
	    	$xmlUpdate .= '<ExternalDeliveryId>' . $deliveryId . '</ExternalDeliveryId>';
	    	$xmlUpdate .= '</UserPreferencesUpdateData>';
	    	OTAPILib2::UpdateUserPreferences(Session::getActiveLang(), $this->sid, $xmlUpdate, $result);
	    	OTAPILib2::makeRequests();
	    	$this->clearUserPreferencesCache();
    	} catch (Exception $e) {
    		ErrorHandler::registerError($e);
    	}
    }
    
    public function setActiveLang($language) {
    	try {
    		$result = false;
    		$xmlUpdate = '<UserPreferencesUpdateData>';
    		$xmlUpdate .= '<Language>' . $language . '</Language>';
    		$xmlUpdate .= '</UserPreferencesUpdateData>';
    		OTAPILib2::UpdateUserPreferences(Session::getActiveLang(), $this->sid, $xmlUpdate, $result);
    		OTAPILib2::makeRequests();
    		$this->clearUserPreferencesCache();
    	} catch (Exception $e) {
    		ErrorHandler::registerError($e);
    	}
    }
    
    /*
    * @return OtapiUserPreferences
    */
    public function getUserPreferences()
    {
        $cacheKey = "getUserPreferences:" . $this->sid;
        if (! empty($this->registry[$cacheKey])) {
            return $this->registry[$cacheKey];
        }

        $data = new OtapiUserPreferences(null);
        if (!$this->isFirstVisit && $this->fileMysqlMemoryCache->Exists($cacheKey)) {
            $cache = simplexml_load_string(json_decode($this->fileMysqlMemoryCache->GetCacheEl($cacheKey), true));
            $data = new OtapiUserPreferences($cache);
            $this->registry[$cacheKey] = $data;
        } else {
            try {
                OTAPILib2::GetUserPreferences(Session::getActiveLang(), $this->sid, $data);
                OTAPILib2::makeRequests();
                $data = $data->GetResult();
                if (!$this->isFirstVisit) {
                    $this->fileMysqlMemoryCache->AddCacheEl($cacheKey, 3600, json_encode($data->asXML()));
                }
            } catch (Exception $e) {
                ErrorHandler::registerError($e);
            }

            $this->registry[$cacheKey] = $data;
        }

        return $data;
    }

    /*
     * @return OtapiUserPreferences
     */
    public function getUserInfo()
    {
    	$cacheKey = "getUserInfo:" . $this->sid;
    	if (! empty($this->registry[$cacheKey])) {
    		return $this->registry[$cacheKey];
    	}
    
    	$data = new OtapiUserInfo(null);
    	if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
    		$cache = simplexml_load_string(json_decode($this->fileMysqlMemoryCache->GetCacheEl($cacheKey), true));
    		$data = new OtapiUserInfo($cache);
    		$this->registry[$cacheKey] = $data;
    	} else {
    		try {
    			OTAPILib2::getUserInfo($this->sid, $data);
    			OTAPILib2::makeRequests();
    		} catch (Exception $e) {
    			ErrorHandler::registerError($e);
    		}
    
    		if ($data && $data->GetUserInfo()) {
    			$data = $data->GetUserInfo();
    			$this->fileMysqlMemoryCache->AddCacheEl($cacheKey, 3600, json_encode($data->asXML()));
    		}
    		$this->registry[$cacheKey] = $data;
    	}
    
    	return $data;
    }

    public function clearUserPreferencesCache()
    {
        $cacheKey = "getUserPreferences:" . $this->sid;
        $this->clearCache($cacheKey);
    }

    /**
     * @return OtapiDiscountGroupInfoAnswer
     */
    public function getDiscountGroup()
    {
        OTAPILib2::GetDiscountGroup($this->getSid(), $userDiscount);
        OTAPILib2::makeRequests();
        return $userDiscount;
    }
}
