<?php

OTBase::import('system.lib.Assets');
OTBase::import('system.lib.Cache');
OTBase::import('system.lib.Cookie');
OTBase::import('system.lib.Users');
OTBase::import('system.lib.service.OrderItem.Picture');
OTBase::import('system.lib.service.PackageRecord');
OTBase::import('system.lib.service.ServiceRecord');
OTBase::import('system.lib.service.UserRecord');
OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');
OTBase::import('system.lib.CDEK.*');
OTBase::import('system.uploader.php.UploadHandler');
OTBase::import('system.admin.lib.otapi_providers.UsersProvider');
OTBase::import('system.lib.helpers.IDN');

class Orders extends GeneralUtil
{
    const ITEMS_PER_PAGE = 25;

    const STATUS_WAITING_SURCHARGE = 11;

    const ITEM_RETURNED_TO_SUPPLIER = 11;
    const ITEM_STATUS_IMPOSSIBLE_TO_PUT = 12;
    const ITEM_STATUS_CANCELLED = 13;

    const ACTIVE_TAB_COOKIE = 'active_ajax_tab__orders_list';

    protected $validActiveTabs = array(
        'orders',
        'items',
    );

    protected $multiCurlActions = array(
        'list',
        'view',
        'package',
        'changeItemsStatus'
    );

    protected $defaulAction = 'list';

    // TODO: расшифровать эти непонятные цифры
    private $defaultFilterStatuses = array(20=>20, 30=>30, 31=>31, 32=>32, 36=>36, 37=>37, 38=>38, 39=>39);

    /**
     * @var OrdersProvider
     */
    protected $ordersProvider;

    /**
     * @var usersProvider
     */
    protected $usersProvider;

    public function __construct()
    {
        parent::__construct();

        $this->ordersProvider = new OrdersProvider($this->getOtapilib());
        $this->usersProvider = new UsersProvider($this->getOtapilib());
        $this->supportRepository = new SupportRepository($this->cms);
    }

    public function listAction($request)
    {
        $sid = Session::get('sid');

        $page = $this->getPageDisplayParams($request, self::ITEMS_PER_PAGE);

        $activeTab = Cookie::get(self::ACTIVE_TAB_COOKIE, 'orders');
        if (! in_array($activeTab, $this->validActiveTabs)) {
            $activeTab = 'orders';
        }

        $sort = $request->getValue('sort', array());

        if ($request->valueExists('resetFilters')) {
            $cacher = new Cache('getFilters');
            $cacher->drop();
            $this->redirect($this->getPageUrl()->generate(array('cmd' => 'orders', 'do' => 'list')));
        }
        $filter = $this->getFilters($request);
        $applyFilters = (bool) ($request->getValue('applyFilters') || ! empty($filter));
        $uids = $this->getUsersIdsByFilters($filter);
        if (! isset($filter['orders_status'])) {
            $filter['orders_status'] = $this->defaultFilterStatuses;
        }

        $orders = array(
            'Content' => array(),
            'TotalCount' => 0,
        );
        $items = array(
            'Content' => array(),
            'TotalCount' => 0,
        );
        $languages = array();
        try {
            $xmlOrders = $this->ordersProvider->generateSearchParams($filter, $sort, implode(';', $uids));

            if (! $this->inMulti && $request->getValue('redirectToOrder') && !empty($filter['number'])) {
                if (empty($orders)) {
                    $orders = $this->ordersProvider->SearchOrders($sid, $xmlOrders, $page['offset'], $page['limit']);
                }
                if ($orders && ! empty($orders['Content'])) {
                    $foundOrder = reset($orders['Content']);
                    if ($foundOrder->getNumericId() == OrdersProxy::getOrderNumericId($filter['number'])) {
                        $this->redirect($this->getPageUrl()->generate(array(
                            'cmd' => 'orders',
                            'do' => 'view',
                            'id' => $foundOrder->id,
                        )));
                    }
                }
            }

            $ordersStatusList = $this->ordersProvider->GetOrderStatusList($sid);
            $itemsStatusList = $this->ordersProvider->getItemsStatusList($sid);
            $languages = $this->languagesProvider->GetActiveLanguages();

            if ($activeTab == 'items') {
                $xmlItems = $this->ordersProvider->generateItemsSearchParams($filter, $sort, implode(';', $uids));
                $items = $this->ordersProvider->SearchOrderLines($sid, $xmlItems, $page['offset'], $page['limit'], '', $this->getPageUrl());
            } else {
                $orders = $this->ordersProvider->SearchOrders($sid, $xmlOrders, $page['offset'], $page['limit']);
            }

            if ($this->inMulti) {
                return;
            } else if (OTBase::isMultiCurlEnabled()) {
                $this->stopMulti();
            }

            if ($activeTab == 'items') {
                $this->tpl->assign('items', ! empty($items['Content']) ? $items['Content'] : array());
                $this->tpl->assign('paginator', new Paginator($items['TotalCount'], $page['number'], $page['limit']));
            } else {
                $ordersPrepared = ! empty($orders['Content']) ? Permission::filter_orders($orders['Content']) : array();
                $this->tpl->assign('orders', $this->checkLinesStatuses($ordersPrepared));
                $this->tpl->assign('paginator', new Paginator($orders['TotalCount'], $page['number'], $page['limit']));
            }

            $categoriesProvider = new CategoriesProvider($this->cms, $this->getOtapilib());
            $providersList = $categoriesProvider->GetProviderInfoList();
            $providers = array();

            foreach ($providersList as $provider) {
                $providers[$provider['type']] = $provider;
            }

            $this->tpl->assign('providers', $providers);

            // delivery list
            $deliveries = array();
            if (RightsManager::hasRight(RightsManager::RIGHT_VIEWDELIVERYCALCULATOR)) {
                $shipmentProvider = new ShipmentProvider($this->getOtapilib());
                $deliveries = $shipmentProvider->GetDelivery();
            }
            $this->tpl->assign('deliveries', $deliveries);

            $this->tpl->assign('ordersStatusList', $ordersStatusList);
            $this->tpl->assign('itemsStatusList', $itemsStatusList);

        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }

        $itemsStatuses = array(
            'ITEM_STATUS_CANCELLED' => self::ITEM_STATUS_CANCELLED,
        );

        $this->tpl->assign('itemsStatuses', $itemsStatuses);
        $this->tpl->assign('applyFilters', $applyFilters);
        $this->tpl->assign('sorting', $sort);
        $this->tpl->assign('perpage', $page['limit']);
        $this->tpl->assign('pageParams', $page);
        $this->tpl->assign('filter', $filter);
        $this->tpl->assign('languages', $languages);
        $this->tpl->assign('activeTab', $activeTab);
        $this->tpl->assign('periodFilters', $this->getPeriodFilters());

        print $this->fetchTemplate();
    }

    private function checkLinesStatuses ($orders)
    {
        $result = array();

        foreach ($orders as $order) {

            $order['CanExportOrder'] = true;


            $lineStatuses = array(
                'ids'   => array(),
                'count' => 0
            );

            foreach ($order['LineStatusSummaries'] as $line) {
                $lineStatuses['ids'][] = (int) $line['Status']['Id'];
                $lineStatuses['ids_count'][(int) $line['Status']['Id']] = (int) $line['Count'];
                $lineStatuses['count'] += (int) $line['Count'];
            }

            if (in_array(self::ITEM_STATUS_CANCELLED, $lineStatuses['ids'])) {
                if ($lineStatuses['ids_count'][self::ITEM_STATUS_CANCELLED] == $lineStatuses['count']) {
                    $order['CanExportOrder'] = false;
                }

                if (in_array(self::ITEM_STATUS_IMPOSSIBLE_TO_PUT, $lineStatuses['ids'])) {
                    $countOf2Statuses = $lineStatuses['ids_count'][self::ITEM_STATUS_IMPOSSIBLE_TO_PUT] + $lineStatuses['ids_count'][self::ITEM_STATUS_CANCELLED];
                    if ($countOf2Statuses == $lineStatuses['count']) {
                        $order['CanExportOrder'] = false;
                    }
                }
            }

            if (in_array(self::ITEM_STATUS_IMPOSSIBLE_TO_PUT, $lineStatuses['ids'])) {
                if ($lineStatuses['ids_count'][self::ITEM_STATUS_IMPOSSIBLE_TO_PUT] == $lineStatuses['count']) {
                    $order['CanExportOrder'] = false;
                }
            }

            $result[] = $order;
        }

        return $result;
    }


    public function searchOrdersAction($request)
    {
        $sid = Session::get('sid');
        $page = $this->getPageDisplayParams($request, self::ITEMS_PER_PAGE);

        $filter = $this->getFilters($request);
        $applyFilters = (bool) ($request->getValue('applyFilters') || ! empty($filter));
        $uids = $this->getUsersIdsByFilters($filter);
        if (! isset($filter['orders_status'])) {
            $filter['orders_status'] = $this->defaultFilterStatuses;
        }

        $result = array();
        try {
            $xmlOrders = $this->ordersProvider->generateSearchParams($filter, array(), implode(';', $uids));
            $orders = $this->ordersProvider->SearchOrders($sid, $xmlOrders, $page['offset'], $page['limit']);
            $paginator = new Paginator($orders['TotalCount'], $page['number'], $page['limit']);
            $paginator->setUrlParams(array('do' => 'list'));
            $ordersStatusList = $this->ordersProvider->GetOrderStatusList($sid);
            $this->tpl->assign('ordersStatusList', $ordersStatusList);
            $this->tpl->assign('orders', $orders['Content']);
            $this->tpl->assign('activeTab', 'orders');
            $this->tpl->assign('ajax', true);

            $categoriesProvider = new CategoriesProvider($this->cms, $this->getOtapilib());
            $providersList = $categoriesProvider->GetProviderInfoList();
            $providers = array();

            foreach ($providersList as $provider) {
                $providers[$provider['type']] = $provider;
            }

            $this->tpl->assign('providers', $providers);
            $result['providers'] = $providers;

            $this->_template = 'filter-orders-results';
            $result['html'] = $this->fetchTemplateWithoutHeaderAndFooter();
            $result['pagination'] = $paginator->display(false);
            $result['orders'] = $orders['Content'];
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse($result);
    }

    public function searchOrdersLinesAction($request)
    {
        $sid = Session::get('sid');
        $page = $this->getPageDisplayParams($request, self::ITEMS_PER_PAGE);

        $filter = $this->getFilters($request);
        $applyFilters = (bool) ($request->getValue('applyFilters') || ! empty($filter));
        $uids = $this->getUsersIdsByFilters($filter);
        if (! isset($filter['orders_status'])) {
            $filter['orders_status'] = $this->defaultFilterStatuses;
        }

        $result = array();
        try {
            $xmlItems = $this->ordersProvider->generateItemsSearchParams($filter, array(), implode(';', $uids));
            $items = $this->ordersProvider->SearchOrderLines($sid, $xmlItems, $page['offset'], $page['limit'], '', $this->getPageUrl());
            $paginator = new Paginator($items['TotalCount'], $page['number'], $page['limit']);
            $paginator->setUrlParams(array('do' => 'list'));
            $itemsStatusList = $this->ordersProvider->getItemsStatusList($sid);
            $this->tpl->assign('itemsStatusList', $itemsStatusList);
            $this->tpl->assign('items', $items['Content']);
            $this->tpl->assign('activeTab', 'items');
            $this->tpl->assign('ajax', true);
            $itemsStatuses = array(
                'ITEM_STATUS_CANCELLED' => self::ITEM_STATUS_CANCELLED,
            );
            $this->tpl->assign('itemsStatuses', $itemsStatuses);

            $categoriesProvider = new CategoriesProvider($this->cms, $this->getOtapilib());
            $providersList = $categoriesProvider->GetProviderInfoList();
            $providers = array();

            foreach ($providersList as $provider) {
                $providers[$provider['type']] = $provider;
            }

            $this->tpl->assign('providers', $providers);
            $result['providers'] = $providers;

            $this->_template = 'filter-goods-results';
            $result['html'] = $this->fetchTemplateWithoutHeaderAndFooter();
            $result['pagination'] = $paginator->display(false);
            $result['items'] = $items['Content'];
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse($result);
    }


    public function getOrderItemsAction($request)
    {
        $sid = Session::get('sid');
        $id = OrdersProxy::originOrderId($request->getValue('id'));
        $result = array();
        try {
            $order = $this->ordersProvider->getOrderInfo($sid, $id);
            if (empty($order)) {
                throw new ServiceException(__METHOD__, '', 'Could not find order #' . $id, 1);
            }
            $items = array();
            foreach ($order->items as $item) {
                $item['itemurl'] = UrlGenerator::generateItemUrl($item['itemid'], array('ConfigId' => $item['configid']));
                $items[] = $item;
            }
            $result['items'] = $items;
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse($result);
    }

    public function viewAction($request)
    {
        if ($request->post('itemsGroupBy')) {
            Cookie::set('itemsGroupBy', $request->post('itemsGroupBy'));
            $this->sendAjaxResponse();
        }
        $groupBy = Cookie::get('itemsGroupBy', 'none');

        $sid = Session::get('sid');
        $id = OrdersProxy::originOrderId($request->getValue('id'));
        $normalizeOrderId = OrdersProxy::normalizeOrderId($id);
        $additionalAddresses = array();
        try {
            $order = false;
            if ($this->inMulti) {
                $this->stopMulti();
                $order = $this->ordersProvider->getOrderInfo($sid, $id);
                $this->startMulti();
            } else {
                $order = $this->ordersProvider->getOrderInfo($sid, $id);
            }
            if (empty($order)) {
                throw new ServiceException(__METHOD__, '', 'Could not find order #' . $id, 1);
            }

            $user = array();
            if (! empty($order['custid'])) {
                // Информацию о пользователе получаем в отдельном try {} catch,
                // потому что по каким-то причинам у заказа может отсутствовать заказчик О_о
                if (RightsManager::hasRight(RightsManager::RIGHT_VIEWUSERS)) {
                    try {
                        $userAccount = $this->getOtapilib()->GetAccountInfoForOperator($sid, $order['custid']);
                        $user = $this->getOtapilib()->GetUserInfoForOperator($sid, $order['custid']);
                        if (RightsManager::hasRight(RightsManager::RIGHT_VIEWUSERPROFILES)) {
                            $profiles = $this->usersProvider->GetUserProfileInfoListForOperator($sid, $order['custid']);
                        }
                        $user['account'] = $userAccount;
                        $user['orders'] = $this->ordersProvider->getOrdersByUserId($sid, $order['custid'], $order['id']);
                        $additionalAddresses = array();
                        if ($profiles && count($profiles) > 0) {
                            foreach ($profiles as $key => $profile) {
                                $additionalAddresses[] = $profile;
                            }
                        }

                    } catch (ServiceException $e) {}
                }
            }
            $user = new UserRecord($user);

            $itemsStatusList = $this->ordersProvider->getItemsStatusList($sid);

            $order['packages']              = $this->ordersProvider->getOrderPackages($sid, $id, $order->items);
            $order['readyToPurchaseItems']  = $this->ordersProvider->getReadyToPurchaseOrderItems($sid, $id, $order->items);
            $order['purchasedItems']        = $this->ordersProvider->getPurchasedOrderItems($sid, $id, $order->items);
            $order['processlog']            = new ServiceRecord($this->getOtapilib()->GetSalesProcessLog($sid, $id));

            $usedStatusList = array();
            $filteredSalesLinesList = array();

            if ($this->inMulti) {
                return;
            } else if (OTBase::isMultiCurlEnabled()) {
                $this->stopMulti();
            }

            // Переписка с пользователем
            $order['ticketMessages'] = $this->getTicketMessages($order, $user);

            $itemsPrepared = array();

            foreach ($order->items as $item) {
                if (ProductsHelper::isWarehouseProduct($item)) {
                    $url = $this->getPageUrl()->getWarehouseProductUrl($item);
                    $item->ItemExternalURL = $url;
                    $item->itemexternalurl = $url;
                }
                $itemsPrepared[] = $item;
            }

            $order->items = $itemsPrepared;

            $disabledStatuses = array(
                self::ITEM_RETURNED_TO_SUPPLIER,
                self::ITEM_STATUS_IMPOSSIBLE_TO_PUT,
                self::ITEM_STATUS_CANCELLED,

            );
            $itemsGroupByList = array(
                'none' => LangAdmin::get('group_by_none'),
                'vendid' => LangAdmin::get('group_by_vendor'),
                'statusid' => LangAdmin::get('group_by_status'),
            );

            $groupTitles = array();
            if ($groupBy != 'none') {
                $itemsGrouped = array();
                foreach ($itemsPrepared as $item) {
                    $itemsGrouped[$item[$groupBy]][] = $item;
                    if (in_array($item->statusid, $disabledStatuses)) {
                        $total = 0;
                    } else {
                        $total = (float)$item->amountcust;
                    }
                    if (isset($groupTitles[$item[$groupBy]])) {
                        $groupTitles[$item[$groupBy]]['total'] += $total;
                    } else {
                        $groupTitles[$item[$groupBy]]['total'] = $total;
                        $groupTitles[$item[$groupBy]]['name'] = ($groupBy == 'vendid') ? (LangAdmin::get('Vendor') . ': ' . $item->vendnick) : $item->statusname;
                    }
                }
                if ($groupBy == 'statusid') {
                    ksort($itemsGrouped);
                }
                $order->itemsGrouped = $itemsGrouped;
            }

            $packageConfirmingButtons = array();
            // при наличии посылок запрашиваем список служб доставки
            if (!empty($order->packages)) {
                /** @var ArrayOfOtapiDeliveryServiceSystemInfo $systems */
                $systems = InstanceProvider::getObject()->GetDeliveryServiceSystemInfoList(Session::getActiveAdminLang());
                foreach ($systems->GetItem() as $system) {
                    if ($system->IsAvailable() && $system->NeedPackagesConfirming()) {
                        $packageConfirmingButtons[$system->GetIntegrationType()] = $system->GetPackagesConfirmingActionName();
                    }
                }
            }

            $deliveryModes = array();

            try {
                $packages = isset($order->packages) ? $order->packages : array();
                if (empty($packages) && RightsManager::hasRight(RightsManager::RIGHT_EDITORDER)) {
                    $deliveries = $this->ordersProvider->SearchDeliveryModes($order->deliveryaddress->countrycode, (float)$order->weight, $order->deliveryaddress->citycode, $order->providertypeenum);

                    foreach ($deliveries['Content'] as $mode) {
                        $deliveryModes[$mode['Id']] = $mode['Name'];
                    }
                } elseif (count($packages) === 1 && RightsManager::hasRight(RightsManager::RIGHT_EDITPACKAGE)) {
                    $package = $packages[0];
                    $deliveries = $this->ordersProvider->SearchDeliveryModes($package->deliverycountrycode, (float)$package->weight, $package->deliverycitycode, $order->providertypeenum);

                    foreach ($deliveries['Content'] as $mode) {
                        $deliveryModes[$mode['Id']] = $mode['Name'];
                    }
                }
            } catch (ServiceException $e) {
                ErrorHandler::registerError($e);;
            }

            $canUpdateDeliveryMode = empty($deliveryModes) ? false : true;

            $this->tpl->assign('groupBy', $groupBy);
            $this->tpl->assign('itemsGroupByList', $itemsGroupByList);
            $this->tpl->assign('groupTitles', $groupTitles);
            $this->tpl->assign('disabledStatuses', $disabledStatuses);
            $da = $order->deliveryaddress;
            $this->tpl->assign('order', $order);
            $this->tpl->assign('user', $user);
            $this->tpl->assign('itemsStatusList', $itemsStatusList);
            $this->tpl->assign('additionalAddresses', $additionalAddresses);
            $this->tpl->assign('packageConfirmingButtons', $packageConfirmingButtons);
            $this->tpl->assign('deliveryModes', $deliveryModes);
            $this->tpl->assign('canUpdateDeliveryMode', $canUpdateDeliveryMode);
            $this->tpl->assign('pageUrl', $this->pageUrl);
        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);

            if (! $order) {
                Session::setError(LangAdmin::get('merge_error_wrong_order') . ' - ' . $normalizeOrderId);
                $this->redirect($this->getPageUrl()->generate(array('cmd' => 'orders', 'do' => 'list')));
            }
        }

        print $this->fetchTemplate();
    }

    public function getPackageTrackingAction($request)
    {
        $packageId = $request->getValue('packageId');
        $lang = Session::getActiveAdminLang();
        $sid = Session::get('sid');

        try {
            OTAPILib2::GetPackageTrackingForOperator($lang, $sid, $packageId, $answer);
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
            $this->respondAjaxError($e->getMessage());
        }
    }

    public function runExportedPackagesConfirmingAction($request)
    {
        $activityId = '';
        $activityType = '';
        try {
            $integrationType = $request->get('integrationType');
            $lang =  Session::getActiveAdminLang();
            $sessionId = Session::get('sid');

            $answer = array();
            OTAPILib2::RunExportedPackagesConfirming($lang, $sessionId, $integrationType, $answer);
            OTAPILib2::makeRequests();

            if (! $answer) {
                throw new Exception('Service reply is wrong');
            }

            $activityId = $answer->getResult()->GetId()->asString();
            $activityType = $answer->getResult()->GetType();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse(array('result' => 'ok', 'activityId' => $activityId, 'activityType' => $activityType), true);
    }

    public function printdeclarationAction($request)
    {
        $sid = Session::get('sid');
        $id = OrdersProxy::originOrderId($request->getValue('id'));
        $pid = $request->getValue('pid');
        try {
            if ($this->inMulti) {
                $this->stopMulti();
                $order = $this->ordersProvider->getOrderInfo($sid, $id);
                $package = $this->getOtapilib()->GetPackage($sid, $pid);
                $this->startMulti();
            } else {
                $order = $this->ordersProvider->getOrderInfo($sid, $id);
                $package = $this->getOtapilib()->GetPackage($sid, $pid);
            }

            $this->_template = 'declaration';
            $op = explode('/', @$order->OperatorName);
            $sender = trim(@$op[0]);
            if (empty($sender)) $sender = $_SERVER['SERVER_NAME'];
            $this->tpl->assign('sender', IDN::decodeIDN($sender));
            $this->tpl->assign('logist_order_id', trim(@$op[1]));
            $this->tpl->assign('order_id', $order->Id);
            $this->tpl->assign('receiver',
                $package['DeliveryContactLastname'].
                ' '.$package['DeliveryContactFirstname'].' '.
                $package['DeliveryContactMiddlename']);
            $address = array();
            if($package['DeliveryCountry']) {
                $address[] = $package['DeliveryCountry'];
            }
            if($package['DeliveryPostalCode']) {
                $address[] = $package['DeliveryPostalCode'];
            }
            if($package['DeliveryRegionName']) {
                $address[] = $package['DeliveryRegionName'];
            }
            if($package['DeliveryCity']) {
                $address[] = $package['DeliveryCity'];
            }
            if($package['DeliveryAddress']) {
                $address[] = $package['DeliveryAddress'];
            }
            $this->tpl->assign('address', implode(', ', $address));
            $this->tpl->assign('phone', $order->DeliveryAddress->Phone);
            $this->tpl->assign('DeliveryModeName', $package['DeliveryModeName']);
            $this->tpl->assign('container', $this->ordersProvider->getOrderContainer($id));
            $count = 0;
            $sum = 0;
            $currency = '';
            $packageIds = array();
            foreach ($package['Items'] as $ii) {
                $count += $ii['quantity'];
                $sum += $ii['totalpriceinternal'];
                $packageIds[] = $ii['OrderLineId'];
            }

            foreach ($order->items as $ii) {
                $currency = $ii['InternalPriceCurrencyCode'];
            }

            $this->tpl->assign('lines', $order->items);
            $this->tpl->assign('packageIds', $packageIds);
            $this->tpl->assign('count', $count);
            $this->tpl->assign('package', $package);
            $this->tpl->assign('currency', $currency);
            $this->tpl->assign('sum', $sum);
        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);

            if (!$order) {
                $this->redirect($this->getPageUrl()->generate(array('cmd' => 'orders', 'do' => 'list')));
            }
        }

        print $this->fetchTemplateWithoutHeaderAndFooter();
    }

    public function getOrderItemCommentsAction($request)
    {
        $repo = new ItemInfoRepository($this->cms);
        $comments = $repo->getItemComments($request->getValue('itemid'), $request->getValue('categoryid'));
        $comments = is_array($comments) ? array_slice($comments, 0, 2) : array();
        $this->sendAjaxResponse(array(
            'comments' => $comments,
        ));
    }

    public function deleteItemFromOrderAction($request)
    {
        $orderid = $request->getValue('orderid');
        $itemId = $request->getValue('itemid');

        $request->set('itemId', array($itemId));
        $request->set('orderId', $orderid);
        $request->set('status', 13);

        $this->changeItemStatusAction($request);
    }

    public function getItemConfigAction($request)
    {
        $sid = Session::get('sid');
        $itemId = $request->getValue('itemId');
        $configs = array();
        try {
            $result = $this->getOtapilib()->GetItemFullInfoWithPromotions($itemId);
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', 'Could not get item full info', 1);
            }
            $result = $this->checkHierarchicalConfigurators($result);
            if (! empty($result['configurations'])) {
                $configs['HasHierarchicalConfigurators'] = isset($result['HasHierarchicalConfigurators']) ? $result['HasHierarchicalConfigurators'] : 'false';
                $configs['configurations'] = $result['configurations'];
            }
            if (! empty($result['item_with_config'])) {
                $configs['item_with_config'] = $result['item_with_config'];
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse($configs);
    }

    public function setItemConfigAction($request)
    {
        $sid = Session::get('sid');
        $itemId = $request->getValue('itemId');
        $orderId = $request->getValue('orderId');
        $configId = $request->getValue('configId');
        try {
            $this->ordersProvider->changeOrderItemConfig($sid, $orderId, $itemId, $configId);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function changeItemPriceAction($request)
    {
        $sid = Session::get('sid');
        list($itemId, $orderId) = explode('_', $request->getValue('pk'));
        $newPrice = (string)$request->getValue('value');
        $newPrice = str_replace(',', '.', $newPrice);
        if (empty($newPrice)) {
            $this->respondAjaxError(LangAdmin::get('Value_must_not_be_empty'));
        }
        $response = array();
        try {
            $result = $this->ordersProvider->changeOrderItemPrice($sid, $orderId, $itemId, $newPrice);
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', 'Could not change item price', 1);
            }
            $order = $this->ordersProvider->getOrderInfo($sid, $orderId);
            $userInfo = false;
            if (RightsManager::hasRight(RightsManager::RIGHT_VIEWUSERS)) {
                $userInfo = $this->getOtapilib()->GetUserInfoForOperator($sid, $order->custid);
            }
            $statusInfo = array();
            foreach ($order->items as $item) {
                if ($item->id == $itemId) {
                    $itemId = $item->itemtaobaoid;
                    $statusInfo = array (
                        'name' => $item->statusname,
                        'code' => $item->statuscode,
                        'id'   => $item->statusid);
                    break;
                }
            }
            if ($userInfo) {
                $userData = new UserData();
                $userData->ClearAccountInfoCacheByLogin($userInfo['Login']);
            }
            $response = array(
                'status' => $statusInfo['name'],
                'statusid' => $statusInfo['id'],
                'statuscode' => $statusInfo['code'],
                'amountcust' => (float)$item->newpricecust * (int)$item->qty,
                'itemid' => $request->getValue('pk'),
                'order' => $order,
                'itemsByStatus' => $order->getItemsGrouppedByStatus(),
            );
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse($response);
    }

    public function changeItemWeightAction($request)
    {
        $sid = Session::get('sid');
        list($itemId, $orderId) = explode('_', $request->getValue('pk'));
        $newItemWeight = (string)$request->getValue('value');
        $newItemWeight = str_replace(',', '.', $newItemWeight);
        if (empty($newItemWeight)) {
            $this->respondAjaxError(LangAdmin::get('Value_must_not_be_empty'));
        }
        $response = array();
        try {
            $result = $this->ordersProvider->changeOrderItemWeight($sid, $orderId, $itemId, $newItemWeight);
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', 'Could not change item weight', 1);
            }
            $order = $this->ordersProvider->getOrderInfo($sid, $orderId);
            $orderWeight = $order['Weight'];
            $deliveryAccess = $this->checkDeliveryAccessForOrder($order, $orderWeight);
            $statusInfo = array();
            foreach ($order->items as $item) {
                if ($item->id == $itemId) {
                    $statusInfo = array (
                        'name' => $item->statusname,
                        'code' => $item->statuscode,
                        'id'   => $item->statusid);
                    break;
                }
            }
            $response = array(
                'status' => $statusInfo['name'],
                'statusid' => $statusInfo['id'],
                'statuscode' => $statusInfo['code'],
                'itemid' => $request->getValue('pk'),
                'order' => $order,
                'itemsByStatus' => $order->getItemsGrouppedByStatus(),
                'deliveryAccess' => $deliveryAccess
            );
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse($response);
    }

    public function splitItemQuantityAction($request)
    {
        $sid = Session::get('sid');
        $orderId = $request->getValue('orderId');
        $itemId = $request->getValue('itemId');
        $splitQuantity = $request->getValue('splitQuantity');
        try {
            $result = $this->ordersProvider->splitOrderItemQuantity($sid, $orderId, $itemId, $splitQuantity);
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', 'Could not split item quantity', 1);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function changeOrderWeightAction($request)
    {
        $sid = Session::get('sid');
        $orderId = $request->getValue('pk');
        $weight  = $request->getValue('value');
        $deliveryprice = 0;
        $deliveryName = null;
        if (empty($weight)) {
            $this->respondAjaxError(LangAdmin::get('Value_must_not_be_empty'));
        }
        try {
            $xmlUpdateData = '<OrderUpdateData><Weight>' . str_replace(',', '.', $weight) . '</Weight></OrderUpdateData>';
            $result = $this->getOtapilib()->UpdateOrderForOperator($sid, $orderId, $xmlUpdateData);
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', 'Could not change order weight', 1);
            }
            $order = $this->ordersProvider->getOrderInfo($sid, $orderId);
            $deliveryprice = TextHelper::formatPrice($order->deliveryamount, $order->currencysign);
            $deliveryAccess = $this->checkDeliveryAccessForOrder($order, $weight);

        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('orderDeliveryPrice' => $deliveryprice, 'deliveryAccess' => $deliveryAccess ));
    }

    private function checkDeliveryAccessForOrder($order, $weight) {
        $deliveryModes  = $this->ordersProvider->SearchDeliveryModes($order['DeliveryAddress']['CountryCode'], $weight, $order['DeliveryAddress']['CityCode'], $order['ProviderTypeEnum']);
        $deliveryAccess = false;
        $deliveryName = null;

        if ($deliveryModes['TotalCount'] !== "0") {
            foreach ($deliveryModes as $deliveryMode) {
                if (isset($deliveryMode[0]['Name'])) {
                    $deliveryName = $deliveryMode[0]['Name'];
                } elseif (isset($deliveryMode['Name'])) {
                    $deliveryName = $deliveryMode['Name'];
                }
                if ($deliveryName !== null) {
                    if ($deliveryName === $order->deliverymodename) {
                        $deliveryAccess = true;
                        break;
                    }
                }
            }
        }
        return $deliveryAccess;
    }

    public function updateDeliveryModeAction($request)
    {
        $sid = Session::get('sid');
        $orderId = $request->getValue('pk');
        $deliveryId  = $request->getValue('value');

        try {
            $order = $this->ordersProvider->getOrderInfo($sid, $orderId);
            if (empty($order)) {
                throw new ServiceException(__METHOD__, '', 'Could not find order #' . $orderId, 1);
            }

            $packages = $this->ordersProvider->getOrderPackages($sid, $orderId, $order->items);

            if (empty($packages)) {
                $weight = $order->weight;
                $xmlUpdateData  = '<OrderUpdateData>';
                $xmlUpdateData .= '<DeliveryModeId>' . $deliveryId . '</DeliveryModeId>';
                $xmlUpdateData .= '<Weight>' . str_replace(',', '.', $weight) . '</Weight>';
                $xmlUpdateData .= '</OrderUpdateData>';

                $result = $this->getOtapilib()->UpdateOrderForOperator($sid, $orderId, $xmlUpdateData);

                if (empty($result)) {
                    throw new ServiceException(__METHOD__, '', 'Could not change order weight', 1);
                }
            } elseif (count($packages) === 1) {
                $weight = $packages[0]->weight;
                $xmlUpdateData  = '<PackageAdminUpdateInfo>';
                $xmlUpdateData .= '<DeliveryModeId>' . $deliveryId . '</DeliveryModeId>';
                $xmlUpdateData .= '<Weight>' . str_replace(',', '.', $weight) . '</Weight>';
                $xmlUpdateData .= '</PackageAdminUpdateInfo>';

                $packageId = $packages[0]->id;
                $result = $this->otapilib->UpdatePackage($sid, $packageId, $xmlUpdateData);

                if (empty($result)) {
                    throw new ServiceException(__METHOD__, '', 'Could not change package weight', 1);
                }
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function changeOperatorCommentAction($request)
    {
        $sid = Session::get('sid');
        $orderId = $request->getValue('orderId');
        $itemId = $request->getValue('itemId');
        $status = $request->getValue('status');
        $comment = $request->getValue('comment', '');
        $quantity = $request->getValue('quantity', '');
        $response = array();
        try {
            // TODO: костыль. Сервисы не умеют очищать комментарий. Пустой коммент будет прогнорирован.
            // Поэтому здесь, если нужен пустой коммент, отправляем пробел.
            $comment = strlen($comment) ? $comment : ' ';
            $this->getOtapilib()->ChangeLineStatus($sid, $orderId, $itemId, $status, $comment, $quantity);
            $response['comment'] = array(
                'name' => LangAdmin::get('Operator'),
                'text' => $comment,
            );
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse($response);
    }

    public function changeOrderAdditionalInfoAction($request)
    {
        $sid = Session::get('sid');
        $info = $request->getValue('info');
        $orderId = $request->getValue('orderId');
        try {
            // TODO: костыль. Сервисы не умеют очищать комментарий. Пустой коммент будет прогнорирован.
            // Поэтому здесь, если нужен пустой коммент, отправляем пробел.
            $info = strlen($info) ? $info : ' ';
            $xmlUpdateData = '<OrderUpdateData><AdditionalInfo>' .
                $this->escape($info) . '</AdditionalInfo></OrderUpdateData>';
            $result = $this->getOtapilib()->UpdateOrderForOperator($sid, $orderId, $xmlUpdateData);
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', 'Could not change additional order info', 1);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function getOrderInfoAction($request)
    {
        $sid = Session::get('sid');
        $orderId = $request->getValue('orderId');
        $response = array();
        try {
            $response['order'] = $this->ordersProvider->getOrderInfo($sid, $orderId);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse($response);
    }

    public function changeItemsStatusAction($request)
    {
        $sid = Session::get('sid');
        $orders = $request->getValue('orders');
        $status = $request->getValue('status');
        $response = array();

        try {
            if (is_array($orders)) {
                foreach ($orders as $orderId => $items) {
                    $result = $this->ordersProvider->changeOrderItemsStatus($sid, $orderId, $items, $status);
                }
            }

            if ($this->inMulti) {
                return;
            } else if (OTBase::isMultiCurlEnabled()) {
                $this->stopMulti();
            }

        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse($response);
    }

    public function changeItemStatusAction($request)
    {
        $sid = Session::get('sid');
        $orderId = $request->getValue('orderId');
        $itemId = $request->getValue('itemId');
        $status = $request->getValue('status');
        $comment = $request->getValue('comment', '');
        $quantity = $request->getValue('quantity', '');
        $response = array();
        try {
            if (is_array($itemId)) {
                $result = $this->ordersProvider->changeOrderItemsStatus($sid, $orderId, $itemId, $status);
            } else {
                $result = $this->getOtapilib()->ChangeLineStatus($sid, $orderId, $itemId, $status, $comment, $quantity);
            }
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', 'Could not change item status', 1);
            }
            $order = $this->ordersProvider->getOrderInfo($sid, $orderId);
            $userBalance = null;
            if (RightsManager::hasRight(RightsManager::RIGHT_VIEWUSERS)) {
                $userAccount = $this->getOtapilib()->GetAccountInfoForOperator($sid, $order->custid);
                $userBalance = TextHelper::formatPrice($userAccount['AvailableCust'], $userAccount['CurrencySignCust']);

                $userInfo  = $this->getOtapilib()->GetUserInfoForOperator($sid, $order->custid);
                if (in_array($status, array(13,12,11))) {
                    //Очищаем кэш если у товара статус отменен, возвращен продавцу, невозможно поставить
                    $userData = new UserData();
                    $userData->ClearAccountInfoCacheByLogin($userInfo['Login']);
                }
            }
            $response = array(
                'itemsByStatus' => $order->getItemsGrouppedByStatus(),
                'orderStatusName' => $order->statusName,
                'orderStatusCode' => $order->statusCode,
                'orderPaid' => $order->getPaidAmount(),
                'orderRemain' => TextHelper::formatPrice($order->remainamount, $order->currencysign),
                'orderRemainWithoutSign' => $order->remainamount,
                'orderGoodsAmount' => TextHelper::formatPrice($order->goodsamount, $order->currencysign),
                'orderGoodsAmountWithoutSign' => $order->goodsamount,
                'userBalance' => $userBalance
            );
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse($response);
    }

    public function cancelOrderAction($request)
    {
        $sid = Session::get('sid');
        $orderId = $request->getValue('id');
        try {
            $this->ordersProvider->CancelSalesOrderForOperator($sid, $orderId);
            $order = $this->ordersProvider->getOrderInfo($sid, $orderId);
            if (RightsManager::hasRight(RightsManager::RIGHT_VIEWUSERS)) {
                $userInfo = $this->getOtapilib()->GetUserInfoForOperator($sid, $order->custid);
                $userData = new UserData();
                $userData->ClearAccountInfoCacheByLogin($userInfo['Login']);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array('order' => $order));
    }

    public function closeOrderAction($request)
    {
        $sid = Session::get('sid');
        $id = $request->getValue('id');
        try {
            $result = $this->getOtapilib()->CloseSalesOrderForOperator($sid, $id);
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', 'Could not close order', 1);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function printPackageReceiptAction($request) {
        $sid = Session::get('sid');
        $packageId = $request->getValue('packageId');
        $message = '';
        try {
            $message = CDEK::PrintPackage($packageId);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'Ok', 'message' => $message));
    }

    public function exportPackageAction($request)
    {
        $sid = Session::get('sid');
        $packageId = $request->getValue('packageId');

        $message = '';
        try {
            $message = $this->getOtapilib()->ExportPackageToDeliveryServiceSystem($sid, self::setPackageXML($packageId));
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'Ok', 'message' => $message));
    }

    private static function setPackageXML($packId)
    {
        $xmlParams = new SimpleXMLElement('<PackageExportParameters></PackageExportParameters>');
        $xmlParams->addChild('PackageId', $packId);
        return $xmlParams->asXML();
    }


    public function deletePackageAction($request)
    {
        $sid = Session::get('sid');
        $packageId = $request->getValue('packageId');
        try {
            $this->getOtapilib()->DeletePackage($sid, $packageId);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function packageAction($request)
    {
        $sid = Session::get('sid');
        $packageId = $request->getValue('packageId', 'new');
        $orderId = $request->getValue('orderId');
        $profiles = array();
        $address = array();

        try {
            if ($this->inMulti) {
                $this->stopMulti();
                $order = $this->ordersProvider->getOrderInfo($sid, $orderId);
                $this->startMulti();
            } else {
                $order = $this->ordersProvider->getOrderInfo($sid, $orderId);
            }
            if (empty($order)) {
                throw new ServiceException(__METHOD__, '', 'Could not find order #' . $orderId, 1);
            }

            if ($order['custid'] && RightsManager::hasRight(RightsManager::RIGHT_VIEWUSERS)) {
                $user = $this->getOtapilib()->GetUserInfoForOperator($sid, $order['custid']);
                if (RightsManager::hasRight(RightsManager::RIGHT_VIEWUSERPROFILES)) {
                    $profiles = $this->usersProvider->GetUserProfileInfoListForOperator($sid, $order['custid']);
                }
                if (empty($user['countrycode'])) {
                    $user['countrycode'] = 'RU';
                }
            } else {
                $user['countrycode'] = 'RU';
            }

            $itemsStatusList = $this->ordersProvider->getItemsStatusList($sid);

            $countries = InstanceProvider::getObject()->getDeliveryCountryInfoList();
            $countries = $this->otapilib->GetDeliveryCountryInfoList($countries->asXML());
            $processLog     = $this->getOtapilib()->GetSalesProcessLog($sid, $orderId);

            if ($packageId == 'new') {
                if ($order->deliveryaddress && !empty($order->deliveryaddress->address)) {
                    $address['country'] = $order->deliveryaddress->countrycode;
                    $address['regionName'] = $order->deliveryaddress->regionname;
                    $address['city'] = $order->deliveryaddress->city;
                    $address['cityCode'] = $order->deliveryaddress->cityCode;
                    $address['address'] = $order->deliveryaddress->address;
                    $address['postalCode'] = $order->deliveryaddress->postalcode;
                    $address['firstName'] = $order->deliveryaddress->name;
                    $address['lastName'] = $order->deliveryaddress->familyname;
                    $address['middleName'] = $order->deliveryaddress->patername;
                    $address['phone'] = $order->deliveryaddress->phone;
                } else {
                    $address['country'] = isset($user['countrycode']) ? $user['countrycode'] : '';
                    $address['regionName'] = '';
                    $address['city'] = isset($user['city']) ? $user['city'] : '';
                    $address['cityCode'] = isset($user['cityCode']) ? $user['cityCode'] : '';
                    $address['address'] = isset($user['address']) ? $user['address'] : '';
                    $address['postalCode'] = isset($user['postalcode']) ? $user['postalcode'] : '';
                    $address['firstName'] = isset($user['firstname']) ? $user['firstname'] : '';
                    $address['lastName'] = isset($user['lastname']) ? $user['lastname'] : '';
                    $address['middleName'] = isset($user['middlename']) ? $user['middlename'] : '';
                    $address['phone'] = isset($user['phone']) ? $user['phone'] : '';
                }

                $order->deliveryaddress;

                $actionTitle = LangAdmin::get('Creating_package');
                $packageItems = array();
                $itemsIds = $request->getValue('itemsIds', array());
                if (! empty($itemsIds)) {
                    foreach ($order->items as $item) {
                        if (in_array($item->id, $itemsIds)) {
                            $item['in_package'] = true;
                            $packageItems[] = $item;
                        }
                    }
                }
            } else {
                $actionTitle = LangAdmin::get('Editing_package') . ' № ' . $packageId;

                $package = $this->getOtapilib()->GetPackage($sid, $packageId);

                // Если редактровать статус нельзя, то не делаем запрос.
                $packageStatuses = array();
                if ($this->inMulti || ($package && $package['CanChangeStatus'] == 'true')) {
                    $packageStatuses = $this->ordersProvider->GetPackageAvailableStatusList($sid, $packageId);
                }
                // Определение товаров, которые относятся к данной посылке
                $packageItemsIds = array();
                if ($package && !empty($package['items'])) {
                    foreach ($package['items'] as $item) {
                        $packageItemsIds[] = $item['orderlineid'];
                    }
                }

                // TODO: Здесь происходит что-то непонятное.
                // Понять что и сделать это правильно.
                $packageItems = array();
                foreach ($order->items as $item) {
                    if (in_array($item['id'], $packageItemsIds)) {
                        $item['in_package'] = true;
                    }
                    if ($item['canmovetopackage'] == 'true') {
                        $packageItems[] = $item;
                    }
                }

                $this->tpl->assign('package', new ServiceRecord($package));
                $this->tpl->assign('packageStatuses', $packageStatuses);
            }

            if ($this->inMulti) {
                return;
            } else if (OTBase::isMultiCurlEnabled()) {
                $this->stopMulti();
            }
            // В мудьти нельзя вызвать так как необходим параметр из другого запроса.
            if ($packageId == 'new') {
                $deliveryModes  = $this->ordersProvider->SearchDeliveryModes($order['DeliveryAddress']['CountryCode'], 0, $order['DeliveryAddress']['CityCode'], $order['ProviderTypeEnum']);
            } else {
                $deliveryModes  = $this->ordersProvider->SearchDeliveryModes($order['DeliveryAddress']['CountryCode'], (float)$package['Weight'], $package['DeliveryCityCode'], $order['ProviderTypeEnum']);
            }
            $deliveryModes = $deliveryModes['Content'];
            $this->tpl->assign('user', $user);
            $this->tpl->assign('packageItems', $packageItems);
            $this->tpl->assign('order', $order);
            $this->tpl->assign('profiles', $profiles);
            $this->tpl->assign('countries', $countries);
            $this->tpl->assign('deliveryModes', $deliveryModes);
            $this->tpl->assign('itemsStatusList', $itemsStatusList);
            $this->tpl->assign('actionTitle', $actionTitle);
            $this->tpl->assign('address', $address);

        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }

        print $this->fetchTemplate();
    }

    /**
     * Перемещение товаров между посылками.
     *
     * - Если передан только toPackageId (fromPackageId == null) -  это добавление товаров в существующую посылку.
     *                                                              Товары при этом не находятся ни в одной посылке.
     *
     * - Если передан только fromPackageId (toPackageId == null) -  это перемещение товаров из существующей посылки
     *                                                              во вновь создаваемую посылку для перемещаемых
     *                                                              товаров.
     *
     * - Если переданы оба параметра toPackageId и fromPackageId -  это перемещение товаров из одной существующей
     *                                                              посылки в другую существующую посылку.
     *
     * - Если передан параметр toPackageId и doDelete            -  это удаление товаров из посылки.
    **/
    public function moveItemsToPackageAction($request)
    {
        $sid = Session::get('sid');
        $itemsIds = $request->getValue('itemsIds');
        $toPackageId = $request->getValue('toPackageId', null);
        $fromPackageId = $request->getValue('fromPackageId', null);
        $doDelete = $request->getValue('doDelete', null);
        $orderId = $request->getValue('orderId', null);

        if (! ($toPackageId || $fromPackageId)) {
            $this->respondAjaxError('There must be at least one package ID');
        }

        if ($toPackageId == $fromPackageId) {
            $this->respondAjaxError(LangAdmin::get('Choose_another_package_for_items'));
        }

        try {
            Plugins::invokeEvent('onUpdatePackage');

            if (! is_null($fromPackageId)) {
                // Если посылка, в которую нужно переместить itemsIds не передана,
                // значит ее надо создать.
                if (is_null($toPackageId)) {
                    $newPackageId = $this->ordersProvider->createPackage($sid, $orderId, $itemsIds);
                    if (empty($newPackageId)) {
                        $this->respondAjaxError('Could not create a new package');
                    }
                }
            }

            if (! is_null($toPackageId)) {
                $packageTo = new PackageRecord($this->getOtapilib()->GetPackage($sid, $toPackageId));
                $itemsTo = array();
                if (! is_null($doDelete)) {
                    foreach ($packageTo->items as $item) {
                        if (! in_array($item->orderLineId, $itemsIds)) {
                           $itemsTo[] = $item->orderLineId;
                        }
                    }
                } else {
                    $itemsTo = $itemsIds;
                    foreach ($packageTo->items as $item) {
                        $itemsTo[] = $item->orderLineId;
                    }
                }
                $this->ordersProvider->moveItemsToPackage($sid, array_unique($itemsTo), $toPackageId);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array(
            'message' => LangAdmin::get('Package_updated_successfully')
        ));
    }

    public function savePackageAction($request)
    {
        $sid = Session::get('sid');
        $packageId = $request->getValue('packageId', 'new');
        $orderId = $request->getValue('orderId');
        $itemsIds = $request->getValue('itemsIds');
        $PriceInternal = $request->getValue('PriceInternal');

        try {
            if ($packageId == 'new') {
                $newPackageId = $this->ordersProvider->createPackage($sid, $orderId, $itemsIds);
                if (! empty($newPackageId)) {
                    $this->ordersProvider->updatePackage($sid, $newPackageId, $request);
                    Session::setMessage(LangAdmin::get('Package_created_successfully'));
                }
                $this->redirect('index.php?cmd=orders&do=package&orderId=' . $orderId . '&packageId=' . $newPackageId);
            } else {
                Plugins::invokeEvent('onUpdatePackage');

                if ($PriceInternal < 0) {
                    throw new Exception(LangAdmin::get('Price_can_not_be_below_zero'));
                }

                $this->ordersProvider->updatePackage($sid, $packageId, $request);

                Session::setMessage(LangAdmin::get('Package_updated_successfully'));
            }
        } catch (Exception $e) {
            Session::setError($e->getMessage());
            $urlParams = array(
                'cmd' => 'orders',
                'do' => 'package',
                'orderId' => $orderId,
                'packageId' => $packageId,
            );
            if (! empty($itemsIds)) {
                $urlParams['itemsIds'] = $itemsIds;
            }
            $this->redirect('index.php?' . http_build_query($urlParams));
        }

        $this->redirect('index.php?cmd=orders&do=view&id=' . $orderId);
    }

    public function paymentReserveAction($request)
    {
        $sid = Session::get('sid');
        $orderId = $request->getValue('orderId');
        try {
            $paymentInfo = $this->getOtapilib()->GetSalesPaymentInfo($sid, $orderId);
            $order = $this->ordersProvider->getOrderInfo($sid, $orderId);
            // если средств на счету меньше чем необходимо - резервируем все срадства которые есть на счету
            $amount = min(
                (float)$paymentInfo['custbalanceavail'],
                $order->remainamount
            );
            $this->ordersProvider->SalesPaymentReserve($sid, $orderId, $amount);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function purchaseItemsAction($request)
    {
        $sid = Session::get('sid');
        $orderId = $request->getValue('orderId');
        $itemsIds = $request->getValue('itemsIds', array());
        try {
            $itemsIds = is_array($itemsIds) ? $itemsIds : array($itemsIds);
            $readyToPurchaseOrderItems = $this->ordersProvider->getReadyToPurchaseOrderItems($sid, $orderId);
            $items = array();
            foreach ($readyToPurchaseOrderItems as $item) {
                if (in_array($item->id, $itemsIds)) {
                    $items[] = $item;
                }
            }
            $result = $this->ordersProvider->purchaseOrderItems($sid, $orderId, $items);
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', 'Could not purchase items', 1);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function restoreOrderAction($request)
    {
        $sid = Session::get('sid');
        $orderId = $request->getValue('id');
        try {
            $orderInfo = $this->ordersProvider->GetSalesOrderDetailsForOperator($sid, $orderId, '', 0);

            // TODO: какой-то левый костыль. Убрать.
            //Не костыль - сервисы позволяют восстановить толкьо по товарам, целиком заказ нельзя
            //нет метода
            foreach ($orderInfo['saleslineslist'] as $item) {
                $this->getOtapilib()->RestoreLineSalesOrderForOperator($sid, $orderId, $item['id']);
                break;
            }
            $order = $this->ordersProvider->getOrderInfo($sid, $orderId);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array('order' => $order));
    }

    public function removeOrderItemImageAction($request)
    {
        $sid = Session::get('sid');
        $itemId = $request->getValue('itemId');
        $orderId = $request->getValue('orderId');
        $imageUrl = $request->getValue('imageUrl');

        $image = new Picture($imageUrl);
        $imageType = $image->getType();

        $imagesSizeArray = array(100, 300, 150, 310);

        if (! in_array($imageType, Picture::getAvailableTypes())) {
            $this->respondAjaxError('Invalid image type given.');
        }

        $response = array();
        try {
            if ($imageType === 'link') {
                $status = $request->getValue('status');
                $comment = $request->getValue('comment', '');
                $quantity = $request->getValue('quantity', '');

                if (strpos($comment, $imageUrl) !== false) {
                    $comment = str_replace($imageUrl, '', $comment);
                    $comment = trim(preg_replace("#\n+#si", "\n", $comment), "\n");
                    // TODO: костыль. Сервисы не умеют очищать комментарий. Пустой коммент будет прогнорирован.
                    // Поэтому здесь, если нужен пустой коммент, отправляем пробел.
                    $comment = strlen($comment) ? $comment : ' ';
                    $this->getOtapilib()->ChangeLineStatus($sid, $orderId, $itemId, $status, $comment, $quantity);
                    $response = array(
                        'comment' => $comment,
                    );
                }
            } else {
                $image->remove($imagesSizeArray);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse($response);
    }

    public function uploadOrderItemImageAction($request)
    {
        $sid = Session::get('sid');
        $itemId = $request->request('itemId');
        $orderId = $request->request('orderId');
        $uploadType = $request->request('type', Picture::TYPE_UPLOADED);

        if (! in_array($uploadType, Picture::getAvailableTypes())) {
            $this->respondAjaxError('Unknown upload type requested.');
        }

        $response = array();
        try {
            if ($uploadType === Picture::TYPE_LINK) {
                $imagesLinks = $request->getValue('imagesLinks');
                $status = $request->getValue('status');
                $comment = $request->getValue('comment', '');
                $quantity = $request->getValue('quantity', '');
                foreach ($imagesLinks as $key => $link) {
                    if (strpos($comment, $link) !== false) {
                        unset($imagesLinks[$key]);
                    }
                }
                if (empty($imagesLinks)) {
                    $this->respondAjaxError('No new images links given.');
                }
                $validator = new Validator($imagesLinks);
                foreach ($imagesLinks as $key => $link) {
                    $validator->addRule(new URL(), $key, LangAdmin::get('Image link must be a valid URL'));
                }
                if (! $validator->validate()) {
                    $this->respondAjaxError($validator->getErrors());
                }
                $data = $validator->getData();

                $comment = str_replace('\n', "\n", $comment);
                $comment .= "\n" . implode("\n", $data) . "\n";
                $comment = trim(preg_replace("#\n+#si", "\n", $comment), "\n");
                $this->getOtapilib()->ChangeLineStatus($sid, $orderId, $itemId, $status, $comment, $quantity);
                $response = array(
                    'comment' => $comment,
                    'urls' => $data,
                );
            } else {
                $uploadResult = $this->uploadImage($itemId . '/' . OrdersProxy::originOrderId($orderId));
                if (empty($uploadResult['files'])) {
                    $this->respondAjaxError('Failed to upload images.');
                }
                $response['urls'] = array();
                foreach ($uploadResult['files'] as $file) {
                    $response['urls'][] = $file->url;
                }
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse($response, false, ($uploadType === Picture::TYPE_WEBCAM));
    }

    protected function getTicketMessages(& $order, $user)
    {
        $messages = array();
        $ticketList = $this->supportRepository->getTicketInfoList($order['custid']);
        foreach ($ticketList as $item) {
            if (($item['OrderId'] == $order['id']) && ($item['CategoryId'] == 'Common')) {
                $ticketId = str_replace('Ticket-', '', $item['ticketid']);
                $order['ticketId'] = $ticketId;
                $messages = $this->supportRepository->getTicketMessageList($order['custid'], $ticketId, true);
                break;
            }
        }
        $countNew = 0;
        foreach ($messages as $key => $message) {
            if ($message['Direction'] == 'Out') {
                $userName = LangAdmin::get('Operator');
            } else {
                if (!$message['read']) {
                    $countNew++;
                }
                $custName = trim($order['custname']);
                $userName = ! empty($custName)? $custName : $user->getDisplayName();
            }
            $messages[$key]['username'] = $userName;
        }
        $order['ticketMessagesNewCount'] = $countNew;

        return $messages;
    }

    private function moveItemsImages($itemIds, $orderId, $newOrderId)
    {
        $oldWebcamFiles = glob(CFG_APP_ROOT . '/files/ItemCam/' . $orderId . '-*.jpg');
        $oldWebcamFiles = is_array($oldWebcamFiles) ? $oldWebcamFiles : array();

        $oldWebcamFilesThumbs = glob(CFG_APP_ROOT . '/files/ItemCam/thumbs/' . $orderId . '-*.jpg');
        $oldWebcamFilesThumbs = is_array($oldWebcamFilesThumbs) ? $oldWebcamFilesThumbs : array();

        $webcamFiles = array_merge($oldWebcamFiles, $oldWebcamFilesThumbs);
        foreach ($webcamFiles as $name) {
            $newName = str_replace($orderId, $newOrderId, $name);
            rename($name, $newName);
        }

        $uploaded = array();
        foreach ($itemIds as $itemId) {
            $uploaded = array_merge($uploaded, glob(CFG_APP_ROOT . '/uploaded/items/' . $itemId . '/' . $orderId . '/'));
        }
        foreach ($uploaded as $name) {
            $newName = str_replace($orderId, $newOrderId, $name);
            rename($name, $newName);
        }
    }

    // TODO: Убрать дублирование с SiteConfigutaion::uploadImage
    private function uploadImage($uploadDir = null)
    {
        if ($uploadDir) {
            $uploadDir = str_replace('//', '/', '/uploaded/items/' . $uploadDir . '/');
        } else {
            $uploadDir = '/uploaded/items/';
        }
        $uploader = new UploadHandler(array(
            'image_versions' => array(
                'original' => array(
                    'jpeg_quality' => 90
                ),
                'thumbnail_100_100' => array(
                    'max_width' => 100,
                    'max_height' => 100,
                    'jpeg_quality' => 90
                ),
                'thumbnail_150_150' => array(
                    'max_width' => 150,
                    'max_height' => 150,
                    'jpeg_quality' => 90
                ),
                'thumbnail_310_310' => array(
                    'max_width' => 310,
                    'max_height' => 310,
                    'jpeg_quality' => 90
                ),
            )
        ), false, null, $uploadDir);
        return $uploader->post(false);
    }

    private function getFilters($request)
    {
        $cacher = new Cache('getFilters');
        if ($request->valueExists('filter')) {
            $filter = $request->getValue('filter');
            if (! empty($filter['orders_status'])) {
                $filter['orders_status'] = array_filter($filter['orders_status']);
            }
            $filterForCache = $filter;
            $excludeCacheFields = array('number');
            foreach ($excludeCacheFields as $key) {
                if (isset($filterForCache[$key])) {
                    unset($filterForCache[$key]);
                }
            }
            $cacher->set($filterForCache);
            return $filter;
        }
        return $cacher->has() ? $cacher->get() : array();
    }

    private function getUsersIdsByFilters($filter)
    {
        $framePosition = 0;
        $frameSize = 100;
        $uids = array();
        if (! empty($filter['phone']) || ! empty($filter['email'])) {
            // поиск пользователей по фильтрам
            if ($this->inMulti) {
                $this->stopMulti();
                $uids = $this->usersProvider->getUsersByFilters($filter, $framePosition, $frameSize);
                $this->startMulti(true);
            } elseif (! $this->continuedMulti){
                $uids = $this->usersProvider->getUsersByFilters($filter, $framePosition, $frameSize);

            }
            // если пользователи не найдены, но был задан email, то подставляем фейковый ид пользователя, что-бы список заказов был пуст
            if (empty($uids) && ! empty($filter['email'])) {
                $uids[] = -1;
            }
        }
        if (! empty($filter['client_id'])) {
            $uids[] = (int)$filter['client_id'];
        }
        return $uids;
    }

    public function getOrdersListForMergeAction($request)
    {
        $sid = Session::get('sid');
        $orders = array();

        try {
            $orderId = $request->getValue('orderId');
            $uid = $request->getValue('customerId');
            $provider = $request->getValue('provider');
            $page = array('number'=> 0, 'offset'=>0, 'limit'=>100);
            $sort = array();
            $filter = array(
                'period' => '',
                'fromdate' => '',
                'todate' => '',
                'client_id' => '',
                'client_surname' => '',
                'phone' => '',
                'email' => '',
                'number' => '',
                'todate' => '',
                'provider' => $provider,
                'orders_status' => array(
                    10 => 10,
                    11 => 11,
                    20 => 20,
                    30 => 30,
                    31 => 31,
                    32 => 32,
                    36 => 36,
                    37 => 37
                    )

                );

            $uids = array($uid);
            $xmlOrders = $this->ordersProvider->generateSearchParams($filter, $sort, implode(';', $uids));

            $foundOrders = $this->ordersProvider->SearchOrders($sid, $xmlOrders, $page['offset'], $page['limit']);
            foreach ($foundOrders['content'] as $key => $order) {
                if ($orderId == $order['id']) {
                    continue;
                }
                $orders[] = $order;
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->_template = 'orders2merge';

        $this->tpl->assign('orders', $orders);
        $result = $this->fetchTemplateWithoutHeaderAndFooter(false);
        $this->sendAjaxResponse(array('orders' => $result));
    }

    public function mergeOrdersAction($request)
    {
        $result = '';
        try {
            $sid = Session::get('sid');
            $orderId = $request->getValue('orderId');
            $order2Id = $request->getValue('order2Id');

            $id = OrdersProxy::originOrderId($orderId);
            $orderInfo = $this->ordersProvider->GetSalesOrderDetailsForOperator($sid, $id, '', 0);
            $itemIds = array();
            foreach ($orderInfo['saleslineslist'] as $item) {
                $itemIds[] = $item['id'];
            }

            $result = $this->ordersProvider->MergeOrders($sid, $orderId, $order2Id);
            $this->moveItemsImages($itemIds, $orderId, $order2Id);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getErrorMessage());
        }
        $this->sendAjaxResponse(array('result' => $result));
    }

    public function checkHierarchicalConfigurators ($iteminfo) {
        if (! isset($iteminfo['HasHierarchicalConfigurators'])) {
            return $iteminfo;
        }
        if ($iteminfo['HasHierarchicalConfigurators'] == 'false') {
            return $iteminfo;
        }
        $existingConfigs = array();
        $newConfigs = array(
            "id"    =>  1,
            'name'  =>  Lang::get('configuration'),
            'values'=>  array()
        );
        $i = 0;
        /* Перебор всех сущеcтвующих конфигураций товара */
        foreach ($iteminfo['item_with_config'] as $id => &$item) {
            $existingConfigs[$id] = $item['config'];
            $configValues = array();
            $newConfigs['values'][$i] = array(
                'id'    => $id,
                'name'  =>  array(),
                'alias'  =>  array(),
                'name_cny'  => array(),
                'imageurl'  => '',
                'miniimageurl'  => ''
            );

            foreach ($item['config'] as $configId => $configValue) {

                foreach ($iteminfo['configurations'][$configId]['values'] as $value) {

                    if ($value['id'] == $configValue) {
                        $newConfigs['values'][$i]['name'][] =  $value['name'];
                        $newConfigs['values'][$i]['name_cny'][] =  $value['name_cny'];

                        if (strlen(trim($value['alias']))) {
                            $newConfigs['values'][$i]['alias'][] =   $value['alias'];
                        } else {
                            $newConfigs['values'][$i]['alias'][] =   $value['name'];
                        }

                        if (strlen(trim($value['alias_cny']))) {
                            $newConfigs['values'][$i]['alias_cny'][] =   $value['alias_cny'];
                        } else {
                            $newConfigs['values'][$i]['alias_cny'][] =   $value['name'];
                        }

                        if (strlen($value['imageurl'])) {
                            $newConfigs['values'][$i]['imageurl'] =  $value['imageurl'];
                        }
                        if (strlen($value['miniimageurl'])) {
                            $newConfigs['values'][$i]['miniimageurl'] =  $value['miniimageurl'];
                        }
                    }
                }
            }
            $newConfigs['values'][$i]['alias'] = implode(', ', $newConfigs['values'][$i]['alias']);
            $newConfigs['values'][$i]['alias_cny'] = implode(', ', $newConfigs['values'][$i]['alias_cny']);

            $newConfigs['values'][$i]['name'] = implode(', ', $newConfigs['values'][$i]['name']);
            $newConfigs['values'][$i]['name_cny'] = implode(', ', $newConfigs['values'][$i]['name_cny']);

            $item['config'] = array ();
            $item['config'][1] = $i+1;
            $i++;
        }

        $iteminfo['configurations'] = array(1 => $newConfigs);

        return $iteminfo;
    }

    public function editInternalDeliveryAction($request)
    {
        try {
            $sid = Session::get('sid');
            $orderId = $request->getValue('order');
            $orderLines = $request->getValue('lines');
            $price = $request->getValue('internalDeliveryPrice');

            $this->ordersProvider->changeOrderItemsInternalDelivery($sid, $orderId, $orderLines, $price);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse();
    }

    public function editOriginalPriceAction($request)
    {
        try {
            $sid = Session::get('sid');
            $orderId = $request->getValue('order');
            $orderLines = $request->getValue('lines');
            $price = $request->getValue('originalItemPrice');

            $this->ordersProvider->changeOrderItemsOriginalPrice($sid, $orderId, $orderLines, $price);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse();
    }

    public function recalculationItemPriceAction($request)
    {
        try {
            $sid = Session::get('sid');
            $orderId = $request->getValue('order');
            $orderLines = $request->getValue('lines');
            $SetOriginalPrice = ($request->getValue('SetOriginalPrice')) ? 'true' : 'false';
            $SetOriginalDelivery = ($request->getValue('SetOriginalDelivery')) ? 'true' : 'false';

            $xmlUpdateData = "<OrderLineUpdateData>
                                    <SetOriginalPriceByVendor>$SetOriginalPrice</SetOriginalPriceByVendor>
                                    <SetOriginalDeliveryByVendor>$SetOriginalDelivery</SetOriginalDeliveryByVendor>
                              </OrderLineUpdateData>";
            $this->ordersProvider->changeOrderItemPriceByOriginalPrice($sid, $orderId, $orderLines, $xmlUpdateData);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse();
    }

    public function changeDeliveryAddressAction($request)
    {
        $name = $request->getValue('name');
        $orderId = $request->getValue('pk');
        $value  = $request->getValue('value');
        $sid = Session::get('sid');

        try {
            $xmlUpdateData = '<OrderUpdateData><DeliveryAddress><' . $name . '>' . $this->escape($value) . '</' . $name . '></DeliveryAddress></OrderUpdateData>';
            $result = $this->getOtapilib()->UpdateOrderForOperator($sid, $orderId, $xmlUpdateData);

        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse();
    }
}
