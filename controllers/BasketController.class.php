<?php

class BasketController extends GeneralContoller
{
    public function defaultAction()
    {
        CrumbsController::setCrumbs([
            ['title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()],
            ['title' => Lang::get('cart'), 'url' => UrlGenerator::getUrl('basket')]
        ]);

        return $this->render('controllers/basket/index');
    }

    public function getBasketAction()
    {
        $lang = Session::getActiveLang();
        $user = User::getObject();
        $sid = $user->getSid();
        $userAuthenticated = $user->isAuthenticated();
        $currencySign = InstanceProvider::getObject()->GetInternalCurrency()->GetCode();
        $currencyCode = $user->getCurrencyCode();

        $minOrderTotalCost = 0;
        $providers = array();
        $checked = array();
        $currentProviderExists = false;

        $currentProviderAlias = $this->request->getValue('activeProvider');
        $activeLines = $this->request->getValue('activeBasketLines');
        if ($this->request->valueExists('activeProvider')) {
            $activeLines = $activeLines ? explode(',', $activeLines) : array();
        }
        $instanceProvider = InstanceProvider::getObject();

        try {
            OTAPILib2::simpleRequest('GetBasket', [
                'language' => $lang,
                'sessionId' => $sid,
            ], $basket);
            OTAPILib2::makeRequests();
            $collectionInfo = $basket->GetRawData()->CollectionInfo;

            // проходим по всем товарам в корзине
            if (isset($collectionInfo->Elements->ElementInfo)) {
                foreach ($basket->GetRawData()->CollectionInfo->Elements->ElementInfo as $item) {
                    $item = new OtapiElementInfo($item);
                    $basketLineId = $item->GetId();
                    $itemId = $item->GetItemId();
                    $provider = $item->GetProviderType();
                    $product = Product::getObject($itemId, $item);

                    // создаем массив провайдера
                    if (!key_exists($provider, $providers)) {
                        $alias = $instanceProvider->GetAliasByProviderName($lang, $provider);

                        $providerData = array();
                        $providerData['alias'] = $alias;
                        $providerData['itemsQuantity'] = 0;
                        $providerData['info'] = InstanceProvider::getObject()->GetProviderInfo($lang, $provider);

                        // создаем группу товаров с id=0. в ней содержатся несгруппированные товары
                        $providerData['groups'][0] = array(
                            'name' => '',
                            'displayName' => '',
                            'convertedPriceList' => array(
                                'sign' => $currencySign,
                                'code' => $currencyCode,
                                'value' => 0
                            ),
                            'items' => array()
                        );

                        // проверяем активного провайдера
                        if (!$currentProviderAlias || $currentProviderAlias === $alias) {
                            $currentProviderExists = true;
                            $currentProviderAlias = $alias;
                            $providerData['isCurrent'] = true;
                        } else {
                            $providerData['isCurrent'] = false;
                        }

                        $providers[$provider] = $providerData;
                    }

                    // добавляем товар в не сгруппированные
                    $providers[$provider]['groups'][0]['items'][$basketLineId] = $product;
                    $providers[$provider]['itemsQuantity']++;
                }
            }

            // если мы не нашли активного провайдера, то выставляем первого, как активного
            if (! $currentProviderExists) {
                $keys = array_keys($providers);
                if (! empty($keys)) {
                    $firstProvider = $keys[0];
                    $providers[$firstProvider]['isCurrent'] = true;
                }
            }

            // проходим по провайдерам, что бы получить их группы
            if (isset($collectionInfo->CollectionSummaries->CollectionSummary)) {
                foreach ($basket->GetRawData()->CollectionInfo->CollectionSummaries->CollectionSummary as $summary) {
                    $summary = new OtapiCollectionSummary($summary);
                    $provider = $summary->GetProviderType();

                    // проходим по группам провайдеров, что бы сгруппировать по ним товары
                    foreach ($summary->GetAdditionalPriceInfoList()->GetElements()->GetAdditionalPriceInfo() as $i => $group) {
                        // готовим данные текущей группы
                        $groupName = (string) $group->GetName();
                        $groupId = 'additionalPriceInfo-' . $i;
                        $groupDisplayName = (string) $group->GetDisplayName();

                        // берем цену по выбранной пользователем валюте
                        $convertedPriceList = array();
                        $displayedMoneys = $group->GetPrice()->GetConvertedPriceList()->GetDisplayedMoneys();
                        foreach ($displayedMoneys->GetMoney() as $displayMoney) {
                            if ($displayMoney->GetCodeAttribute() === $currencyCode) {
                                $convertedPriceList = array(
                                    'sign' => (string) $displayMoney->GetSignAttribute(),
                                    'code' => (string) $displayMoney->GetCodeAttribute(),
                                    'value' => (float) $displayMoney->GetValue()
                                );

                                break;
                            }
                        }

                        // добавляем текущую группу в группы провайдера
                        $providers[$provider]['groups'][$groupId] = array(
                            'name' => $groupName,
                            'displayName' => $groupDisplayName,
                            'convertedPriceList' => $convertedPriceList,
                            'items' => array(),
                        );

                        // проходим по товарам текущей группы
                        foreach ($group->GetElementIds()->GetRawData()->children() as $basketLineId) {
                            $basketLineId = (string) $basketLineId;

                            // ищем товар в списке не сгруппированных товаров
                            $ungroupedItem = $providers[$provider]['groups'][0]['items'][$basketLineId];
                            // добавляем товар в текущую группу
                            $providers[$provider]['groups'][$groupId]['items'][$basketLineId] = $ungroupedItem;
                            // удаляем товар из списка не сгруппированных товаров
                            unset($providers[$provider]['groups'][0]['items'][$basketLineId]);
                        }
                    }

                    if (empty($providers[$provider]['groups'][0]['items'])) {
                        // если не сгруппированных товаров не осталось, то удаляем пустой массив из групп
                        unset($providers[$provider]['groups'][0]);
                    }
                }
            }

            // проходим по товарам и выбираем товары/группы, которые нужно отметить
            $checked = $this->getChecked($providers, $activeLines);

            // получаем минимальную стоимость заказа
            $instanceSettings = InstanceProvider::getObject()->GetCommonInstanceOptionsInfo($lang);
            $minOrderTotalCost = $instanceSettings->GetOrder()->GetConvertedMinOrderCost();
            foreach ($minOrderTotalCost->GetDisplayedMoneys()->GetMoney() as $value) {
                if($value->GetSignAttribute() === User::getObject()->getCurrencySign()) {
                    $currencySign = $value->GetSignAttribute();
                    $minOrderTotalCost = $value->GetValue();
                    break;
                }
            }

        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse([
            'content' => $this->renderPartial('controllers/basket/list', [
                'providers' => $providers,
                'userAuthenticated' => $userAuthenticated,
                'checked' => $checked,
                'minOrderTotalCost' => $minOrderTotalCost,
                'currencySign' => $currencySign
            ])
        ]);
    }

    private function getChecked($providers, $activeLines)
    {
        $checked = array();

        foreach ($providers as $providerName => $provider) {
            $allChecked = true;
            foreach ($provider['groups'] as $groupId => $group) {
                $groupChecked = true;
                foreach ($group['items'] as $lineId => $product) {
                    if (is_null($activeLines) || in_array($lineId, $activeLines)) {
                        $checked[$lineId] = true;
                    } else {
                        $checked[$lineId] = false;
                        $groupChecked = false;
                    }
                }

                $checked[$groupId] = $groupChecked;
                $allChecked = $groupChecked ? $allChecked : false;
            }

            $checked[$providerName] = $allChecked;
        }

        return $checked;
    }

    public function moveToFavoriteAction()
    {
        $this->itemsAction('MoveItemFromCartToNote');
    }

    public function deleteAction()
    {
        $this->itemsAction('RemoveItemFromBasket');
    }

    private function itemsAction($method)
    {
        try {
            $sid = User::getObject()->getSid();
            $basketLines = $this->request->getValue('basketLines');
            $language = Session::getActiveLang();

            switch ($method) {
                case 'MoveItemFromCartToNote':
                    OTAPILib2::MoveItemsFromBasketToNote($language, $sid, $basketLines, $request);
                    break;
                case 'RemoveItemFromBasket':
                    OTAPILib2::RemoveItemsFromBasket($language, $sid, $basketLines, $request);
                    break;
            }

            OTAPILib2::makeRequests();

            $userData = new UserData();
            $userData->clearUserDataCache();

            $this->sendAjaxResponse();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }

    public function clearAction()
    {
        try {
            $sid = User::getObject()->getSid();
            OTAPILib2::ClearBasket($sid, $answer);
            OTAPILib2::makeRequests();

            $userData = new UserData();
            $userData->clearUserDataCache();

            $this->sendAjaxResponse();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }

    public function editBasketItemFieldsAction()
    {
        try {
            $sid = User::getObject()->getSid();
            $lineId = $this->request->getValue('lineId');
            $itemUrl = $this->request->getValue('itemUrl');
            $comment = $this->request->getValue('comment');
            $externalDeliveryId = $this->request->getValue('externalDeliveryId');

            $fields = $this->getEditBasketItemFields($itemUrl, $comment, $externalDeliveryId);
            OTAPILib2::EditBasketItemFields($sid, $lineId, $fields, $answer);
            OTAPILib2::makeRequests();

            $this->sendAjaxResponse();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }

    public function getItemInfoAction()
    {
        try {
            $lang = Session::getActiveLang();
            $sid = User::getObject()->getSid();
            $itemId = $this->request->getValue('itemId');

            OTAPILIB2::BatchGetSimplifiedItemFullInfo($lang, $sid, $itemId, '', '<Parameters/>', $answer);
            OTAPILib2::makeRequests();

            $configurations = [];
            foreach ($answer->GetResult()->GetItem()->GetConfigurators()->GetProperty() as $property) {
                $id = $property->GetIdAttribute();
                $displayName = $property->GetDisplayNameAttribute();

                $values = [];
                foreach ($property->GetValue() as $value) {
                    $valueId = $value->GetIdAttribute();
                    $valueDisplayName = $value->GetDisplayNameAttribute();
                    $values[$valueId] = $valueDisplayName;
                }

                $configurations[$id] = [
                    'id' => $id,
                    'displayName' => $displayName,
                    'values' => $values
                ];
            }

            $this->sendAjaxResponse(['configurations' => $configurations]);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }

    public function changeConfigurationAction()
    {
        try {
            $sid = User::getObject()->getSid();
            $lang = Session::getActiveLang();
            $lineId = $this->request->getValue('lineId');
            $itemId = $this->request->getValue('itemId');
            $itemUrl = $this->request->getValue('itemUrl');
            $comment = $this->request->getValue('comment');
            $quantity = $this->request->getValue('quantity');
            $configurations = $this->request->getValue('configurations');
            $externalDeliveryId = $this->request->getValue('externalDeliveryId');

            OTAPILib2::RemoveItemFromBasket($sid, $lineId, $answer);
            OTAPILib2::makeRequests();

            $fields = $this->getEditBasketItemFields($itemUrl, $comment, $externalDeliveryId);
            $configId = $this->getItemConfigurationId($itemId, $configurations);

            OTAPILib2::AddItemToBasket($lang, $sid, $itemId, $configId, $quantity, $fields, $answer);
            OTAPILib2::makeRequests();

            $this->sendAjaxResponse();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }

    private function getEditBasketItemFields($itemUrl = false, $comment = false, $externalDeliveryId = false)
    {
        $xmlParams = new SimpleXMLElement('<Fields></Fields>');

        if ($itemUrl) {
            $param = $xmlParams->addChild('FieldInfo');
            $param->addAttribute('Name', 'ItemURL');
            $param->addAttribute('Value', TextHelper::escape($itemUrl));
        }

        if ($comment || $comment === '') {
            $param = $xmlParams->addChild('FieldInfo');
            $param->addAttribute('Name', 'Comment');
            $param->addAttribute('Value', TextHelper::escape($comment));
        }

        if ($externalDeliveryId !== false) {
            $param = $xmlParams->addChild('FieldInfo');
            $param->addAttribute('Name', 'ExternalDeliveryId');
            $param->addAttribute('Value', $externalDeliveryId ? TextHelper::escape($externalDeliveryId) : '');
        }

        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }

    private function getItemConfigurationId($itemId, $configurations)
    {
        $lang = Session::getActiveLang();
        $sid = User::getObject()->getSid();

        OTAPILib2::BatchGetItemFullInfo($lang, $sid, $itemId, '', $answer);
        OTAPIlib2::makeRequests();

        $configurationId = '';
        foreach ($answer->GetResult()->GetItem()->GetConfiguredItems()->GetOtapiConfiguredItem() as $configuredItem) {
            $isCurrent = true;
            foreach ($configuredItem->GetConfigurators()->GetValuedConfigurator() as $configurator) {
                $pid = $configurator->GetPidAttribute();
                $vid = $configurator->GetVidAttribute();

                if ($configurations[$pid] !== $vid) {
                    $isCurrent = false;
                    break;
                }
            }

            if ($isCurrent === true) {
                $configurationId = $configuredItem->GetId();
                break;
            }
        }

        return $configurationId;
    }

    public function changeQuantityAction()
    {
        try {
            $sid = User::getObject()->getSid();
            $lineId = $this->request->getValue('lineId');
            $quantity = $this->request->getValue('quantity');

            OTAPILib2::EditBasketItemQuantity($sid, $lineId, $quantity, $answer);
            OTAPILib2::makeRequests();

            $this->sendAjaxResponse();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }

    public function getBasketDeliveriesAction()
    {
        try {
            $lang = Session::getActiveLang();
            $user = User::getObject();
            $sid = $user->getSid();
            $currency = $user->getCurrencyCode();
            $basketLines = $this->request->getValue('basketLines');


            OTAPILib2::GetDeliveryModesByBasket($lang, $sid, $basketLines, $answer);
            OTAPILib2::makeRequests();

            $deliveryModes = array();
            foreach ($answer->GetResult()->GetRawData()->Content->Item as $deliveryMode) {
                $tmpDeliveryMode = array(
                    'Id' => (string) $deliveryMode->Id,
                    'Name' => (string) $deliveryMode->Name,
                    'Description' => (string) $deliveryMode->Description,
                );

                if ($deliveryMode->ErrorCode) {
                    $tmpDeliveryMode['ErrorCode'] = (string) $deliveryMode->ErrorCode;
                    $tmpDeliveryMode['ErrorDescription'] = (string) $deliveryMode->ErrorDescription;
                } else {
                    foreach ($deliveryMode->FullPrice->ConvertedPriceList->DisplayedMoneys->Money as $money) {
                        if ((string) $money->attributes()->Code === $currency) {
                            $tmpDeliveryMode['Cost']['Code'] = (string) $money->attributes()->Code;
                            $tmpDeliveryMode['Cost']['Sign'] = (string) $money->attributes()->Sign;
                            $tmpDeliveryMode['Cost']['Val'] = (float) $money;
                            break;
                        }
                    }
                }

                $deliveryModes[(string) $deliveryMode->Id] = $tmpDeliveryMode;
            }

            $this->sendAjaxResponse(array('deliveryModes' => $deliveryModes));
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }

    public function runCheckingAction()
    {
        try {
            $lang = Session::getActiveLang();
            $sid = User::getObject()->getSid();
            $basketLines = $this->request->getValue('basketLines');
            $basketLinesStr = implode(',', $basketLines);

            OTAPILib2::RunBasketChecking($lang, $sid, $basketLinesStr, $answer);
            OTAPILib2::makeRequests();

            $this->sendAjaxResponse(array(
                'activityId' => $answer->GetResult()->GetId()->asString()
            ));
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }

    public function checkingAction()
    {
        try {
            $lang = Session::getActiveLang();
            $sid = User::getObject()->getSid();
            $activityId = $this->request->getValue('activityId');

            OTAPILib2::GetBasketCheckingResult($lang, $sid, $activityId, $answer);
            OTAPILib2::makeRequests();

            $data = array();
            $data['finished'] = $answer->GetResult()->IsFinished();
            $data['progressPercent'] = $answer->GetResult()->GetProgressPercent();
            $data['messages'] = array();
            foreach ($answer->GetResult()->GetMessages()->GetMessage() as $message) {
                $tmpMessage = array();
                $tmpMessage['lineId'] = $message->GetElementId()->asString();
                $tmpMessage['status'] = $message->GetStatus();
                $tmpMessage['text'] = $message->GetText();

                $data['messages'][] = $tmpMessage;
            }

            $this->sendAjaxResponse($data);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }
}