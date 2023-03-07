<?php

/*
 * Шаги при оформлении заказа
 *
 * step1 - назначение веса для товара
 * step2 - выбор заказа: новый заказ или дозаказ
 * step3 - выбор подходящей модели доставки
 * step4 - все данные для оформления заказа
 */

OTBase::import('system.lib.service.*');

class UserZakazNew extends GenerateBlock {

    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'userzakaznew'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/order/';
    private $isInternalDeliveryFixed;

    const MAX_USER_PROFILES_COUNT   = 3;

    /**
    *
    */
    private $items = array();
    /**
    *
    */
    private $basket = array();
    /**
    *
    */
    private $basketRecord = array();
    /**
    *
    */
    private $newOrderAvailable = true;
    
    /**
    *
    */
    private $minOrderCost = false;
    
    /**
    *
    */
    private $currencySign;
    
    /**
    *
    */
    private $totalCost = 0;

    public function __construct() {
        parent::__construct(true);
        $this->isInternalDeliveryFixed = defined('CFG_INTERNAL_DELIVERY_FIXED') && CFG_INTERNAL_DELIVERY_FIXED;
    }

    protected function setVars()
    {
        $sid = Session::getUserOrGuestSession();
        $type = $this->checkProviderType();
        $deliveryId = $this->request->get('deliveryId', 0);
        
        if (! $type) {
            return $this->redirect(UrlGenerator::generateContentUrl('basket'));
        }
        if (! Session::getUserData() && ! General::getConfigValue('simplified_registration')) {
            return $this->redirect('/?p=login');
        }

        $basket = $this->otapilib->GetBasket(Session::getUserOrGuestSession());
        $record = new BasketRecord($basket);
        $methodItems = 'get' . $type . 'Items';
        if (RequestWrapper::getParamExists('step1') && RequestWrapper::post('items')) {
            $itemOrderIds = explode(",", $this->request->post('items'));
            Session::set("itemsToOrder", $itemOrderIds);
        } else {
            $itemOrderIds = Session::get("itemsToOrder");
        }
        $items = $record->$methodItems();
        $itemsToOrder = array();
        //Убираем не выбранные товары
        //и лишних продавцов
        foreach ($items as $item) {
            if (in_array($item['id'], $itemOrderIds)) {
                $itemsToOrder[] = $item;
            }
        }
        if (! empty($basket['CollectionSummaries'][$type]['AdditionalPriceInfoList']['Elements'])) {
            $nAdd = array();
            foreach ($itemsToOrder as $item) {
                foreach ($basket['CollectionSummaries'][$type]['AdditionalPriceInfoList']['Elements'] as $v => $d) {
                    if ($v == $item['VendorId'] && ! isset($nAdd[$v])) {
                        $nAdd[$v] = $d;
                    }
                }
            }
            $basket['CollectionSummaries'][$type]['AdditionalPriceInfoList']['Elements'] = $nAdd;
        }
        $this->items = $itemsToOrder;
        $this->basket = $basket;
        $this->basketRecord = $record;
        
        $settings = $this->otapilib->GetCommonInstanceOptionsInfo();
        $this->minOrderCost = $settings['order'];
        $this->currencySign = User::getObject()->getCurrencySign();
        
        $itemsWeight = 0;
        foreach ($itemsToOrder as $item) {
            if (isset($item['weight'])) $itemsWeight += $item['weight'];
            $this->totalCost = $this->totalCost + $item['cost'];
        }
        if ($this->minOrderCost && $this->totalCost < $this->minOrderCost) {
            $this->newOrderAvailable = false;
        }
        
        if (! count($itemsToOrder )) {
            return $this->redirect('/?p=basket');
        } elseif (isset($_GET['step1'])) {
        	if ($deliveryId) {
        		try {
        			$xmlParams = $this->_generateXmlForSearchDelivery($type, User::getObject()->getCountryCode(), $itemsWeight, $deliveryId);
        			$modes = $this->otapilib->SearchDeliveryModes($xmlParams);
        			if (! $modes) {
        				Session::setError(Lang::get('Defined_delivery_mode_not_found_Please_select_other'));
        			}
        		} catch (Exception $e) {
        			Session::setError(Lang::get('Defined_delivery_mode_not_found_Please_select_other'));
        		}
        		Session::set("deliveryId", $deliveryId);
        	} else {
        		Session::set("deliveryId", 0);
        	}        	
        	
            if (General::getConfigValue('hide_step_weight_order')) {
                $this->_step2($sid);
            } else {
                $this->_template = 'step1new';
                $this->tpl->assign('list', $itemsToOrder);
                $this->tpl->assign('deliveryId', $deliveryId);
            }

        } elseif (isset($_GET['step2'])) {
            $this->_step2($sid);

        } elseif (isset($_GET['step3'])) {
            $this->_step3($sid);

        } elseif (isset($_GET['step4'])) {
            $this->_step4($sid);

        } elseif (isset($_GET['createorder'])) {
            $this->_createOrder($sid);

        }
    }

    protected function CreateZakaz() {
        $zakaz = new Zakaz();
        $sid = session_id();
        $message = $zakaz->CreateOrderFromBasket($sid);
        $this->tpl->assign('message', $message);
    }

    protected function GetZakaz($data) {
        $zakaz = new Zakaz();
        $order = $zakaz->GetOrder($data);
        return $order;
    }

    private function _step2($sid)
    {
        $alias = $this->getProviderAlias();
        $type = $this->checkProviderType(true);

        $weight = $this->_getWeight($this->items);
        Session::set('order_weight', $weight);
        
        $xmlSearchParameters = "<OrderSearchParametersForUser><ProviderType>" . $type . "</ProviderType><IsAvailableForRecreation>true</IsAvailableForRecreation></OrderSearchParametersForUser>";
        $orders = $this->otapilib->SearchOrdersForUser($sid, $xmlSearchParameters, 0, 100);

        // если нет заказов или запрещен дозаказ, то переходим к шагу з
        if (($orders['TotalCount'] != 0) && ! General::getConfigValue('skip_reordering')) {
            //Можно сделать как минимум дозаказ
            $this->_template = 'step2new';
            $this->tpl->assign('orders', $orders['Content']);
            $this->tpl->assign('weight', $weight);
            $this->tpl->assign('newOrderAvailable', $this->newOrderAvailable);
            $this->tpl->assign('minOrderCost', $this->minOrderCost);
            $this->tpl->assign('sign', $this->currencySign);
        } else if ($this->newOrderAvailable && (($orders['TotalCount'] == 0) || General::getConfigValue('skip_reordering'))) {
            //Можно пропускать
            if (strpos($_SERVER['HTTP_REFERER'], 'step3')) {
                return $this->redirect(General::getConfigValue('hide_step_weight_order')? UrlGenerator::generateContentUrl('basket'): '/?p=userorder&step1&type=' . $alias);
            } else {
                return $this->redirect('/?p=userorder&step3&order=new&type=' . $alias);
            }
        } else if ($this->newOrderAvailable) {            
            $this->_template = 'step2new';
            $this->tpl->assign('orders', $orders['Content']);
            $this->tpl->assign('weight', $weight);
            $this->tpl->assign('newOrderAvailable', $this->newOrderAvailable);
        } else {
            //Упираемся в мин заказ
            return $this->redirect(UrlGenerator::generateContentUrl('basket'));
        }
    }

    private function _step3($sid)
    {
        $alias = $this->getProviderAlias();
        $type = $this->checkProviderType(true);
        $deliveryId = Session::get("deliveryId");

        $this->otapilib->setErrorsAsExceptionsOn();

        $this->_template = 'step3new';

        $items = $this->items;

        $weight = $this->_getWeight($items);

        if ($this->request->getValue('order')) {
            $order = $this->request->getValue('order');
        } else {
            $order = 'new';
        }

        if (! $this->request->getValue('order')) {
            return $this->redirect('/?p=userorder&step3&order=' . $order . '&type=' . $alias);
        }

        if (defined('CFG_DISCOUNTER_REORDER') && $order != 'new')
        {
            Session::set('DISCOUNTER_REORDER', $order);
            $order = 'new';
            $userData = $this->otapilib->GetUserInfo($sid);
            $country = $userData['Country'] ? $userData['Country'] : Lang::get('geo_rus');
            $countries = InstanceProvider::getObject()->getDeliveryCountryInfoList();
            $countries = $this->otapilib->GetDeliveryCountryInfoList($countries->asXML());
            $country = $userData['Country'] ? $userData['Country'] : Lang::get('geo_rus');
            foreach($countries as $c){
                if($c['Name'] == $country) $currentCountry = $c;
            }
            $models = $this->otapilib->GetDeliveryModesWithPrice(isset($currentCountry['Id']) ? $currentCountry['Id'] : User::getObject()->getCountryCode(), 0);
            $model_id = $models[0]['id'];
            return $this->redirect('/?p=userorder&step4&order=' . $order . '&model=' . $model_id . '&country=' . $currentCountry['Id'] . '&type=' . $alias);
        } else {
            if (isset($_SESSION['DISCOUNTER_REORDER'])) Session::clear('DISCOUNTER_REORDER');
        }

        // Если у нас дозаказ
        try {
            if ($order != 'new') {
                $ids = array();
                foreach ($items as $item) {
                    $ids[] = $item['id'];
                }

                $r = $this->otapilib->RecreateOrder($sid, $this->_xmlRecreateOrder($order, $ids));
                if (!$r) {
                    return $this->redirect('/?p=privateoffice&message=error');
                } else {
                    $userData = new UserData();
                    $userData->ClearUserDataCache();
                    return $this->redirect(UrlGenerator::generateOrderDetailsUrl((string)$order, array('tab' => 3)));
                }
            }
        } catch(ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
            if ($e->getErrorCode() == 'SessionExpired') {
                return $this->redirect('/?p=login');
            }
            return $this->redirect('/?p=userorder&step2&type=' . $alias);
        } catch(Exception $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
            return $this->redirect('/?p=userorder&step2&type=' . $alias);
        }

        try {
            $countries = InstanceProvider::getObject()->getDeliveryCountryInfoList();
            $countries = $this->otapilib->GetDeliveryCountryInfoList($countries->asXML());
            $countryPrices = array();
            foreach ($countries as $country) {
                try {
                    if ($deliveryId) {
                        $xmlParams = $this->_generateXmlForSearchDelivery($type, $country['Id'], $weight, $deliveryId);
                    } else {
                        $xmlParams = $this->_generateXmlForSearchDelivery($type, $country['Id'], $weight);
                    }
                    $modes = $this->otapilib->SearchDeliveryModes($xmlParams);
                    $modes = $modes['Content'];

                    if ($modes && is_array($modes)) {
                        foreach ($modes as &$m) {
                            foreach($m['FullPrice']['ConvertedPriceList']['DisplayedMoneys'] as $i=>$d){
                                $price = $d;
                                $price->price = (float)$d;
                                $m['FullPrice']['ConvertedPriceList']['DisplayedMoneys'][$i] = $price;
                            }
                        }
                        $countryPrices[$country['Id']] = $modes;
                    }
                } catch (Exception $e) {}
            }
            $profiles = array();
            if (Session::getUserData()) {
                $profiles = $this->otapilib->GetUserProfileInfoList($sid);
            } else {
                if ($this->fileMysqlMemoryCache->Exists('userTempProfile:'.Session::getUserOrGuestSession())){
                    $xmlProfile = new SimpleXMLElement(unserialize($this->fileMysqlMemoryCache->GetCacheEl('userTempProfile:'.Session::getUserOrGuestSession())));
                    $profileTmp['id'] = 'temp';
                    $profileTmp['Id'] = 'temp';
                    $profileTmp['PostalCode'] = (string)$xmlProfile->PostalCode;
                    $profileTmp['Region'] = (string)$xmlProfile->Region;
                    $profileTmp['City'] = (string)$xmlProfile->City;
                    $profileTmp['Address'] = (string)$xmlProfile->Address;
                    $profileTmp['FirstName'] = (string)$xmlProfile->FirstName;
                    $profileTmp['MiddleName'] = (string)$xmlProfile->MiddleName;
                    $profileTmp['LastName'] = (string)$xmlProfile->LastName;
                    $profileTmp['Phone'] = (string)$xmlProfile->Phone;
                    $profileTmp['CountryCode'] = (string)$xmlProfile->CountryCode;
                    $profileTmp['PassportNumber'] = isset($xmlProfile->PassportNumber) ? (string)$xmlProfile->PassportNumber : '';
                    $profileTmp['RegistrationAddress'] = isset($xmlProfile->RegistrationAddress) ? (string)$xmlProfile->RegistrationAddress : '';
                    $profiles[] = $profileTmp;
                }
            }
            $country = '';

            if (count($profiles)) {
                foreach ($profiles as $profile) {
                    $country = $profile['CountryCode'];
                    break;
                }
            }

            foreach ($countries as $c){
                if($c['Id'] == $country) {
                    $currentCountry = $c;
                }
            }

            if(! isset($currentCountry)) {
                $currentCountry['Id'] = User::getObject()->getCountryCode();
            }

            $models = array();
            if (isset($currentCountry['Id']) && $currentCountry['Id'] && ! empty($countryPrices[$currentCountry['Id']])) {
                $models = $countryPrices[$currentCountry['Id']];
            }

            $this->tpl->assign('profiles', $profiles);
            $this->tpl->assign('countries', $countries);
            $this->tpl->assign('countryPrices', $countryPrices);
            $this->tpl->assign('models', $models);
            $this->tpl->assign('weight', $weight);
            $this->tpl->assign('order', $order);
            $this->tpl->assign('deliveryId', $deliveryId);
            $this->tpl->assign('defaultDelivery', User::getObject()->getUserPreferences()->GetExternalDeliveryId());
        } catch(ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
            if ($e->getErrorCode() == 'SessionExpired') {
                return $this->redirect('/?p=login');
            }

            if ($e->getErrorCode() == 'InternalError') {
                $message = $e->getErrorMessage();
                if (OTBase::isTest() && Session::get('sid')) {
                    $message = $e->getErrorCode().': '.$message;
                }
                show_error($message);
            } else {
                $error = $e->getMessage();

                if ($e->getErrorCode() == 'NotAvailable') {
                    $error = Lang::get('NotAvailable');
                } elseif (strripos($error, 'There is no delivery') !== false) {
                    $error = Lang::get('no_deliver_error');
                }
                $this->tpl->assign('error', $error);
            }

            if (isset($profiles)) $this->tpl->assign('profiles', $profiles);
            if (isset($countries)) $this->tpl->assign('countries', $countries);
            if (isset($models)) $this->tpl->assign('models', $models);
            if (isset($weight)) $this->tpl->assign('weight', $weight);
            if (isset($order)) $this->tpl->assign('order', $order);

        } catch(Exception $e){
            return $this->redirect('/?p=userorder&step3&order=' . $order . '&error='.$e->getMessage());
        }
    }

    private function _step4($sid)
    {
        $this->otapilib->setErrorsAsExceptionsOn();
        $alias = $this->getProviderAlias();
        $type = $this->checkProviderType(true);

        $allBasket = $this->basket;
        $basketRecord = $this->basketRecord;
        $items = $this->items;

        if ($this->request->getValue('total_weight')) {
            $weight = $this->request->getValue('total_weight');
        } elseif (Session::get('order_weight')) {
            $weight = Session::get('order_weight');
        } elseif ($this->request->getValue('weight')) {
            $weight = $this->request->getValue('weight');
        } else {
            $weight = $this->_getWeight($items);
        }

        if ($this->request->getValue('order')) {
            $order = $this->request->getValue('order');
        } else {
            $order = 'new';
        }

        $profile_info = $this->request->getValue('Profile');

        if (isset($profile_info['New'])) {
            $delivery_profile_id = '';
        } else {
            $delivery_profile_id = (! empty($profile_info['Id']) && $profile_info['Id'] != 'temp') ? $profile_info['Id'] : $this->request->getValue('profile');
        }
        $country = $profile_info['CountryCode'] ? $profile_info['CountryCode'] : $this->request->getValue('country');
        if(!$country){
            return $this->redirect('/?p=userorder&step3&order=' . $order . '&type=' . $alias);
        }

        if ($this->request->getValue('model')) {
            $model_id = $this->request->getValue('model');
        } else {
            $model_id = '';
        }

        try {

            if ($profile_info) {
                if (! General::getConfigValue('simplified_registration') || $delivery_profile_id) {
                    try{
                        list($success, $delivery_profile_id) = $this->_saveDeliveryProfile($profile_info);
                    }
                    catch(ServiceException $e){
                        Session::setError($e->getMessage(), $e->getErrorCode());
                        return $this->redirect('/?p=userorder&step3&order=' . $order . '&error=' . $e->getMessage() . '&type=' . $alias);
                    }
                    Plugins::invokeEvent('onUpdateUserStep4');
                } else {
                    try{
                        $result = $this->_validateDeliveryProfile($profile_info);
                    }
                    catch(ServiceException $e){
                        Session::setError($e->getErrorMessage(), $e->getErrorCode());
                        return $this->redirect('/?p=userorder&step3&order=' . $order . '&error=' . $e->getErrorMessage() . '&type=' . $alias);
                    }
                    $this->_saveTmpDeliveryProfile($profile_info);
                }

            }

            if ($model_id == '') {
                return $this->redirect('/?p=userorder&step3&order=' . $order . '&type=' . $alias);
            }

            $this->_template = 'step4new';

            $xmlParams = $this->_generateXmlForSearchDelivery($type, $country, $weight, $model_id);
            $deliveryModes = $this->otapilib->SearchDeliveryModes($xmlParams);
            $deliveryModes = $deliveryModes['Content'];

            $userdiscount = false;
            if (Session::getUserData()) {
                $userdiscount = $this->otapilib->GetDiscountGroup($sid);
            }
            $model_info = array();
            foreach ($deliveryModes as $model) {
                if ($model['id'] == $model_id)
                    $model_info = $model;
            }

            if (Session::getUserData()) {
                $userinfo = $this->otapilib->GetUserInfo($sid);
                $userinfo = array_filter($userinfo);

                $user = array();

                foreach ($userinfo as $key => $value) {
                    $key = strtolower((string) $key);
                    if (!isset($user[$key]))
                        $user[$key] = (string) $value;
                }

                /** профиль доставки пользователя */
                $profiles = $this->otapilib->GetUserProfileInfoList($sid);
                $profile = '';
                foreach ($profiles as $item) {
                    // TODO: проверка на совпадение данных профиля убрана - слишком много с ней проблем
                    if ($item['Id'] == $delivery_profile_id /*&& $this->_sameProfiles($item, $profile_info)*/) {
                        $profile = $item;
                    }
                }
                unset($user['isemailverified']);
                unset($user['id']);
                unset($user['isactive']);
                unset($user['password']);
                unset($user['login']);
            }
            if (!Session::getUserData() || ! $profile ) {
                $user = false;
                $profile = array();
                if ($this->fileMysqlMemoryCache->Exists('userTempProfile:' . Session::getUserOrGuestSession())){
                    $xmlProfile = new SimpleXMLElement(unserialize($this->fileMysqlMemoryCache->GetCacheEl('userTempProfile:'.Session::getUserOrGuestSession())));
                    $profile['postalcode'] = (string)$xmlProfile->PostalCode;
                    $profile['region'] = (string)$xmlProfile->Region;
                    $profile['city'] = (string)$xmlProfile->City;
                    $profile['address'] = (string)$xmlProfile->Address;
                    $profile['firstname'] = (string)$xmlProfile->FirstName;
                    $profile['middlename'] = (string)$xmlProfile->MiddleName;
                    $profile['lastname'] = (string)$xmlProfile->LastName;
                    $profile['phone'] = (string)$xmlProfile->Phone;
                } else {
                    return $this->redirect('/?p=userorder&step3&order=new&type='.($alias ? $alias : 'taobao'));
                }
            }
            /** определение страны доставки по ее коду */
            $countries = InstanceProvider::getObject()->getDeliveryCountryInfoList();
            $countries = $this->otapilib->GetDeliveryCountryInfoList($countries->asXML());
            foreach ($countries as $c) {
                if($c['Id'] == $country) {
                    $profile['country'] = $c['Name'];
                }
            }

            /** */
            if (General::getConfigValue('origin_package')) {
                $origin_package = true;
            } else {
                $origin_package = false;
            }
            $order_insurance = 0;
            if (General::getConfigValue('order_insurance_percent')) {
                $order_insurance = General::getConfigValue('order_insurance_percent');
            }

            $this->tpl->assign('allBasket', $allBasket);
            $this->tpl->assign('profile', $profile);
            $this->tpl->assign('basket', $basketRecord->asArray());
            $this->tpl->assign('list', $items);
            $this->tpl->assign('order', $order);
            $this->tpl->assign('weight', $weight);
            $this->tpl->assign('model_info', $model_info);
            $this->tpl->assign('userdiscount', $userdiscount);
            $this->tpl->assign('user_info', $user);
            $this->tpl->assign('origin_package', General::getConfigValue('origin_package'));
            $this->tpl->assign('country', $country);
            $orderComment = Session::get('orderComment');
            $orderComment = !empty($orderComment) ? $orderComment : '';
            $this->tpl->assign('order_comment', $orderComment);

			$cRep = new ContentRepository($this->cms);
            $page = $cRep->GetPageByAlias('terms_of_use');
            $userAgreement = ($page) ? $page['text'] : Lang::get('empty_page_msg');
            $this->tpl->assign('userAgreement', $userAgreement);

            $O = new Step4OrderSummary($deliveryModes, $basketRecord, $this->_getWeight($items), $type, $allBasket, $this->totalCost);
            $this->tpl->assign('OrderSummary', $O->Generate());
        } catch(ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
            if ($e->getErrorCode()=='SessionExpired') {
                return $this->redirect('/?p=login');
            }
            return $this->redirect('/?p=userorder&step3&order=' . $order . '&error=' . $e->getMessage() . '&type=' . $alias);
        } catch (Exception $e) {
            return $this->redirect('/?p=userorder&step3&order=' . $order . '&error=' . $e->getMessage() . '&type=' . $alias);
        }
    }

    private function _sameProfiles($profile1, $profile2) {
        return $profile1['Address'] == $profile2['Address'] and
                $profile1['CountryCode'] == $profile2['CountryCode'] and
                $profile1['PostalCode'] == $profile2['PostalCode'] and
                $profile1['Region'] == $profile2['Region'] and
                $profile1['City'] == $profile2['City'] and
                $profile1['FirstName'] == $profile2['FirstName'] and
                $profile1['MiddleName'] == $profile2['MiddleName'] and
                $profile1['LastName'] == $profile2['LastName'] and
                $profile1['Phone'] == $profile2['Phone'];
    }

    private function _createOrder($sid)
    {
        $alias = $this->getProviderAlias();
        $unUserSid = Session::getUserOrGuestSession();

        try {
            Session::set('orderComment', $this->request->getValue('comment'));
            Session::set('orderOriginPackage', $this->request->getValue('origin_package', false));
            Session::set('orderInsurance', $this->request->getValue('order_insurance', false));
            $this->otapilib->setErrorsAsExceptionsOn();
            $this->_loginOrRegisterIfNeed();
        } catch (ServiceException $e){
            Session::setError($e->getErrorMessage(), $e->getErrorCode());
            return $this->redirect('/?p=userorder&step4&order=new'
                    . '&model=' . $this->request->getValue('model')
                    . '&country=' . $this->request->getValue('country', 'RU')
                    . '&type=' . ($alias ? $alias : 'taobao'));
        }
        Session::clear('orderComment');
        Session::clear('orderOriginPackage');
        Session::clear('orderInsurance');

        $type = $this->checkProviderType(true);

        $items = $this->items;

        $weight = $this->_getWeight($items);

        $deliveryModeId = $this->request->getValue('model');

        $sid = Session::getUserSession();
        $profile_info = $this->request->getValue('Profile');
        $deliveryProfileId = (! empty($profile_info['Id']) && $profile_info['Id'] != 'temp') ? $profile_info['Id'] : $this->request->getValue('profile');
        if(!$deliveryProfileId && $this->fileMysqlMemoryCache->Exists('userTempProfile:'.$unUserSid)) {
            $profiles = $this->otapilib->GetUserProfileInfoList($sid);
            $xmlProfile = unserialize($this->fileMysqlMemoryCache->GetCacheEl('userTempProfile:'.$unUserSid));
            if (count($profiles) == self::MAX_USER_PROFILES_COUNT) {
                foreach ($profiles as $p) {
                    $deliveryProfileId = $p['id'];
                }
                $xmlProfile = new SimpleXMLElement($xmlProfile);
                $paramsProfile['PostalCode'] = (string)$xmlProfile->PostalCode;
                $paramsProfile['Region'] = (string)$xmlProfile->Region;
                $paramsProfile['City'] = (string)$xmlProfile->City;
                $paramsProfile['Address'] = (string)$xmlProfile->Address;
                $paramsProfile['FirstName'] = (string)$xmlProfile->FirstName;
                $paramsProfile['MiddleName'] = (string)$xmlProfile->MiddleName;
                $paramsProfile['LastName'] = (string)$xmlProfile->LastName;
                $paramsProfile['Phone'] = (string)$xmlProfile->Phone;
                $paramsProfile['CountryCode'] = (string)$xmlProfile->CountryCode;
                $paramsProfile['Id'] = $deliveryProfileId;

                $profile = new Profile();
                $paramsProfile = array('Profile' => $paramsProfile);
                list($success, $data) = $profile->saveDeliveryProfile($paramsProfile);
            } else {
                $deliveryProfileId = $this->otapilib->CreateUserProfile($sid, $xmlProfile);
            }

            /** Очищаем временный профиль доставки из кеша */
            $this->fileMysqlMemoryCache->DelCacheEl('userTempProfile:'.$unUserSid);
        }
        $comment = htmlspecialchars((string)$this->request->getValue('comment'))." \r\n ".Plugins::invokeEvent('onOrderGetDomainName');
        if ($this->request->getValue('origin_package')) {
            $comment .= '  ' . Lang::get('save_origin_package');
        }
        if ($this->request->getValue('order_insurance')) {
            $order_insurance = 0;
            if (General::getConfigValue('order_insurance_percent')) {
                $order_insurance = General::getConfigValue('order_insurance_percent');
            }
            if ($order_insurance>0) $comment .= '  (INSURANCE:+'.$order_insurance.'%)';
        }
        if (defined('CFG_DISCOUNTER_REORDER') && Session::get('DISCOUNTER_REORDER'))
        {
            $comment = Lang::get('Need_to_merge_with_order_upper').' '.Session::get('DISCOUNTER_REORDER')."\r\n".$comment;
        }

        $order = ($this->request->getValue('order')) ? $this->request->getValue('order') : 'new';

        $this->_template = 'order_processing';

        $ids = array();
        foreach ($items as $item) {
            $ids[] = $item['id'];
        }

        try{
            $this->otapilib->setErrorsAsExceptionsOn();
            if ($order == 'new') {

                $orderInfo = Plugins::onCreateOrder($sid, $deliveryModeId);
                $xmlParams = str_replace('<?xml version="1.0"?>', '', $this->_xmlNewOrder($deliveryModeId, $comment, $deliveryProfileId, $ids));
                if ($orderInfo === 0) {
                    $orderInfo = $this->ordersProxy->CreateOrder($sid, $xmlParams);
                }
            } else {
                $orderInfo = $this->otapilib->RecreateOrder($sid, $this->_xmlRecreateOrder($order, $ids));
            }
            Session::clear("itemsToOrder");
        }
        catch(ServiceException $e){
            $country = $this->request->getValue('country', 'RU');
            Session::setError($e->getErrorMessage(), $e->getErrorCode());
            return $this->redirect('/?p=userorder&step4&order=' . $order
                    . '&model=' . $deliveryModeId
                    . '&country=' . $country
                    . '&profile=' . $deliveryProfileId
                    . '&type=' . $alias);
        }

        if(isset($orderInfo->Result)) {
            Plugins::runSerialEvent('onAfterSuccesCreateOrder', array('order' => $orderInfo));

            $newRedirect =  Plugins::invokeEvent('onNewAfterOrderRedirect', array('delivery' => (string)$orderInfo->Result->DeliveryModeId, 'orderInfo' => $orderInfo));
            $order_array['Id'] = $order_array['id'] = (string)$orderInfo->Result->Id;
            $order_array['TotalAmount'] = (string)$orderInfo->Result->TotalAmount;
            $orderInfo = $order_array;
        }

        $userData = new UserData();
        $userData->ClearUserDataCache();

        $newRedirect = $newRedirect ? $newRedirect : UrlGenerator::generateOrderDetailsUrl($orderInfo['Id'], array('tab' => 3, 'newOrderId' => (string)$orderInfo['id']));
        return $this->redirect($newRedirect);
    }

    private function _getWeight($items) {
        $weight = 0;

        foreach ($items as $item) {
            $weight += isset($item['Weight']) ? (float)$item['Weight']*$item['Quantity'] : 0;
        }

        return round($weight, 3);
    }

    private function _saveProfile($fields) {

        $xmlParams = str_replace('<?xml version="1.0"?>', '', $this->_xmlParams($fields));
        $sid = Session::getUserSession();
        $reg = $this->otapilib->UpdateUser($sid, $xmlParams);

        if ($reg === false){            
            throw new Exception('User cannot be saved: '. $this->otapilib->error_message);
        }

        return array(true, Lang::get('data_updated_successfully'));
    }

    private function _saveDeliveryProfile($fields) {
        $profile = new Profile();
        $params = array('Profile' => $fields);
        if (isset($fields['Id'])) {
            $profile_id = $fields['Id'];
            list($success, $data) = $profile->saveDeliveryProfile($params);
        } else {
            $sid = Session::getUserSession();
            $xmlParams = str_replace('<?xml version="1.0"?>', '', $profile->xmlParamsDeliveryProfile($fields));
            $profile_id = $this->otapilib->CreateUserProfile($sid, $xmlParams);
            return array(true, $profile_id);
        }

        if ($success === false) {
            throw new Exception('User cannot be saved: '.$data);
        }

        return array(true, $profile_id);
    }

    private function _validateDeliveryProfile($fields) {
        $profile = new Profile();
        $params = array('Profile' => $fields);

        $this->_saveTmpDeliveryProfile($fields);
        $xmlParams = str_replace('<?xml version="1.0"?>', '', $profile->xmlParamsDeliveryProfile($fields));
        $xmlParams = str_replace('UserProfileCreateData', 'UserProfileValidateData', $profile->xmlParamsDeliveryProfile($fields));
        $result = $this->otapilib->ValidateUserProfile($xmlParams);
        return $result;
    }

    private function _saveTmpDeliveryProfile($fields) {
        $profile = new Profile();
        $sid = Session::getUserOrGuestSession();
        $deliveryProfile = $profile->xmlParamsDeliveryProfile($fields);
        $this->fileMysqlMemoryCache->AddCacheEl('userTempProfile:'.$sid, 600, serialize($deliveryProfile));
        return array(true, 1);
    }

    private function _xmlParams($fields) {
        $xml = new SimpleXMLElement('<UserUpdateData></UserUpdateData>');
        $xml->addChild('Email', htmlspecialchars($fields['Email']));
        $xml->addChild('FirstName', htmlspecialchars($fields['FirstName']));
        $xml->addChild('LastName', htmlspecialchars($fields['LastName']));
        $xml->addChild('MiddleName', htmlspecialchars($fields['MiddleName']));
        $xml->addChild('Sex', htmlspecialchars($fields['Sex'] ? $fields['Sex'] : 'Male'));

        $xml->addChild('CountryCode', htmlspecialchars($fields['Country']));
        $xml->addChild('City', htmlspecialchars($fields['City']));
        $xml->addChild('Address', htmlspecialchars($fields['Address']));
        $xml->addChild('Phone', htmlspecialchars($fields['Phone']));
        $xml->addChild('PostalCode', htmlspecialchars($fields['PostalCode']));
        $xml->addChild('Region', htmlspecialchars($fields['Region']));

        $xml->addChild('RecipientFirstName', htmlspecialchars($fields['RecipientFirstName']));
        $xml->addChild('RecipientLastName', htmlspecialchars($fields['RecipientLastName']));
        if(!General::getConfigValue('hide_middlename'))
            $xml->addChild('RecipientMiddleName', htmlspecialchars($fields['RecipientMiddleName']));

        return $xml->asXML();
    }

    private function _generateXmlForSearchDelivery($provider, $country, $weight, $deliveryId = false) {
    	$xml = new SimpleXMLElement('<DeliveryModeSearchParameters></DeliveryModeSearchParameters>');
    	$xml->addChild('CountryCode', htmlspecialchars($country));
    	$xml->addChild('Weight', htmlspecialchars($weight));
    	$xml->addChild('ProviderType', htmlspecialchars($provider));
    	if ($deliveryId) {
    		$xml->addChild('DeliveryModeIds');
    		$xml->DeliveryModeIds->addChild('Id', $deliveryId);
    	}
    
    	return str_replace('<?xml version="1.0"?>', '', $xml->asXML());
    }

    private function _xmlNewOrder($model, $comment, $profile, $ids) {
        $xml = new SimpleXMLElement('<OrderCreateData></OrderCreateData>');
        $xml->addChild('DeliveryModeId', htmlspecialchars($model));
        $xml->addChild('Comment', htmlspecialchars($comment));
        $xml->addChild('UserProfileId', htmlspecialchars($profile));
        $xml->addChild('Elements');
        foreach ($ids as $id) {
            $xml->Elements->addChild('Id', $id);
        }

        return str_replace('<?xml version="1.0"?>', '', $xml->asXML());
    }

    private function _xmlRecreateOrder($orderId, $ids)
    {
        $xml = new SimpleXMLElement('<OrderRecreateData></OrderRecreateData>');
        $xml->addChild('OrderId', htmlspecialchars($orderId));
        $xml->addChild('Elements');
        foreach ($ids as $id) {
            $xml->Elements->addChild('Id', $id);
        }

        return trim(str_replace('<?xml version="1.0"?>', '', $xml->asXML()));
    }

    private function _loginOrRegisterIfNeed()
    {
        if (! Session::getUserData()) {
            $alias = $this->getProviderAlias();

            $userSession = Session::getUserOrGuestSession();
            $actions = false;
            $login = $this->request->getValue('username');
            $register = $this->request->getValue('email');
            if (! empty($login)) {
                $actions = true;
                $userStatus = Users::Login($this->request->getAll());
            }
            if (! empty($register) && ! $actions) {
                $xml = new SimpleXMLElement('<UserRegistrationData></UserRegistrationData>');
                $xml->addChild('Email', htmlspecialchars($this->request->getValue('email')));
                $xml->addChild('Password', htmlspecialchars($this->request->getValue('password_to_registration')));
                $xml->addChild('Login', htmlspecialchars($this->request->getValue('email')));
                $xmlParams = str_replace('<?xml version="1.0"?>', '', $xml->asXML());
                $result = $this->otapilib->RegisterUser(User::getObject()->getSid(), $xmlParams);

                if (!empty($result['SessionId'])) {
                    $this->authBySessionId($result['SessionId']);
                }
            }
            if (empty($login) && empty($register)) {
                return $this->redirect('/?p=userorder&step4&order=new'
                    . '&model=' . $this->request->getValue('model')
                    . '&country=' . $this->request->getValue('country', 'RU')
                    . '&type=' . ($alias ? $alias : 'taobao')
                    . '&noAuth=1');
            } else {
                $userData = new UserData();
                $userData->ClearUserDataCache();
            }
        }
    }

    private function authBySessionId($sid)
    {
        // проверяем корректность sid
        $answer = null;
        OTAPILib2::GetUserStatusInfo(Session::getActiveLang(), $sid, $answer);
        OTAPILib2::makeRequests();
        // логиним покупателя
        Users::AutoLogin($sid);
    }

    private function checkProviderType($onlyGetType = false)
    {
        $instanceProvider = InstanceProvider::getObject();

        $alias = $this->request->post('type');
        if (! $alias) $alias = $this->request->get('type');
        if (! $alias) $alias = Session::get("createOrderType");
        if ($alias) Session::set("createOrderType", $alias);
        
        $type = $instanceProvider->GetProviderNameByAlias(Session::getActiveLang(), $alias);
     
        if ($onlyGetType) {
            return $type;
        }
        $this->tpl->assign('type', $type);
        $this->tpl->assign('alias', $alias);
        if (! $type) {
            Session::setError(Lang::get('No_provider_type_of_items_in_order'));
            return false;
        } else {
            return $type;
        }

    }

    private function getProviderAlias()
    {
        $alias = $this->request->post('type');
        if (! $alias) $alias = $this->request->get('type');
        return $alias;
    }


}