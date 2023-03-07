<?php

OTBase::import('system.lib.FileAndMysqlMemoryCache');

class InstanceProvider
{
    private static $_object = null; // Статическая переменная, в которой мы храним экземпляр класса
    private static $social_networks = ['VKontakte', 'Facebook', 'Odnoklassniki', 'Twitter', 'Instagram'];
    const PROVIDER_TYPE_WAREHOUSE = 'Warehouse';
    const PROVIDER_TYPE_TAOBAO  = 'Taobao';

    /**
     * @var CMS
     */
    protected $cms;

    /**
     * @var FileAndMysqlMemoryCache
     */
    protected $fileMysqlMemoryCache;

    private $registry;

    private function __construct()
    {
        $this->cms = new CMS();
        $this->cms->Check();

        $this->fileMysqlMemoryCache = new FileAndMysqlMemoryCache($this->cms);
    }

    private function __clone()
    {
    }

    /**
     * Возвращает единственный экземпляр класса
     *
     * @return InstanceProvider
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

    private function clearCache($cacheKey)
    {
        $this->fileMysqlMemoryCache->DelCacheEl($cacheKey);
        unset($this->registry[$cacheKey]);
    }

    public function GetProviderInfo($lang, $providerType)
    {
        $providerInfoList = $this->GetProviderInfoList($lang);

        foreach ($providerInfoList->GetItem() as $item) {
            if ($item->GetType() == $providerType) {
                return $item;
            }
        }

        return new OtapiProviderInfo(null);
    }

    public function getAvailableProviders($lang)
    {
        $providers = array();
        $providerInfoList = $this->GetProviderInfoList($lang);

        foreach ($providerInfoList->GetItem() as $item) {
            if ($item->IsEnabled()) {
                $providers[] = $item;
            }
        }

        return $providers;
    }

    /*
     * получить активных провайдеров для которых включена интеграция заказов
     */
    public function getAvailableAndOrdersIntegrationEnabledProviders($lang)
    {
        $providers = array();
        $providersList = $this->getAvailableProviders($lang);

        foreach ($providersList as $item) {
            if ($item->GetOrdersIntegration()->IsEnabled()) {
                $providers[] = $item;
            }
        }

        return $providers;
    }

    /*
     * @return string
     */
    public function getDefaultProviderType($lang)
    {
        $providers = $this->getAvailableProviders($lang);

        // возвращаем ид первого доступного провайдера
        foreach ($providers as $provider) {
            return $provider->GetType();
        }

        return self::PROVIDER_TYPE_TAOBAO ;
    }

    public function GetAliasByProviderName($lang, $providerType)
    {
        $provider = self::GetProviderInfo($lang, $providerType);

        return $provider->GetAlias();
    }

    public function GetProviderNameByAlias($lang, $providerAlias)
    {
        $providerInfoList = $this->GetProviderInfoList($lang);

        foreach ($providerInfoList->GetItem() as $item) {
            if ($item->GetAlias() == $providerAlias) {
                return $item->GetType();
            }
        }

        return $providerAlias;
    }

    public function GetProfileFieldState($id) {
        $options = $this->GetCommonInstanceOptionsInfo(Session::getActiveLang());
        $item = $options->GetTranslatableOptions()->GetContent()->GetItem();

        foreach ($item as $value) {
            foreach ($value->GetUserProfileFields()->GetContent()->GetItem() as $value2) {
                if ($value2->GetIdAttribute() === $id) {
                    return (string)$value2->GetState();
                }
            }
        }
        return null;
    }

    public function GetProfileFieldDisplayName($id) {
        $lang = Session::getActiveLang();
        $options = $this->GetCommonInstanceOptionsInfo($lang);
        $item = $options->GetTranslatableOptions()->GetContent()->GetItem();

        foreach ($item as $value) {
            if ($value->GetLanguageAttribute() === $lang) {
                foreach ($value->GetUserProfileFields()->GetContent()->GetItem() as $value2) {
                    if ($value2->GetIdAttribute() === $id) {
                        return (string)$value2->GetDisplayNameAttribute();
                    }
                }
            }
        }
        return null;
    }

    public function checkOrdersIntegrationIsEnabled($lang, $providerType)
    {
        $provider = $this->GetProviderInfo($lang, $providerType);
        return $provider ? $provider->GetOrdersIntegration()->IsEnabled() : false;
    }

    public function isSellFree($lang)
    {
        $options = $this->GetCommonInstanceOptionsInfo($lang);
        return $options ? $options->GetInstanceKey()->IsSellFree() : false;
    }

    public function getDefaultAdminPanelLanguage($lang)
    {
        $options = $this->GetCommonInstanceOptionsInfo($lang);
        return $options ? $options->GetInstanceKey()->GetDefaultAdminPanelLanguage() : false;
    }

    /**
     * Получить общие настройки инстанса без
     * учета существования кэша
     *
     * @param $lang
     * @return OtapiCommonInstanceOptionsInfo
     * @throws Exception
     */
    public function updateCommonInstanceOptionsInfo($lang)
    {
        $cacheKey = "GetCommonInstanceOptionsInfo:Result";

        OTAPILib2::GetCommonInstanceOptionsInfo($lang, $instanceOptions);
        OTAPILib2::makeRequests();

        if ($instanceOptions && $instanceOptions->GetResult()) {
            $instanceOptions = $instanceOptions->GetResult();
            $ttl = 86400 + CronHelper::INCREASE_TTL; // 24 часа + неделя
            $this->fileMysqlMemoryCache->AddCacheEl($cacheKey, $ttl, json_encode($instanceOptions->asXML()));
        } else {
            $instanceOptions = new OtapiCommonInstanceOptionsInfo(false);
        }
        return $instanceOptions;
    }

    /**
     * Получить общие настройки инстанса
     *
     * @param $lang
     * @param bool $realTime
     * @return OtapiCommonInstanceOptionsInfo
     * @throws Exception
     */
    public function GetCommonInstanceOptionsInfo($lang)
    {
        $cacheKey = "GetCommonInstanceOptionsInfo:Result";
        if (isset($this->registry[$cacheKey])) {
            return $this->registry[$cacheKey];
        }

        if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
            $cache = simplexml_load_string(json_decode($this->fileMysqlMemoryCache->GetCacheEl($cacheKey), true));
            $this->registry[$cacheKey] = new OtapiCommonInstanceOptionsInfo($cache);
        } else {
            $instanceOptions = $this->updateCommonInstanceOptionsInfo($lang);
            $this->registry[$cacheKey] = $instanceOptions;
        }

        return $this->registry[$cacheKey];
    }

    /**
     * Получение списка стран, в которые возможна доставка
     *
     * @return OtapiArrayOfCountryInfo
     * @throws Exception
     */
    public function getDeliveryCountryInfoList()
    {
        $lang = Session::getActiveLang();
        $translatableOption = $this->getTranslatableOption($lang);

        $deliveryCounties = $translatableOption
            ->GetDeliveryCountries()
            ->GetContent();

        return $deliveryCounties;
    }

    /**
     * Получение информации о доступных провайдерах
     *
     * @param $lang
     * @return OtapiArrayOfProviderInfo
     * @throws Exception
     */
    public function GetProviderInfoList($lang)
    {
        $translatableOption = $this->getTranslatableOption($lang);

        $providerInfoList = $translatableOption
            ->GetProviders()
            ->GetContent();

        return $providerInfoList;
    }

    /**
     * Получение информации о доступных системах для внешней авторизации
     *
     * @param $lang
     * @return OtapiArrayOfAuthenticationSystemInfo
     * @throws Exception
     */
    public function GetExternalAuthentications($lang)
    {
        $translatableOption = $this->getTranslatableOption($lang);

        $providerInfoList = $translatableOption
            ->GetExternalAuthentications()
            ->GetContent();

        return $providerInfoList;
    }

    /**
     * Получить объект с локализованными настройками
     *
     * @param $lang
     * @return OtapiCommonTranslatableOptionsInfo
     * @throws Exception
     */
    private function getTranslatableOption($lang)
    {
        $dataObject = new OtapiCommonTranslatableOptionsInfo(false);

        $translatableOptions = $this->GetCommonInstanceOptionsInfo($lang)
            ->GetTranslatableOptions()
            ->GetContent()->GetItem();

        foreach ($translatableOptions as $translatableOption) {
            if (empty($lang)) {
                $dataObject = $translatableOption;
                break;
            } elseif ($lang == $translatableOption->GetLanguageAttribute()) {
                $dataObject = $translatableOption;
                break;
            }
        }

        return $dataObject;
    }

    /**
     * Получить используемые валюты
     *
     * @return OtapiInstanceListOfCurrencyInfo
     * @throws Exception
     */
    public function getCurrencyInstanceList()
    {
        $currencies = $this->GetCommonInstanceOptionsInfo(Session::getActiveLang())
            ->GetCurrencies();

        return $currencies;
    }

    /**
     * Получить список доступных языков витрины
     *
     * @return OtapiDataListOfLanguageInfo
     * @throws Exception
     */
    public function GetLanguageInfoList()
    {
        $languages = $this->GetCommonInstanceOptionsInfo(Session::getActiveLang())
            ->GetLanguages();

        return $languages;
    }

    /**
     * Получить массив доступных фич
     *
     * @return array
     * @throws Exception
     */
    public function GetFeatures()
    {
        $enabledFeatures = InstanceProvider::getObject()
            ->GetCommonInstanceOptionsInfo(Session::getActiveLang())
            ->GetFeatures()
            ->GetContent()
            ->GetItem();
        if ($enabledFeatures instanceof UnboundedElementsIterator) {
            $enabledFeatures = $enabledFeatures->toArray();
        }

        return $enabledFeatures;
    }

    public function GetProviderCurrency($provider) {
        $providerInfo = $this->GetProviderInfo(Session::getActiveLang(), $provider);

        if ($providerInfo) {
            if ($providerInfo->GetCurrencySign()) {
                return $providerInfo->GetCurrencySign();
            }
            if ($providerInfo->GetCurrencyCode()) {
                return $providerInfo->GetCurrencyCode();
            }
        }

        return '';
    }

    /*
     * @return OtapiCurrencyInfo
     */
    public function GetInternalCurrency()
    {
        return $this->getCurrencyInstanceList()->GetInternal();
    }

    /*
     * @return OtapiArrayOfCurrencyInfo
     */
    public function GetDisplayedMoneys()
    {
        return $this->getCurrencyInstanceList()->GetDisplayedMoneys();
    }

    public function clearCurrencyInstanceListCache()
    {
        $this->clearCommonInstanceOptionsInfoCache();
    }

    public function clearDeliveryCountryInfoListCache($lang)
    {
        $this->clearCommonInstanceOptionsInfoCache();
    }

    public function clearCommonInstanceOptionsInfoCache()
    {
        $cacheKey = "GetCommonInstanceOptionsInfo:Result";
        $this->clearCache($cacheKey);
    }

    public function isLimitItemsByCatalog()
    {
        $options = $this->GetCommonInstanceOptionsInfo(Session::getActiveLang());
        return $options->GetShowcase()->LimitItemsByCatalog();
    }

    public function GetWebUISettings($lang, $sid)
    {
        $cacheKey = "InstanceProvider:GetWebUISettings";
        if (! empty($this->registry[$cacheKey])) {
            return $this->registry[$cacheKey];
        }

        // проверяем данные в кеше
        if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
            $cache = simplexml_load_string(json_decode($this->fileMysqlMemoryCache->GetCacheEl($cacheKey), true));
            $settings = new OtapiWebUISettings($cache);
        } else {
            /**
             * @var OtapiWebUISettingsAnswer $settings
             */
            OTAPILib2::GetWebUISettings($lang, $sid, $settings);
            OTAPILib2::makeRequests();

            if ($settings && $settings->GetSettings()) {
                $settings = $settings->GetSettings();
                $this->fileMysqlMemoryCache->AddCacheEl($cacheKey, 3600, json_encode($settings->asXML()));
            }
        }

        $this->registry[$cacheKey] = $settings;
        return $settings;
    }

    private function getLanguagesAsAssoc($lang, $sid)
    {
        $langs = array();
        $settings = $this->GetWebUISettings($lang, $sid);
        foreach ($settings->GetLanguages()->GetNamedProperty() as $data) {
            $langs[(string)$data->GetName()] = (string)$data->GetDescription();
        }
        return $langs;
    }

    public function getLanguageDescriptionByCode($code, $lang, $sid)
    {
        $langs = $this->getLanguagesAsAssoc($lang, $sid);
        return isset($langs[$code]) ? $langs[$code] : $code;
    }

    public function GetDeliveryServiceSystemInfoList($lang)
    {
        $cacheKey = "InstanceProvider:GetDeliveryServiceSystemInfoList";
        if (! empty($this->registry[$cacheKey])) {
            return $this->registry[$cacheKey];
        }

        // проверяем данные в кеше
        if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
            $cache = simplexml_load_string(json_decode($this->fileMysqlMemoryCache->GetCacheEl($cacheKey), true));
            $systems = new ArrayOfOtapiDeliveryServiceSystemInfo($cache);
        } else {
            /** @var OtapiDeliveryServiceSystemInfoListAnswer $systems */
            OTAPILib2::GetDeliveryServiceSystemInfoList($lang, $systems);
            OTAPILib2::makeRequests();

            if ($systems && $systems->GetResult()->GetContent()) {
                $systems = $systems->GetResult()->GetContent();
                $this->fileMysqlMemoryCache->AddCacheEl($cacheKey, 3600, json_encode($systems->asXML()));
            }
        }

        $this->registry[$cacheKey] = $systems;
        return $systems;
    }

    public function isEnablePhotoSearch()
    {
        $cacheKey = 'isEnablePhotoSearch';
        if (!isset($this->registry[$cacheKey])) {
            $providers = self::getAvailableProviders(Session::getActiveLang());

            $isEnablePhotoSearch = false;
            foreach ($providers as $provider) {
                if ($provider->GetImageSearch()->IsEnabled()) {
                    $isEnablePhotoSearch = true;
                    break;
                }
            }
            $this->registry[$cacheKey] = $isEnablePhotoSearch;
        }

        return $this->registry[$cacheKey];
    }

    /**
     * Получить настройки социальных сетей
     *
     * @return OtapiSocialNetworkOptionsInfo
     * @throws Exception
     */
    public function GetSocialNetworks()
    {
        return $this->GetCommonInstanceOptionsInfo(Session::getActiveLang())
            ->GetSocialNetworks();
    }

    public static function GetSocialWithSubscribeWidgets($ids = [])
    {
        $socialNetworksMainInfo = InstanceProvider::getObject()->GetSocialNetworks()->GetMainInfo()->GetContent()->GetItem();

        $social = [];
        foreach ($socialNetworksMainInfo as $item) {
            if (!empty($ids) && !in_array($item->GetId(), $ids)) {
                continue;
            }

            $widget = $item->GetSubscribeWidget();
            if (empty($widget)) {
                continue;
            }

            array_push($social, $item);
        }

        return $social;
    }

    public static function GetSocialWithLikeWidgets($ids = [])
    {
        $socialNetworksMainInfo = InstanceProvider::getObject()->GetSocialNetworks()->GetMainInfo()->GetContent()->GetItem();

        $social = [];
        foreach ($socialNetworksMainInfo as $item) {
            if (!empty($ids) && !in_array($item->GetId(), $ids)) {
                continue;
            }

            $widget = $item->GetLikeWidget();
            if (empty($widget)) {
                continue;
            }

            array_push($social, $item);
        }

        return $social;
    }

    public static function GetSocialWithShareWidgets($ids = [])
    {
        $socialNetworksMainInfo = InstanceProvider::getObject()->GetSocialNetworks()->GetMainInfo()->GetContent()->GetItem();

        $social = [];
        foreach ($socialNetworksMainInfo as $item) {
            if (!empty($ids) && !in_array($item->GetId(), $ids)) {
                continue;
            }

            $widget = $item->GetShareWidget();
            if (empty($widget)) {
                continue;
            }

            array_push($social, $item);
        }

        return $social;
    }

    public static function GetSocialWithLinks($ids = [])
    {
        $socialNetworksMainInfo = InstanceProvider::getObject()->GetSocialNetworks()->GetMainInfo()->GetContent()->GetItem();

        $social = [];
        foreach ($socialNetworksMainInfo as $item) {
            if (!empty($ids) && !in_array($item->GetId(), $ids)) {
                continue;
            }

            $link = $item->GetLink();
            if (empty($link)) {
                continue;
            }

            array_push($social, $item);
        }

        return $social;
    }

    /**
     * @return array
     * @deprecated не используется с 13.05.2021
     */
    public static function GetSocialNetworkIds()
    {
        return self::$social_networks;
    }
}
