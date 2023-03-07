<?php

OTBase::import('system.lib.service.*');
OTBase::import('system.lib.service.OrderItem.Picture');

class OrderController extends GeneralContoller {

    const MAX_USER_PROFILES_COUNT = 3;

    /**
     * @var Basket
     */
    protected $basket;

    /**
     * @var CollectionSummaries
     */
    protected $collectionSummaries;

    /**
     * @var OrdersProxy
     */
    protected $ordersProxy;

    public function quickOrderAction() {
        $quantity = $this->request->getValue('quantity');
        $itemWeight = $this->request->getValue('itemWeight');
        $deliveryId = $this->request->getValue('deliveryId', 0);
        $totalPrice = $this->request->getValue('totalPrice', 0);
        $items = $this->request->getValue('items');

        $totalWeight = $quantity * $itemWeight;
        $provider = $alias = $this->request->getValue('type');
        $sid = Session::getUserOrGuestSession();

        //Get user profiles
        $profiles = array();
        if (Session::getUserData()) {
            $profiles = $this->otapilib->GetUserProfileInfoList($sid);
        }

        $settings = $this->otapilib->GetCommonInstanceOptionsInfo();
        $minOrderCost = $settings['order'];

        //Countries
        $countries = $this->otapilib->GetDeliveryCountryInfoList();

        $defaultCountry = $this->getDefaultCountry($profiles, $countries);

        // Cities
        $cities = array();
        if (!empty($defaultCountry)) {
            $cities = $this->getCities($defaultCountry, '');
        }

        $basketGroups = array();
        $basketGroups['TotalCost']['sign'] = User::getObject()->getCurrencySign();
        $basketGroups['TotalCost']['value'] = $totalPrice;

        $userData = $this->getUserInfoArray($sid);

        $this->sendAjaxResponse(array(
            'layout' => $this->renderPartial('controllers/order/delivery-tab', [
                'provider' => $provider,
                'profiles' => $profiles,
                'countries' => $countries,
                'cities' => $cities,
                'items' => $items,
                'deliveryId' => $deliveryId,
                'userData' => $userData,
                'totalWeight' => $totalWeight,
                'basketGroups' => $basketGroups,
                'isQuickOrder' => true,
                'minOrderCost' => $minOrderCost,
            ])
        ));
    }

    public function quickOrderCreateAction() {
        try {
            $sid = Session::getUserSession();
            $language = Session::getActiveLang();
            $order = $this->request->getValue('order');
            $profile = $this->request->getValue('Profile');
            $items = $this->request->getValue('items');

            if (empty($profile['ExternalDeliveryId'])) {
                throw new Exception(Lang::get('Without_delivery'));
            }
            User::getObject()->setExternalDeliveryId($profile['ExternalDeliveryId']);

            // обновить/создать профиль перед созданием заказа
            $profile = $this->saveProfile($profile);

            $orderComment = $this->parseComment($order);

            $xmlParams = $this->_xmlNewQuickOrder($items, $profile, $orderComment);

            OTAPILib2::AddOrder($language, $sid, $xmlParams, $orderInfo);
            OTAPILib2::makeRequests();

            $orderId = $orderInfo->GetResult()->GetId();

            Plugins::runSerialEvent('onAfterSuccesCreateOrder', array('order' => $orderInfo));

            $userData = new UserData();
            $userData->ClearUserDataCache();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse([
            'orderId' => $orderId,
            'redirectUrl' => '/?p=orderdetails&orderid=' . $orderId,
        ]);
    }

    public function createAction() {
        $salesLineIds = $this->request->getValue('items');
        $provider = $this->checkProviderType();
        $deliveryId = $this->request->getValue('deliveryId', 0);

        $basketGroups = $this->getBasketItems($salesLineIds, true);
        if (empty($basketGroups)) {
            header('Location: /?p=basket');
        }

        $sid = Session::getUserOrGuestSession();
        $userdiscount = $this->otapilib->GetDiscountGroup($sid);

        // Get other orders list
        $xmlSearchParameters = "<OrderSearchParametersForUser><ProviderType>" . $provider . "</ProviderType><IsAvailableForRecreation>true</IsAvailableForRecreation></OrderSearchParametersForUser>";
        $orders = $this->otapilib->SearchOrdersForUser($sid, $xmlSearchParameters, 0, 100);

        //Get user profiles
        $profiles = array();
        if (Session::getUserData()) {
            $profiles = $this->otapilib->GetUserProfileInfoList($sid);
        }

        $settings = $this->otapilib->GetCommonInstanceOptionsInfo();
        $minOrderCost = $settings['order'];

        //Countries
        $countries = InstanceProvider::getObject()->getDeliveryCountryInfoList();
        $countries = $this->otapilib->GetDeliveryCountryInfoList($countries->asXML());

        $defaultCountry = $this->getDefaultCountry($profiles, $countries);

        // Cities
        $cities = array();
        if (!empty($defaultCountry)) {
            $cities = $this->getCities($defaultCountry, '');
        }

        $userData = $this->getUserInfoArray($sid);

        $items = [];
        $totalWeight = 0;
        foreach ($basketGroups as $groupId => $groupData) {
            if (isset($groupData['items'])) {
                foreach ($groupData['items'] as $i => $item) {
                    $items[] = $i;

                    $itemWeight = (float)str_replace(',', '.', $item->weight);
                    $itemQty = (int)$item->quantity;
                    $totalWeight += ($itemWeight * $itemQty);
                }
            }
        }

        $this->defineCrumbs(Lang::get('reg_order'));
        return $this->render('controllers/order/create', [
            'basketGroups' => $basketGroups,
            'provider' => $provider,
            'userdiscount' => $userdiscount,
            'orders' => $orders,
            'profiles' => $profiles,
            'countries' => $countries,
            'cities' => $cities,
            'minOrderCost' => $minOrderCost,
            'deliveryId' => $deliveryId,
            'userData' => $userData,
            'items' => $items,
            'totalWeight' => $totalWeight
        ]);
    }

    private function getCities($countryId, $query)
    {
        $cities = array();
        $citiesSource = $this->searchCities($countryId, $query);
        foreach ($citiesSource->GetContent()->GetItem() as $item) {
            $cities[] = array(
                'countryId' => $item->GetCountryId(),
                'countryName' => $item->GetCountryName(),
                'regionName' => $item->GetRegionName(),
                'cityCode' => $item->GetCityCode(),
                'cityName'=> $item->GetCityName(),
                'displayName' => $item->GetDisplayName()
            );
        }
        return $cities;
    }

    private function searchCities($countryId, $queryText, $framePosition = 0, $frameSize = 20)
    {
        $language = Session::getActiveLang();
        $xmlParams = new SimpleXMLElement('<SearchParameters></SearchParameters>');
        $xmlParams->addChild('CountryId', htmlspecialchars($countryId));
        if (empty($queryText)) {
            $xmlParams->addChild('IsMainCity', 'true');
        } else {
            $xmlParams->addChild('QueryText', htmlspecialchars($queryText));
        }
        $xmlParams = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());

        OTAPILib2::SearchCities($language, $xmlParams, $framePosition, $frameSize, $request);
        OTAPILib2::makeRequests();
        return $request->GetResult();
    }

    public function getCitiesAction()
    {
        $countryId = $this->request->getValue('country', 'RU');
        $query = $this->request->getValue('query', '');

        $cities = $this->getCities($countryId, $query);

        $this->sendAjaxResponse(array('cities' => $cities));
    }

    public function getExternalDeliveriesAction() {
        $weight = str_replace(' ', '', $this->request->getValue('weight'));
        $country = $this->request->getValue('country');
        $city = $this->request->getValue('city');
        $provider = $this->request->getValue('type');
        $deliveryId = $this->request->getValue('deliveryId', 0); // продоставка
        $profileDeliveryId = $this->request->getValue('profileDeliveryId', 0); // доставка из профиля
        $selectedDeliveryId = $this->request->getValue('selectedDeliveryId', 0); // доставка выбранная пользователем

        $instanceProvider = InstanceProvider::getObject();
        $provider = $instanceProvider->GetProviderNameByAlias(Session::getActiveLang(), $provider);

        $countryPrices = array();
        $deliveriesIds = array();
        $modes = array();

        try {
            $deliveryModes = $this->searchDeliveryModes($country, $provider, 0, $weight, $city);
            $modes = $deliveryModes['Content'];

            if ($deliveryId) {
                $deliveryMode = $this->searchDeliveryModes($country, $provider, $deliveryId, $weight, $city);
                $modes = array_merge($deliveryMode['Content'], $modes);
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        if ($modes && is_array($modes)) {
            foreach ($modes as &$m) {
                $deliveriesIds[] = $m['id'];
                foreach ($m['FullPrice']['ConvertedPriceList']['DisplayedMoneys'] as $i => $d) {
                    $price = $d;
                    $price->price = (float) $d;
                    $m['FullPrice']['ConvertedPriceList']['DisplayedMoneys'][$i] = $price;
                }
            }

            if (empty($modes) && $deliveryId) {
                $this->respondAjaxError(Lang::get('Defined_delivery_mode_not_found_Please_select_other'));
            }

            $countryPrices = $modes;
        }

        // 1. Доставка выбранная пользователем
        // 2. Доставка из продоставки
        // 3. Доставка из выбранного профиля
        // 4. Доставка из UserPreferences
        // 5. Самая дешевая (допускается что первая в списке - самая дешевая доставка)
        if (in_array($selectedDeliveryId, $deliveriesIds)) {
            $defaultDelivery = $selectedDeliveryId;
        } elseif (in_array($deliveryId, $deliveriesIds)) {
            $defaultDelivery = $deliveryId;
        } elseif (in_array($profileDeliveryId, $deliveriesIds)) {
            $defaultDelivery = $profileDeliveryId;
        } elseif (in_array(User::getObject()->getUserPreferences()->GetExternalDeliveryId(), $deliveriesIds)) {
            $defaultDelivery = User::getObject()->getUserPreferences()->GetExternalDeliveryId();
        } else {
            $defaultDelivery = isset($deliveriesIds[0]) ? $deliveriesIds[0] : 0;
        }

        $this->sendAjaxResponse(array(
            'countryPrices' => $countryPrices,
            'defaultDelivery' => $defaultDelivery
        ));
    }

    /**
     * @param $deliveryModeId
     * @param $countryCode
     * @param $cityCode
     * @param int $framePosition
     * @param int $frameSize
     * @return DataSubListOfOtapiPickupPointInfo
     */
    private function searchPickupPoints($deliveryModeId, $countryCode, $cityCode, $framePosition = 0, $frameSize = 20)
    {
        $language = Session::getActiveLang();
        $xmlParams = new SimpleXMLElement('<DeliveryPickupPointSearchParameters></DeliveryPickupPointSearchParameters>');
        $xmlParams->addChild('DeliveryModeId', htmlspecialchars($deliveryModeId));
        $xmlParams->addChild('CountryCode', htmlspecialchars($countryCode));
        $xmlParams->addChild('CityCode', htmlspecialchars($cityCode));
        $xmlParams = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());

        OTAPILib2::SearchDeliveryPickupPoints($language, $xmlParams, $framePosition, $frameSize, $request);
        OTAPILib2::makeRequests();
        return $request->GetResult();
    }

    public function getPickupPointsAction()
    {
        $countryCode = $this->request->getValue('country');
        $cityCode = $this->request->getValue('city');
        $deliveryModeId = $this->request->getValue('deliveryMode');
        $profilePickupPointCode = $this->request->getValue('profilePickupPointCode');

        try {
            $deliveryPickupPointsSource = $this->searchPickupPoints($deliveryModeId, $countryCode, $cityCode);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $pickupPoints = array();
        foreach ($deliveryPickupPointsSource->GetContent()->GetItem() as $item) {
            $pickupPoints[] = array(
                'CountryCode' => $item->GetCityCode(),
                'CityCode' => $item->GetCityCode(),
                'PickupPointCode' => $item->GetPickupPointCode(),
                'DisplayName' => $item->GetDisplayName(),
                'Description'=> $item->GetDescription(),
                'GeoCoordinates' => $item->GetGeoCoordinates(),
                'PostalCode' => $item->GetPostalCode(),
                'Address' => $item->GetAddress()
            );
        }
        if ($profilePickupPointCode == 0 && !empty($pickupPoints)) {
            $profilePickupPointCode = $pickupPoints[0]['PickupPointCode'];
        }
        $this->sendAjaxResponse(array(
            'pickupPoints' => $pickupPoints,
            'defaultPickupPointCode' => $profilePickupPointCode
        ));
    }

    public function setItemCommentAction() {
        $sid = Session::getUserOrGuestSession();
        $id = $this->request->getValue('id');
        $comment = $this->request->getValue('comment');
        $fields = '<Fields><FieldInfo Name="Comment" Value="' . TextHelper::escape($comment) . '"/></Fields>';
        $this->otapilib->EditBasketItemFields($sid, $id, $fields);
        $this->sendAjaxResponse(array());
    }

    public function setItemWeightAction() {
        $sid = Session::getUserOrGuestSession();
        $id = $this->request->getValue('id');
        $weight = $this->request->getValue('weight');
        if ($weight < 0) {
            $this->respondAjaxError(new Exception('Incorrect_weight'));
        }
        $fields = '<Fields><FieldInfo Name="Weight" Value="' . $weight . '"/></Fields>';
        $this->otapilib->EditBasketItemFields($sid, $id, $fields);
        $basket = $this->otapilib->GetBasket($sid);

        $this->sendAjaxResponse(array('basket' => $basket));
    }

    private function checkProviderType($onlyGetType = false) {
        $instanceProvider = InstanceProvider::getObject();

        $alias = $this->request->getValue('type');
        if (! $alias) {
            $alias = $this->request->request('Provider');
        }
        return $instanceProvider->GetProviderNameByAlias(Session::getActiveLang(), $alias);
    }

    private function getBasketItems($items, $groupBy = false) {
        if (empty($items)) {
            OTAPILib2::GetBasket(
                Session::getActiveLang(), Session::getUserOrGuestSession(), $partBasket
            );
        } else {
            /** @var OtapiCollectionInfoAnswer $partBasket */
            OTAPILib2::GetPartialBasket(
                Session::getActiveLang(), Session::getUserOrGuestSession(), $items, $partBasket
            );
        }
        OTAPILib2::makeRequests();

        $this->basket = $partBasket->GetCollectionInfo();

        $basketGroups = array();
        foreach ($partBasket->GetCollectionInfo()->GetElements()->GetElementInfo() as $element) {
            $tmpProduct = Product::getObject($element->GetItemId(), $element);
            if ($groupBy) {
                // определяем группу товара и добавочную стоимость для этой группы
                $groupId = 0;
                $groupDisplayName = Lang::get('additional_fee');
                $groupConvertedPriceList = new OtapiInstanceListOfMoney(null);
                foreach ($partBasket->GetCollectionInfo()->GetAdditionalPriceInfoList()->GetElements()->GetAdditionalPriceInfo() as $additionalPriceInfo) {
                    foreach ($additionalPriceInfo->GetRawData()->ElementIds->Id as $id) {
                        if ($element->GetId() == (string)$id) {
                            $groupId = md5($additionalPriceInfo->GetType().$additionalPriceInfo->GetName());
                            $groupDisplayName = $additionalPriceInfo->GetDisplayName();
                            $groupConvertedPriceList = $additionalPriceInfo->GetPrice()->GetConvertedPriceList();
                            break;
                        }
                    }
                }

                if (!isset($basketGroups[$groupId])) {
                    $basketGroups[$groupId] = array();
                    $basketGroups[$groupId]['price'] = $groupConvertedPriceList->GetInternal()->GetValue();
                    $basketGroups[$groupId]['sign'] = $groupConvertedPriceList->GetInternal()->GetSignAttribute();
                    $basketGroups[$groupId]['displayName'] = $groupDisplayName;
                }
                $basketGroups[$groupId]['items'][$element->GetId()] = $tmpProduct;
            } else {
                if (!isset($basketGroups[0])) {
                    $basketGroups[0] = array();
                    $basketGroups[0]['items'] = array();
                    $basketGroups[0]['price'] = false;
                    $basketGroups[0]['sign'] = false;
                    $basketGroups[0]['displayName'] = false;
                }
                $basketGroups[0]['items'][$element->GetId()] = $tmpProduct;
            }
        }
        // ставим группу "0" на первое место
        if (isset($basketGroups[0])) {
            $tmp = $basketGroups[0];
            unset($basketGroups[0]);
            array_unshift($basketGroups, $tmp);
        }

        $basketGroups['TotalCost'] = array(
            'value' => $partBasket->GetCollectionInfo()->GetTotalCost()->GetConvertedPriceList()->GetInternal()->GetValue(),
            'sign' => $partBasket->GetCollectionInfo()->GetTotalCost()->GetConvertedPriceList()->GetInternal()->GetSignAttribute()
        );
        return $basketGroups;
    }

    public function getTotalCostAction() {
        $items = $this->request->getValue('items');

        $result = array();
        try{
            if (empty($items)) {
                OTAPILib2::GetBasket(Session::getActiveLang(), Session::getUserOrGuestSession(), $partBasket);
            } else {
                OTAPILib2::GetPartialBasket(Session::getActiveLang(), Session::getUserOrGuestSession(), is_array($items) ? implode(',', $items) : $items, $partBasket);
            }
            OTAPILib2::makeRequests();

            $result['TotalCost'] = array(
                'value' => $partBasket->GetCollectionInfo()->GetTotalCost()->GetConvertedPriceList()->GetInternal()->GetValue(),
                'sign' => $partBasket->GetCollectionInfo()->GetTotalCost()->GetConvertedPriceList()->GetInternal()->GetSignAttribute()
            );
        }
        catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse($result);
    }

    private function saveProfile(array $profileInfo)
    {
        $sid = Session::getUserSession();
        $deliveryProfileId = (!empty($profileInfo['Id']) && $profileInfo['Id'] != 'temp') ? $profileInfo['Id'] : false;

        if (!$deliveryProfileId) {
            // Add new profile
            $xmlProfile = $this->xmlParamsDeliveryProfile($profileInfo, true);
            $xmlProfile = str_replace('<?xml version="1.0"?>', '', $xmlProfile);
            $this->otapilib->setErrorsAsExceptionsOn();
            $deliveryProfileId = $this->otapilib->CreateUserProfile($sid, $xmlProfile);
            $this->otapilib->setErrorsAsExceptionsOff();
            $profileInfo['Id'] = $deliveryProfileId;
        } else {
            $profile = new Profile();
            $paramsProfile = array('Profile' => $profileInfo);
            // update existing profile
            list($success, $message) = $profile->saveDeliveryProfile($paramsProfile);
            if (!$success) {
                throw new Exception($message);
            }
        }
        return $profileInfo;
    }

    public function updateProfileAction()
    {
        $profileInfo = $this->request->getValue('Profile');
        try {
            $profile = $this->saveProfile($profileInfo);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse(array('profile' => $profile));
    }

    public function xmlParamsDeliveryProfile($fields, $new = true) {
        if ($new) {
            $xml = new SimpleXMLElement('<UserProfileCreateData></UserProfileCreateData>');
        } else {
            $xml = new SimpleXMLElement('<UserProfileUpdateData></UserProfileUpdateData>');
        }

        $xml->addChild('FirstName', htmlspecialchars($fields['FirstName']));
        $xml->addChild('LastName', htmlspecialchars($fields['LastName']));
        $xml->addChild('EnableValidation', true);
        if (isset($fields['MiddleName'])) {
            $xml->addChild('MiddleName', htmlspecialchars($fields['MiddleName']));
        }
        if (isset($fields['ExternalDeliveryId'])) {
            $xml->addChild('ExternalDeliveryId', htmlspecialchars($fields['ExternalDeliveryId']));
        } else {
            $xml->addChild('ExternalDeliveryId', '');
        }
        if (isset($fields['PickupPointCode'])) {
            $xml->addChild('PickupPointCode', htmlspecialchars($fields['PickupPointCode']));
        } else {
            $xml->addChild('PickupPointCode', '');
        }
        if (isset($fields['CountryCode'])) {
            $xml->addChild('CountryCode', htmlspecialchars($fields['CountryCode']));
        }
        if (isset($fields['CityCode'])) {
            $xml->addChild('CityCode', htmlspecialchars($fields['CityCode']));
        }
        $xml->addChild('City', htmlspecialchars($fields['City']));
        $xml->addChild('Address', htmlspecialchars($fields['Address']));

        $xml->addChild('Phone', htmlspecialchars($fields['Phone']));
        $xml->addChild('PostalCode', htmlspecialchars($fields['PostalCode']) ? $fields['PostalCode'] : '000000');
        $xml->addChild('Region', htmlspecialchars($fields['Region']));

        if (in_array('PassportData', General::$enabledFeatures)) {
            if (isset($fields['PassportNumber']))
                $xml->addChild('PassportNumber', htmlspecialchars($fields['PassportNumber']));
            if (isset($fields['RegistrationAddress']))
                $xml->addChild('RegistrationAddress', htmlspecialchars($fields['RegistrationAddress']));
        }

        return $xml->asXML();
    }

    public function createOrderAction() {
        $this->ordersProxy = new OrdersProxy($this->otapilib);
        $sid = Session::getUserSession();
        try {
            $order = $this->request->getValue('order');
            $profile = $this->request->getValue('Profile');

            if (empty($profile['ExternalDeliveryId']) && $order['baseOrderId'] == 'new') {
                throw new Exception(Lang::get('Without_delivery'));
            }

            // обновить/создать профиль перед созданием заказа
            $profile = $this->saveProfile($profile);

            $order['items'] = $this->getBasketItems($order['items']);

            $order['items'] = $order['items'][0]['items'];
            if (empty($order['items'])) {
                throw new Exception('Items list is empty');
            }
            $order['weight'] = $this->getWeight($order['items']);

            $comment = $this->parseComment($order);

            $ids = array();
            foreach ($order['items'] as $k => $item) {
                $ids[] = $k;
            }

            $this->otapilib->setErrorsAsExceptionsOn();
            if ($order['baseOrderId'] == 'new') {
                User::getObject()->setExternalDeliveryId($profile['ExternalDeliveryId']);
                $orderInfo = Plugins::onCreateOrder($sid, $profile['ExternalDeliveryId']);
                $xmlParams = str_replace('<?xml version="1.0"?>', '', $this->_xmlNewOrder($profile['ExternalDeliveryId'], $comment, $profile['Id'], $ids));
                if ($orderInfo === 0) {
                    $orderInfo = $this->ordersProxy->CreateOrder($sid, $xmlParams);
                }
                $orderId = (string) $orderInfo->Result->Id;
            } else {
                $orderInfo = $this->otapilib->RecreateOrder($sid, $this->_xmlRecreateOrder($order['baseOrderId'], $ids));
                $orderId = $order['baseOrderId'];
            }

            if (isset($orderInfo->Result)) {
                Plugins::runSerialEvent('onAfterSuccesCreateOrder', array('order' => $orderInfo));

                $newRedirect = Plugins::invokeEvent('onNewAfterOrderRedirect', array('delivery' => (string) $orderInfo->Result->DeliveryModeId, 'orderInfo' => $orderInfo));
                $order_array['Id'] = $order_array['id'] = (string) $orderInfo->Result->Id;
                $order_array['TotalAmount'] = (string) $orderInfo->Result->TotalAmount;
                $orderInfo = $order_array;
            }

            // Notifications
            $userData = new UserData();
            $userData->ClearUserDataCache();
        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse(array('result' => 'ok', 'redirectUrl' => '/?p=orderdetails&orderid=' . $orderId . '&tab=3', 'orderinfo' => $orderInfo));
    }

    private function _xmlNewOrder($deliveryModeId, $comment, $profile, $ids) {
        $xml = new SimpleXMLElement('<OrderCreateData></OrderCreateData>');
        $xml->addChild('DeliveryModeId', htmlspecialchars($deliveryModeId));
        $xml->addChild('Comment', htmlspecialchars($comment));
        $xml->addChild('UserProfileId', htmlspecialchars($profile));
        $xml->addChild('Elements');
        foreach ($ids as $id) {
            $xml->Elements->addChild('Id', $id);
        }

        return str_replace('<?xml version="1.0"?>', '', $xml->asXML());
    }

    private function _xmlNewQuickOrder($items, $profile, $orderComment) {
        $xml = new SimpleXMLElement('<OrderAddData></OrderAddData>');
        $xml->addChild('DeliveryModeId', htmlspecialchars($profile['ExternalDeliveryId']));
        $xml->addChild('UserProfileId', htmlspecialchars($profile['Id']));
        $xml->addChild('Comment', $orderComment);

        $xmlItems = $xml->addChild('Items');
        foreach ($items as $item) {
            $xmlItem = $xmlItems->addChild('Item');
            $xmlItem->addChild('Id', $item['id']);
            $xmlItem->addChild('ConfigurationId', $item['configurationId']);
            $xmlItem->addChild('Quantity', $item['quantity']);
        }

        return str_replace('<?xml version="1.0"?>', '', $xml->asXML());
    }

    private function _xmlRecreateOrder($orderId, $ids) {
        $xml = new SimpleXMLElement('<OrderRecreateData></OrderRecreateData>');
        $xml->addChild('OrderId', htmlspecialchars($orderId));
        $xml->addChild('Elements');
        foreach ($ids as $id) {
            $xml->Elements->addChild('Id', $id);
        }

        return trim(str_replace('<?xml version="1.0"?>', '', $xml->asXML()));
    }

    private function getWeight($items) {
        $weight = 0;

        foreach ($items as $item) {
            $weight += $item->weight ? (float) $item->weight * $item->quantity : 0;
        }

        return round($weight, 3);
    }

    public function payAction() {
        $this->defineCrumbs(Lang::get('payment'));
        return $this->renderPartial('controllers/order/order', [
            'mode' => 'pay'
        ]);
    }

    public function getPackageTrackingAction()
    {
        $packageId = $this->request->getValue('packageId');
        $lang = Session::getActiveLang();
        $sid = User::getObject()->getSid();

        try {
            OTAPILib2::GetPackageTracking($lang, $sid, $packageId, $answer);
            OTAPILib2::makeRequests();
            $result = $answer->GetResult();

            $data = [];
            $data['externalTrackingUrl'] = $result->GetExternalTrackingUrl();
            foreach ($result->GetPackageTrackingCheckpoints()->GetContent()->GetItem() as $checkpoint) {
                $data['packageTrackingCheckpoints'][] = [
                    'time' => $checkpoint->GetTime(),
                    'status' => $checkpoint->GetStatus(),
                    'location' => $checkpoint->GetLocation()
                ];
            }

            $this->sendAjaxResponse($data);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }

    public function orderinfoAction() {
        $this->otapilib->setErrorsAsExceptionsOn();
        if (!Session::getUserData()) {
            Users::Logout();
            header('Location: /?p=login');
            return;
        }
        if (!$this->request->valueExists('orderid')) {
            header('Location:/?p=privateoffice');
        }
        $orderId = $this->request->getValue('orderid');

        $orderInfo = $this->getOrderInfo($orderId, Session::getUserSession());
        $remainAmount = (string) $orderInfo['orderinfo']->GetSalesOrderInfo()->GetRemainAmount();
        $remainAmount = floatval($remainAmount);
        $mode = 'info';
        $this->defineCrumbs(Lang::get('order').' '.OrdersProxy::normalizeOrderId($orderInfo['orderinfo']->GetSalesOrderInfo()->GetId()));

        if ($remainAmount > 0 && !$orderInfo['confirmPrice'] && $orderInfo['supportMessages']['unreadCount'] == 0) {
            $mode = 'pay';
            $this->defineCrumbs(Lang::get('order').' '.OrdersProxy::normalizeOrderId($orderInfo['orderinfo']->GetSalesOrderInfo()->GetId()));
        }

        $statusList = array(
            Lang::get('ordered_goods'),
            Lang::get('goods_in_handling'),
            Lang::get('cancelled_goods'),
            Lang::get('goods_with_questions')
        );

        return $this->render('controllers/order/order', [
            'mode' => $mode,
            'orderInfo' => $orderInfo['orderinfo'],
            'vars' => [
                'statusList' => $statusList,
                'orderInfo' => $orderInfo['orderinfo'],
                'photoReport' => $orderInfo['photoReport'],
                'shippingInfo' => $orderInfo['shippingInfo'],
                'supportMessages' => $orderInfo['supportMessages'],
                'pristroyItems' => $orderInfo['pristroyItems'],
                'orderComment' => $orderInfo['orderComment'],
            ]
        ]);
    }

    private function getOrderInfo($orderId, $sid) {
        $result = array();
        $orderInfo = false;
        $accountInfo = false;
        $shippingInfo = false;

        OTAPILib2::GetSalesOrderDetails(Session::getActiveLang(), $sid, $orderId, $orderInfo);
        OTAPILib2::GetAccountInfo(Session::getActiveLang(), $sid, $accountInfo);
        OTAPILib2::GetSalesOrderShippings(Session::getActiveLang(), $sid, $orderId, $shippingInfo);
        OTAPILib2::makeRequests();
        $orderInfo = $orderInfo ? $orderInfo->GetResult() : array();
        $accountInfo = $accountInfo ? $accountInfo->GetResult() : array();
        $shippingInfo = $shippingInfo ? $shippingInfo->GetResult() : array();

        $result['orderinfo'] = $orderInfo; //->GetSalesOrderInfo()->GetCustComment();
        // $orderInfo->GetSalesLinesList()->GetSalesLine()
        //$result['userinfo'] = $userinfo;
        $result['accountinfo'] = $accountInfo;
        $result['shippingInfo'] = $shippingInfo;
        $result['confirmPrice'] = false;
        $result['photoReport'] = $this->getPhotoReport($orderInfo);
        $result['supportMessages'] = $this->getSupportMessages($orderId);
        $result['orderComment'] = OrdersProxy::prepareOrderComment($orderInfo->GetSalesOrderInfo()->GetCustComment());
        
        $ids = array(); 
        foreach ($orderInfo->GetSalesLinesList()->GetSalesLine() as $item) {
        	$ids[] = $item->GetId();
            if ($item->GetStatusCode() == 3) {
                $result['confirmPrice'] = true;
            }
        }
        
        $pristroy = new PristroyRepository($this->cms);
        $pristroyItems = $pristroy->getListByItemIds($ids);
        $result['pristroyItems'] = $pristroyItems;

        return $result;
    }

    private function defineCrumbs($title) {
        $crumbs = [
            ['title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()],
            ['title' => Lang::get('private_office'), 'url' => UrlGenerator::toRoute('privateoffice')],
            ['title' => Lang::get('orders'), 'url' => UrlGenerator::toRoute('privateoffice', ['orderstate' => 1])],
            ['title' => $title]
        ];
        CrumbsController::setCrumbs($crumbs);
    }

    private function defineCrumbsOrder($title) {
        $crumbs = array(
            array('title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()),
            array('title' => Lang::get('cart'), 'url' => '/basket'),
            array('title' => Lang::get('create_order'), 'url' => '/?p=userorder&step1'),
            array('title' => $title)
        );
        CrumbsController::setCrumbs($crumbs);
    }

    private function _generateXmlForSearchDelivery($provider, $country, $weight, $deliveryId = false, $city = '') {
        $xml = new SimpleXMLElement('<DeliveryModeSearchParameters></DeliveryModeSearchParameters>');
        $xml->addChild('CountryCode', htmlspecialchars($country));
        if (!empty($city)) {
            $xml->addChild('CityCode', htmlspecialchars($city));
        }
        $xml->addChild('Weight', htmlspecialchars($weight));
        $xml->addChild('ProviderType', htmlspecialchars($provider));
        if ($deliveryId) {
            $xml->addChild('DeliveryModeIds');
            $xml->DeliveryModeIds->addChild('Id', $deliveryId);
        }
        return str_replace('<?xml version="1.0"?>', '', $xml->asXML());
    }

    public function removeOrderItemAction() {
        $orderId = $this->request->getValue('orderId');
        $salesLineId = $this->request->getValue('salesLineId');
        $answer = false;
        try {
            OTAPILib2::CancelLinesOrder(Session::getActiveLang(), Session::getUserSession(), $orderId, $salesLineId, $answer);
            OTAPILib2::makeRequests();
            if ($answer) {
                $userData = new UserData();
                $userData->ClearAccountInfoCache();
                $this->sendAjaxResponse(array('result' => true, 'message' => 'Ok'));
            } else {
                $this->sendAjaxResponse(array('result' => false, 'message' => 'Error'));
            }
        } catch (Exception $e) {
            $this->sendAjaxResponse(array('result' => false, 'message' => $e->getMessage()));
        }
    }

    public function cancelOrderAction() {
        $orderId = $this->request->getValue('orderId');
        $answer = false;
        try {
            OTAPILib2::CancelSalesOrder(Session::getUserSession(), $orderId, $answer);
            OTAPILib2::makeRequests();
            if ($answer) {
                $userData = new UserData();
                $userData->ClearAccountInfoCache();
                $this->sendAjaxResponse(array('result' => true));
            } else {
                $this->sendAjaxResponse(array('result' => false));
            }
        } catch (ServiceException $e) {
            $this->throwAjaxError($e);
        }
    }

    public function confirmItemPriceAction() {
        $orderId = $this->request->getValue('orderId');
        $salesLineIds = $this->request->getValue('salesLineId');

        $answer = false;
        try {
            OTAPILib2::ConfirmPriceLinesOrder(Session::getActiveLang(), Session::getUserSession(), $orderId, $salesLineIds, $answer);
            OTAPILib2::makeRequests();
            if ($answer) {
                $this->sendAjaxResponse(array('result' => true, 'message' => 'Ok'));
            } else {
                $this->sendAjaxResponse(array('result' => false, 'message' => 'Error'));
            }
        } catch (Exception $e) {
            $this->sendAjaxResponse(array('result' => false, 'message' => $e->getMessage()));
        }
    }

    private function getPhotoReport($orderInfo) {
        $result = array();
        $orderId = $orderInfo->GetSalesOrderInfo()->GetId();
        foreach ($orderInfo->GetSalesLinesList()->GetSalesLine() as $item) {
            $oldWebcamFiles = glob(CFG_APP_ROOT . '/files/ItemCam/' . $orderId . '-' . $item->GetId() . '*.jpg');
            $oldWebcamFiles = is_array($oldWebcamFiles) ? $oldWebcamFiles : array();

            $uploadedFiles = glob(CFG_APP_ROOT . '/uploaded/items/' . $item->GetId() . '/' . $orderId . '/*.*');
            $uploadedFiles = is_array($uploadedFiles) ? $uploadedFiles : array();

            $operatorFiles = array();
            $operatorComment = $item->GetOperatorComment();
            $operatorComment = str_replace('\n', "\n", $operatorComment);
            preg_match_all('#https?:\/\/\S+\/(.+)(jpg | jpeg | png | ico | gif | bmp)#si', $operatorComment, $m);
            if (!empty($m[0])) {
                foreach ($m[0] as &$url) {
                    $url = str_replace(' ', '%20', $url);
                }
                $operatorFiles = $m[0];
            }

            $pictures = array_merge($oldWebcamFiles, $uploadedFiles, $operatorFiles);
            $picturesFull = array();
            foreach ($pictures as $file) {
                $picturesFull[] = new Picture($file);
                $operatorComment = str_replace($file, '', $operatorComment);
            }
            $result[$item->GetId()]['operatorComment'] = trim(str_replace(array('\n', '\\'), array("\n", '/'), stripslashes($operatorComment)));
            $result[$item->GetId()]['operatorImages'] = $picturesFull;
        }
        return $result;
    }

    private function getSupportMessages($orderId) {
        $result = array('tickets' => array(), 'unreadCount' => 0);
        $arFilter = array();

        $this->SupportRepository = new SupportRepositoryNew(new CMS());

        $sid = Session::getUserSession();
        try {
            $ticketlist = $this->SupportRepository->getTicketInfoList((int) User::getObject()->getId(), $arFilter);

            foreach ($ticketlist as &$ticket) {
                if ($ticket['OrderId'] == $orderId) {
                    $ticket['messages'] = $this->SupportRepository->getTicketMessageList(User::getObject()->getId(), $ticket['id'], true);
                    $ticket['username'] = User::getObject()->getFIO();
                    $result['tickets'][] = $ticket;
                    $result['unreadCount'] += $ticket['newmsgcount'];
                    if ($ticket['newmsgcount'] && !OTBase::isAdmin()) {
                        $this->SupportRepository->markRead($ticket['id'], 'Out');
                    }
                    if (OTBase::isAdmin()) {
                        $this->SupportRepository->markRead($ticket['id'], 'In', 'Answer');
                    }
                }
            }
        } catch (DBException $e) {
            Session::setError($e->getMessage(), 'DBError');
        }
        return $result;
    }

    public function addMessageAction() {
        $orderId = $this->request->getValue('orderId');
        $ticketId = $this->request->getValue('ticketId');
        $message = $this->request->getValue('message');

        $this->SupportRepository = new SupportRepositoryNew(new CMS());

        $userId = User::getObject()->getId();
        try {
            $ticket = $this->SupportRepository->getTicketDetails($userId, $ticketId);
            if ($ticket['Status'] === 'close') {
                throw new Exception(Lang::get('ticket_closed'));
            }
            $add = $this->SupportRepository->createTicketMessage($userId, $ticketId, $message, false);
            $this->SupportRepository->setLoginTicket($ticketId, User::getObject()->getLogin());

            if (OTBase::isAdmin()) {
                $this->SupportRepository->markRead($ticketId, 'Answer', 'Answered');
            }

            $data['userid'] = User::getObject()->getId();
            $data['id'] = $ticketId;
            $data['login'] = User::getObject()->getLogin();
            Notifier::generalNotification('new_ticket', Lang::get('new_ticket'), $data);
        } catch (DBException $e) {
            $this->throwAjaxError($e);
        }

        $this->sendAjaxResponse(array('result' => true, 'creationDate' => date('Y-m-d H:i', $add[2]), 'user' => User::getObject()->getFIO()));
    }

    public function closeLineOrderAction()
    {
        $language = Session::getActiveLang();
        $sid = User::getObject()->getSid();
        $orderId = $this->request->getValue('orderId');
        $salesLineId = $this->request->getValue('salesLineId');

        try {
            OTAPILib2::CloseLineSalesOrder($language, $sid, $orderId, $salesLineId, $answer);
            OTAPILib2::makeRequests();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }

    private function searchDeliveryModes($countryCode, $provider, $deliveryId, $itemsWeight, $city = '')
    {
        $xmlParams = $this->_generateXmlForSearchDelivery($provider, $countryCode, $itemsWeight, $deliveryId, $city);
        return $this->otapilib->SearchDeliveryModes($xmlParams);
    }

    private function parseComment($order)
    {
        $comment = htmlspecialchars((string)$order['orderComment'] . " \r\n " . Plugins::invokeEvent('onOrderGetDomainName'));
        if (isset($order['originPackage']) && !empty($order['originPackage'])) {
            $comment .= '  ' . Lang::get('save_origin_package');
        }
        if (isset($order['insurance']) && !empty($order['insurance'])) {
            $order_insurance = 0;
            if (General::getConfigValue('order_insurance_percent')) {
                $order_insurance = General::getConfigValue('order_insurance_percent');
            }
            if ($order_insurance > 0)
                $comment .= '  (INSURANCE:+' . $order_insurance . '%)';
        }
        if (defined('CFG_DISCOUNTER_REORDER') && Session::get('DISCOUNTER_REORDER')) {
            $comment = Lang::get('Need_to_merge_with_order_upper') . ' ' . Session::get('DISCOUNTER_REORDER') . "\r\n" . $comment;
        }
        return $comment;
    }

    private function getDefaultCountry($profiles, $countries)
    {
        $userCountry = User::getObject()->getCountryCode();
        if (isset($profiles[0])) {
            $defaultCountry = $profiles[0]['CountryCode'];
        } elseif ($userCountry) {
            $defaultCountry = $userCountry;
        } elseif (isset($countries[0])) {
            $defaultCountry = $countries[0]['Id'];
        } else {
            $defaultCountry = '';
        }
        return $defaultCountry;
    }

    private function getUserInfoArray($sid)
    {
        $this->otapilib->setErrorsAsExceptionsOn();
        try {
            $userData = $this->otapilib->GetUserInfo($sid);
        } catch (Exception $e) {
            $userData = array(
                'LastName' => '',
                'FirstName' => '',
                'MiddleName' => '',
                'Phone' => '',
            );
        }
        $this->otapilib->setErrorsAsExceptionsOff();
        return $userData;
    }

}
