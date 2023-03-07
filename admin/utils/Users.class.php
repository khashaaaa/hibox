<?php

// TODO: Other declaration of class Users exist at www\lib\Users.class.php
class Users extends GeneralUtil
{
    const ITEMS_PER_PAGE = 25;

    const COUNT_USERS_IN_ONE_FILE_EXPORT = 5000;

    const ACTIVE_TAB_COOKIE = 'active_ajax_tab__users_profile';

    protected $multiCurlActions = array(
        'bulkBanUser',
        'bulkUnbanUser',
        'bulkRemoveUser',
        'profile',
    );

    protected $validActiveTabs = array(
        'about',
        'account',
        'orders',
    );

    protected $defaulAction = 'list';

    public function __construct()
    {
        parent::__construct();

        $this->usersProvider = new UsersProvider($this->getOtapilib());
        $this->ordersProvider = new OrdersProvider($this->getOtapilib());
    }

    public function listAction($request)
    {
        $sid = Session::get('sid');

        $page = $this->getPageDisplayParams($request, self::ITEMS_PER_PAGE);
        $perpage = $page['limit'];
        $from = $page['offset'];
        $page = $page['number'];

        $filters = $this->_generateFilters();

        $count = 0;
        $users = array('content' => array(), 'Content' => array(), 'totalcount' => 0, 'TotalCount' => 0);
        try {
            if ($filters !== false) {
                $users = $this->usersProvider->FindBaseUserInfoListFrame($sid, $filters, $from, $perpage);
                $this->protectUsersIfNecessary($users);
            }

            $count = $users['totalcount'];
        } catch (ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
        }

        $frameLimits = $this->getFrameLimits($page, $perpage, $count);

        $this->tpl->assign('users', $users['content']);
        $this->tpl->assign('count', $count);
        $this->tpl->assign('startPos', $frameLimits['start']);
        $this->tpl->assign('endPos', $frameLimits['end']);
        $this->tpl->assign('paginator', new Paginator($count, $page, $perpage));
        $this->tpl->assign('filter', RequestWrapper::get('filter'));

        if ($login = RequestWrapper::get('login')) {
            $this->tpl->assign('login', $login);
        }
        if ($lastname = RequestWrapper::get('lastname')) {
            $this->tpl->assign('lastname', $lastname);
        }
        if ($email = RequestWrapper::get('email')) {
            $this->tpl->assign('email', $email);
        }
        if ($phone = RequestWrapper::get('phone')) {
            $this->tpl->assign('phone', $phone);
        }
        if ($city = RequestWrapper::get('city')) {
            $this->tpl->assign('city', $city);
        }
        if ($fromdate = RequestWrapper::get('registrationDateFrom')) {
            $this->tpl->assign('registrationDateFrom', $fromdate);
        }
        if ($todate = RequestWrapper::get('registrationDateTo')) {
            $this->tpl->assign('registrationDateTo', $todate);
        }

        print $this->fetchTemplate();
    }

    private function protectUsersIfNecessary(&$users)
    {
        if (defined('CFG_PROTECT_USERS_EMAIL') && CFG_PROTECT_USERS_EMAIL) {
            $usersNew = $users;
            $usersNew['content'] = $usersNew['Content'] = array();
            foreach ($users['Content'] as $user) {
                if ($user['Login'] == Session::getUserData('username')) {
                    $usersNew['Content'][] = $user;
                }
            }
            $usersNew['content'] = $usersNew['Content'];
            $users = $usersNew;
        }
    }

    public function addUserAction()
    {
        try {
            $countries = InstanceProvider::getObject()->getDeliveryCountryInfoList();
            $countries = $this->otapilib->GetDeliveryCountryInfoList($countries->asXML());
            $this->tpl->assign('countries', $countries);
        } catch (ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
        }

        $this->tpl->assign('actionTitle', LangAdmin::get('adding'));

        $this->_template = 'form';
        print $this->fetchTemplate();
    }

    public function editUserAction()
    {
        $sid = Session::get('sid');
        $userId = RequestWrapper::get('id');

        try {
            $user = $this->usersProvider->GetUserInfoForOperator($sid, $userId);
            if (empty($user['id'])) {
                throw new ServiceException(__METHOD__, '', 'User not found', 1);
            }
            $this->tpl->assign('user', $user);
            $countries = InstanceProvider::getObject()->getDeliveryCountryInfoList();
            $countries = $this->otapilib->GetDeliveryCountryInfoList($countries->asXML());
            $this->tpl->assign('countries', $countries);
        } catch (ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
        }

        $this->tpl->assign('actionTitle', LangAdmin::get('editing'));

        $this->_template = 'form';
        print $this->fetchTemplate();
    }

    public function profileAction($request)
    {
        $sid = Session::get('sid');
        $userId = $request->getValue('id');

        $pageParams = $this->getPageDisplayParams($request, self::ITEMS_PER_PAGE);
        $perpage = $pageParams['limit'];
        $from = $pageParams['offset'];
        $page = $pageParams['number'];

        try {
            $user = $this->usersProvider->GetUserInfoForOperator($sid, $userId);
            $profiles = $this->usersProvider->GetUserProfileInfoListForOperator($sid, $userId);
            if ($profiles && count($profiles) > 0) {
                $additionalAddresses = array();
                foreach ($profiles as $key => $profile) {
                    $userAddr = $user['countrycode'].$user['city'].$user['address'].$user['phone'].$user['postalcode'].$user['region'];
                    $profileAddr = $profile['countrycode'].$profile['city'].$profile['address'].$profile['phone'].$profile['postalcode'].$profile['region'];
                    if ( strcmp($profileAddr, $userAddr) != 0 ) { 
                        $additionalAddresses[] = $profile; 
                    } else {
                        if (!empty($profile['PassportNumber']) && $profile['PassportNumber']) {
                            $user['PassportNumber'] = $profile['PassportNumber'];
                        }
                        if (!empty($profile['RegistrationAddress']) && $profile['RegistrationAddress']) {
                            $user['RegistrationAddress'] = $profile['RegistrationAddress'];
                        }
                    }
                }
                $user->additionalAddresses = $additionalAddresses;
            }

            $activeTab = $request->getValueSafe('active_tab') ?: Cookie::get(self::ACTIVE_TAB_COOKIE, 'about');
            if (! in_array($activeTab, $this->validActiveTabs)) {
                $activeTab = 'about';
            }

            if ($activeTab == 'account') {
                $userAccount    = $this->usersProvider->GetAccountInfoForOperator($sid, $userId);
                $moneyHistory   = $this->usersProvider->GetStatementForOperator($sid, $userId);

                $this->tpl->assign('userAccount', $userAccount);
                $this->tpl->assign('moneyHistory', $moneyHistory);
            } elseif ($activeTab == 'orders') {
                $orders = $this->ordersProvider->getOrdersByUserId($sid, $userId, null, false, $from, $perpage, true);
                $statusList = $this->ordersProvider->GetOrderStatusList($sid);

                $this->tpl->assign('orders', !empty($orders['Content']) ? $orders['Content'] : array());
                $this->tpl->assign('statusList', $statusList);

                $count = !empty($orders['totalcount']) ? $orders['totalcount'] : 0;
                $this->tpl->assign('paginator', new Paginator($count, $page, $perpage));
            }

            if ($this->inMulti) {
                return;
            } else if (OTBase::isMultiCurlEnabled()) {
                $this->stopMulti();
            }

            $this->tpl->assign('user', $user);
            $this->tpl->assign('activeTab', $activeTab);
            $this->tpl->assign('pageParams', $pageParams);

            $this->_template = 'profile/about';
            $this->tpl->assign('profileAboutBlock', $this->fetchTemplateWithoutHeaderAndFooter(false));

            $this->_template = 'profile/account';
            $this->tpl->assign('profileAccountBlock', $this->fetchTemplateWithoutHeaderAndFooter(false));

            $this->_template = 'profile/orders';
            $this->tpl->assign('profileOrdersBlock', $this->fetchTemplateWithoutHeaderAndFooter(false));

        } catch (ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
        }

        $this->_template = 'profile';
        print $this->fetchTemplate();
    }

    public function getUserOrdersAction($request)
    {
        $sid = Session::get('sid');
        $userId = $request->getValue('userId');
        $page = $this->getPageDisplayParams($request, self::ITEMS_PER_PAGE);
        $perpage = $page['limit'];
        $from = $page['offset'];
        $page = $page['number'];

        $result = array();
        try {
            $orders = $this->ordersProvider->getOrdersByUserId($sid, $userId, null, false, $from, $perpage, true);
            $statusList = $this->ordersProvider->GetOrderStatusList($sid);
            $paginator = new Paginator($orders['TotalCount'], $page, $perpage);
            $paginator->setUrlParams(array('do' => 'profile', 'id' => $userId));
            $this->tpl->assign('statusList', $statusList);
            $this->tpl->assign('orders', $orders['Content']);
            $this->tpl->assign('activeTab', 'orders');
            $this->tpl->assign('ajax', true);
            $this->_template = 'profile/orders';
            $result['html'] = $this->fetchTemplateWithoutHeaderAndFooter(false);
            $result['pagination'] = $paginator->display(false);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse($result);
    }

    public function getAccountInfoAction($request)
    {
        $sid = Session::get('sid');
        $userId = $request->getValue('userId');

        $result = array();
        try {
            $user         = $this->usersProvider->GetUserInfoForOperator($sid, $userId);
            $userAccount  = $this->usersProvider->GetAccountInfoForOperator($sid, $userId);
            $moneyHistory = $this->usersProvider->GetStatementForOperator($sid, $userId);

            $this->tpl->assign('user', $user);
            $this->tpl->assign('userAccount', $userAccount);
            $this->tpl->assign('moneyHistory', $moneyHistory);
            $this->tpl->assign('activeTab', 'account');
            $this->tpl->assign('ajax', true);
            $this->_template = 'profile/account';
            $result['html'] = $this->fetchTemplateWithoutHeaderAndFooter(false);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse($result);
    }

    public function updateAccountAction()
    {
        $sid    = Session::get('sid');
        $userId = RequestWrapper::get('id');
        $userLogin = RequestWrapper::get('login');

        $amount     = RequestWrapper::post('amount');
        $comment    = RequestWrapper::post('comment');
        $isDebit    = RequestWrapper::post('isDebit');
        $date       = date('Y-m-d h-i-s');
        if (empty($amount)) {
            $this->respondAjaxError(LangAdmin::get('Value_must_not_be_empty'));
        }
        try {
            $result = $this->otapilib->PostTransaction($sid, $userId, $amount, $comment, $isDebit, $date);
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', LangAdmin::get('Notify_error'), 1);
            }

            // del cache AccountInfo for user
            $userData = new UserData();
            $userData->ClearAccountInfoCacheByLogin($userLogin);

            $userAccount = $this->usersProvider->GetAccountInfoForOperator($sid, $userId);
            $moneyHistory = $this->usersProvider->GetStatementForOperator($sid, $userId);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array(
            'userAccount' => $userAccount,
            'moneyHistory' => $moneyHistory
        ));
    }

    public function savePasswordAction()
    {
        $sid = Session::get('sid');
        $userId = (int)RequestWrapper::post('Id');

        $password = trim(RequestWrapper::post('Password'));
        if (empty($password) || strlen($password) < 6) {
            $this->respondAjaxError(LangAdmin::get('Password_min_length_6'));
        }

        try {
            $user = $this->usersProvider->GetUserInfoForOperator($sid, $userId);
            if (empty($user['id'])) {
                throw new ServiceException(__METHOD__, '', 'User not found', 1);
            }
            foreach ($user as $key => $field){
                $_POST[$key] = $field;
            }
            $fields = $this->_generateUserFields();
            $this->usersProvider->EditUser($sid, $userId, $fields);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array(
            'message' => LangAdmin::get('Password_successfully_changed')
        ));
    }

    /**
     * @param RequestWrapper $request
     */
    public function saveUserAction($request)
    {
        $sid = Session::get('sid');
        $userId = RequestWrapper::post('id');

        try {
            $fields = $this->_generateUserFields();
            $deliveryFields = $this->_generateUserDeliveryFields();
            if ($userId) {
                $user = $this->usersProvider->GetUserInfoForOperator($sid, $userId);

                $this->usersProvider->EditUser($sid, $userId, $fields);
                if ($deliveryFields)
                    $this->usersProvider->CreateUserProfileForOperator($sid, $userId, $deliveryFields);

                Plugins::invokeEvent('onEditUser', array('user' => $_POST));
                $this->sendAjaxResponse(array('error' => 0, 'message' => LangAdmin::get('Data_added_successfully'), 'id' => $userId));
            } else {
                $newUserId = $this->usersProvider->AddUser($sid, $fields);
                if (empty($newUserId)) {
                    throw new ServiceException(__METHOD__, '', $this->usersProvider->getError(), 1);
                }
                OTBase::import('system.blocks.Subscribe');
                Subscribe::SetSubscribe($request->getValue('Email'), $request->getValue('Login'), $newUserId);
                Plugins::invokeEvent('onAddUser', array('user' => $_POST, 'newUserId' => $newUserId));
                $this->sendAjaxResponse(array('error' => 0, 'message' => LangAdmin::get('Data_added_successfully'), 'id' => $newUserId));
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }

    public function banUserAction($request)
    {
        $sid = Session::get('sid');
        $userId = $request->getValue('id');
        try {
            $result = $this->usersProvider->SetUserBan($sid, $userId, 'true');
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', $this->usersProvider->getError(), 1);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function unbanUserAction($request)
    {
        $sid = Session::get('sid');
        $userId = $request->getValue('id');
        try {
            $result = $this->usersProvider->SetUserBan($sid, $userId, 'false');
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', $this->usersProvider->getError(), 1);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function verifyUserEmailAction($request)
    {
        $sid = Session::get('sid');
        $userId = $request->getValue('id');

        try {
            $result = $this->usersProvider->VerifyUserEmail($sid, $userId);
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', $this->usersProvider->getError(), 1);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function verifyUserPhoneAction($request)
    {
        $sid = Session::get('sid');
        $userId = $request->getValue('id');

        try {
            $this->usersProvider->VerifyUserPhon($sid, $userId);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function removeUserAction($request)
    {
        $sid = Session::get('sid');
        $userId = $request->getValue('id');
        try {
            $result = $this->usersProvider->DeleteUser($sid, $userId);
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', $this->usersProvider->getError(), 1);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function bulkBanUserAction($request)
    {
        $sid = Session::get('sid');
        $userIds = $request->getValue('ids');
        try {
            foreach ($userIds as $id) {
                $result = $this->usersProvider->SetUserBan($sid, $id, 'true');
                if (empty($result) && ! $this->inMulti) {
                    throw new ServiceException(__METHOD__, '', $this->usersProvider->getError(), 1);
                }
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        if ($this->inMulti) {
            return;
        } else if (OTBase::isMultiCurlEnabled()) {
            $this->stopMulti();
        }
        $this->sendAjaxResponse();
    }


    public function bulkUnbanUserAction($request)
    {
        $sid = Session::get('sid');
        $userIds = $request->getValue('ids');
        try {
            foreach ($userIds as $id) {
                $result = $this->usersProvider->SetUserBan($sid, $id, 'false');
                if (empty($result) && ! $this->inMulti) {
                    throw new ServiceException(__METHOD__, '', $this->usersProvider->getError(), 1);
                }
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        if ($this->inMulti) {
            return;
        } else if (OTBase::isMultiCurlEnabled()) {
            $this->stopMulti();
        }
        $this->sendAjaxResponse();
    }

    public function bulkVerifyUserEmail($request)
    {
        $sid = Session::get('sid');
        $userIds = $request->getValue('ids');

        try {
            foreach ($userIds as $id) {
                $result = $this->usersProvider->VerifyUserEmail($sid, $id);
                if (empty($result)) {
                    throw new ServiceException(__METHOD__, '', $this->usersProvider->getError(), 1);
                }
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function bulkVerifyUserPhone($request)
    {
        $sid = Session::get('sid');
        $userIds = $request->getValue('ids');

        try {
            foreach ($userIds as $id) {
                $this->usersProvider->VerifyUserPhone($sid, $id);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function bulkRemoveUserAction($request)
    {
        $sid = Session::get('sid');
        $userIds = $request->getValue('ids');
        try {
            foreach ($userIds as $id) {
                $result = $this->usersProvider->DeleteUser($sid, $id);
                if (empty($result) && ! $this->inMulti) {
                    throw new ServiceException(__METHOD__, '', $this->usersProvider->getError(), 1);
                }
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        if ($this->inMulti) {
            return;
        } else if (OTBase::isMultiCurlEnabled()) {
            $this->stopMulti();
        }
        Session::setMessage(LangAdmin::get('Users_is_deleted'));
        $this->sendAjaxResponse();
    }

    public function ajaxSearchAction()
    {
        $sid = Session::get('sid');
        $param = RequestWrapper::post('param');
        $query = RequestWrapper::post('query');
        $_GET[$param] = $query;

        $filters = $this->_generateFilters();

        try {
            $users = $this->usersProvider->FindBaseUserInfoListFrame($sid, $filters);
            if (empty($users)) {
                throw new ServiceException(__METHOD__, '', $this->usersProvider->getError(), 1);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array(
            'items' => $users['content']
        ));
    }

    /**
     * @param RequestWrapper $request
     */
    public function loginAsUserAction($request)
    {
        $sid = Session::get('sid');
        $login = $request->getValue('login');
        try {
            $authSid = $this->usersProvider->AuthenticateAsUser($sid, $login);
            Session::setUserData(array(
                'sid' => $authSid,
                'username' => $login,
                'IsAuthenticated' => true
            ));
            $this->redirect('../');
        } catch (ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
            $this->redirect($request->env('HTTP_REFERER'));
        }
    }

    /**
     * @param RequestWrapper $request
     */
    public function exportUsersAction($request)
    {
        $sid = Session::get('sid');
        $xmlParams = $this->_generateFilters();
        if (defined('CFG_PROTECT_USERS_EMAIL') && CFG_PROTECT_USERS_EMAIL) {
            $xmlParams = '<UserFilterParameters><Login>'.Session::getUserData('username').'</Login></UserFilterParameters>';
        }
        $type = '';
        $pack = intval($request->getValue('position') / self::COUNT_USERS_IN_ONE_FILE_EXPORT);
        $cacheFile = new FileAndMysqlMemoryCache($this->cms);
        $cacheKey = 'exportUsers:'.$request->getValue('type').'_'.$pack;

        try {
            $usersPack = $this->usersProvider->FindBaseUserInfoListFrame($sid, $xmlParams, $request->getValue('position'), 100);
            $totalCount = $usersPack['TotalCount'];
            $usersPack = $usersPack['Content'];

            // если это не начало фрейма
            if (($request->getValue('position') % self::COUNT_USERS_IN_ONE_FILE_EXPORT) != 0) {
                $usersPackPrev = unserialize($cacheFile->GetCacheEl($cacheKey));
                $usersPack = array_merge($usersPackPrev, $usersPack);
            }
            $cacheFile->AddCacheEl($cacheKey, 600000, serialize($usersPack));

            // если это конец фрейма или конец списка пользователей
            if (
                ((($request->getValue('position') + 100) % self::COUNT_USERS_IN_ONE_FILE_EXPORT) == 0) ||
                (($request->getValue('position') + 100) >= $totalCount)
            ) {
                $this->prepareExportFile($request->getValue('type'), $pack, $cacheKey);
            }

            // если это конец списка пользователей
            if (($request->getValue('position') + 100) >= $totalCount) {
                $type = $request->getValue('type');
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'type' => $type,
            'totalCount' => $totalCount
        ));
    }

    public function dowmloadExportUsersFileAction($request)
    {
        require dirname(dirname(__FILE__)).'/lib/pclzip/pclzip.lib.php';

        $type = $request->getValue('type');
        $totalCount = $request->getValue('totalCount');

        $packMax = intval($totalCount / self::COUNT_USERS_IN_ONE_FILE_EXPORT);

        // формируем zip архив из файлов выгрузки
        $path = dirname(dirname(dirname(__FILE__)));
        $files = array();
        for ($pack = 0; $pack <= $packMax; $pack++) {
            $files[] = $path . '/cache/users-' . $pack . '-' . $type . '.' . $type;
        }

        $archiveName = 'users-' . date('Y-m-d_h-i-s') . '.zip';
        $archive = new PclZip($path . '/cache/' . $archiveName);
        $archive->create($files, PCLZIP_OPT_REMOVE_PATH, $path . '/cache/');

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header('Content-Disposition: attachment; filename="' . $archiveName . '"');
        readfile('../cache/' . $archiveName);
    }

    private function prepareExportFile($type, $pack, $cacheKey)
    {
        $cacheFile = new FileAndMysqlMemoryCache($this->cms);
        $usersPack = unserialize($cacheFile->GetCacheEl($cacheKey));
        $cacheFile->DelCacheEl($cacheKey);
        $file = '../cache/users-' . $pack . '-' . $type . '.' . $type;
        switch ($type) {
            case 'xml':
                $this->setExportFileXML($file, $usersPack);
                break;
            case 'xls':
                $this->setExportFileXLS($file, $usersPack);
                break;
            case 'txt':
                $this->setExportFileTXT($file, $usersPack);
                break;
        }
        return $type;

    }


    private function setExportFileXML($file, $usersPack)
    {
        $xml = new SimpleXMLElement('<Users/>');
        foreach($usersPack as $user){
            $el = $xml->addChild('User');
            $el->addChild('OtapiId', htmlspecialchars($user['Id']));
            $el->addChild('Login', htmlspecialchars($user['Login']));
            $el->addChild('Email', htmlspecialchars($user['Email']));
            $el->addChild('LastName', htmlspecialchars($user['LastName']));
            $el->addChild('FirstName', htmlspecialchars($user['FirstName']));
            $el->addChild('MiddleName', htmlspecialchars($user['MiddleName']));
            $el->addChild('FIO', htmlspecialchars($user['LastName'] . ' ' . $user['FirstName'] . ' ' . $user['MiddleName']));
            $el->addChild('Phone', htmlspecialchars($user['Phone']));
        }
        $data = $xml->asXML();
        file_put_contents($file, $data);
    }

    private function setExportFileTXT($file, $usersPack)
    {
        $data = '';
        foreach($usersPack as $user){
            $data .= $user['Login'] . '|';
            $data .= $user['Email'] . '|';
            $data .= $user['LastName'] . ' ' . $user['FirstName'] . ' ' . $user['MiddleName'] . '|';
            $data .= $user['Phone'] . '|';
            $data .= "\r\n";
        }
        file_put_contents($file, $data);
    }

    private function setExportFileXLS($file, $usersPack)
    {
        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel.php';
        $pExcel = new PHPExcel();
        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Writer/Excel5.php';
        $objWriter = new PHPExcel_Writer_Excel5($pExcel);
        $pExcel->setActiveSheetIndex(0);
        $aSheet = $pExcel->getActiveSheet();
        $aSheet->setTitle(LangAdmin::get('customers'));

        $aSheet->setCellValue('A1', 'Login');
        $aSheet->setCellValue('B1', 'Email');
        $aSheet->setCellValue('C1', 'FIO');
        $aSheet->setCellValue('D1', 'Phone');
        foreach($usersPack as $i => $user){
            $aSheet->setCellValue('A' . ($i + 2), $user['Login']);
            $aSheet->setCellValue('B' . ($i + 2), $user['Email']);
            $aSheet->setCellValue('C' . ($i + 2), $user['LastName'] . ' ' . $user['FirstName'] . ' ' . $user['MiddleName']);
            $aSheet->setCellValue('D' . ($i + 2), $user['Phone']);
        }
        $aSheet->getColumnDimension('A')->setWidth(20);
        $aSheet->getColumnDimension('B')->setWidth(30);
        $aSheet->getColumnDimension('C')->setWidth(40);
        $aSheet->getColumnDimension('D')->setWidth(15);
        $objWriter->save($file);
    }

    private function _generateFilters()
    {
        $xmlParams = new SimpleXMLElement('<UserFilterParameters></UserFilterParameters>');
        if (RequestWrapper::get('filter')) {
            $filter = RequestWrapper::get('filter');
            if (isset($filter['user_id'])) $xmlParams->addChild('IdList', $filter['user_id']);
        }
        if (RequestWrapper::get('login')) $xmlParams->addChild('Login', @htmlspecialchars(RequestWrapper::get('login')));
        if (RequestWrapper::get('email')) $xmlParams->addChild('Email', RequestWrapper::get('email'));
        if (RequestWrapper::get('lastname')) $xmlParams->addChild('LastName', RequestWrapper::get('lastname'));
        if (RequestWrapper::get('isactive')) $xmlParams->addChild('IsActive', RequestWrapper::get('isactive'));
        if (RequestWrapper::get('phone')) $xmlParams->addChild('Phone', @htmlspecialchars(RequestWrapper::get('phone')));
        if (RequestWrapper::get('city')) $xmlParams->addChild('City', @htmlspecialchars(RequestWrapper::get('city')));
        if (RequestWrapper::get('registrationDateFrom')) $xmlParams->addChild('RegistrationDateFrom', date("Y-m-d", strtotime(RequestWrapper::get('registrationDateFrom'))));
        if (RequestWrapper::get('registrationDateTo')) $xmlParams->addChild('RegistrationDateTo', date("Y-m-d", strtotime(RequestWrapper::get('registrationDateTo'))));

        if (defined('CFG_PROTECT_USERS_EMAIL') && CFG_PROTECT_USERS_EMAIL && Session::getUserData('username') == null){
            return false;
        } elseif (defined('CFG_PROTECT_USERS_EMAIL') && CFG_PROTECT_USERS_EMAIL) {
            $xmlParams->Login = htmlspecialchars(Session::getUserData('username'));
        }

        if (RequestWrapper::post('sort_by'))
            $xmlParams->addChild('OrderBy', RequestWrapper::post('sort_by'));
        elseif (RequestWrapper::get('sort_by'))
            $xmlParams->addChild('OrderBy', RequestWrapper::get('sort_by'));

        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }

    private function _generateUserFields()
    {
        $xmlParams = new SimpleXMLElement('<UserUpdateData></UserUpdateData>');
        if (! is_null(RequestWrapper::post('id'))) $xmlParams->addChild('Id', @htmlspecialchars(RequestWrapper::post('id')));
        if (! is_null(RequestWrapper::post('Email'))) $xmlParams->addChild('Email', RequestWrapper::post('Email'));
        if (! is_null(RequestWrapper::post('Login'))) $xmlParams->addChild('Login', RequestWrapper::post('Login'));
        if (! is_null(RequestWrapper::post('Password'))) $xmlParams->addChild('Password', trim(RequestWrapper::post('Password')));

        if (RequestWrapper::post('IsActive')) $xmlParams->addChild('IsActive', 'true');
        else $xmlParams->addChild('IsActive', 'false');

        if (! is_null(RequestWrapper::post('FirstName'))) $xmlParams->addChild('FirstName', RequestWrapper::post('FirstName'));
        if (! is_null(RequestWrapper::post('LastName'))) $xmlParams->addChild('LastName', RequestWrapper::post('LastName'));
        if (! is_null(RequestWrapper::post('MiddleName'))) $xmlParams->addChild('MiddleName', RequestWrapper::post('MiddleName'));
        if (! is_null(RequestWrapper::post('Sex'))) $xmlParams->addChild('Sex', RequestWrapper::post('Sex'));

        if (! is_null(RequestWrapper::post('Country'))) $xmlParams->addChild('CountryCode', htmlspecialchars(RequestWrapper::post('Country')));
        if (! is_null(RequestWrapper::post('DeliveryCountryCode'))) $xmlParams->addChild('DeliveryCountryCode', htmlspecialchars(RequestWrapper::post('DeliveryCountryCode')));
        if (! is_null(RequestWrapper::post('DeliveryCountryCode'))) $xmlParams->addChild('CountryCode', htmlspecialchars(RequestWrapper::post('DeliveryCountryCode')));
        if (! is_null(RequestWrapper::post('City'))) $xmlParams->addChild('City', htmlspecialchars(RequestWrapper::post('City')));
        if (! is_null(RequestWrapper::post('Address'))) $xmlParams->addChild('Address', htmlspecialchars(RequestWrapper::post('Address')));
        if (! is_null(RequestWrapper::post('Phone'))) $xmlParams->addChild('Phone', RequestWrapper::post('Phone'));
        if (! is_null(RequestWrapper::post('PostalCode'))) $xmlParams->addChild('PostalCode', RequestWrapper::post('PostalCode'));
        if (! is_null(RequestWrapper::post('Region'))) $xmlParams->addChild('Region', RequestWrapper::post('Region'));
        if (! is_null(RequestWrapper::post('Skype'))) $xmlParams->addChild('Skype', RequestWrapper::post('Skype'));

        if (! is_null(RequestWrapper::post('RecipientFirstName'))) $xmlParams->addChild('RecipientFirstName', RequestWrapper::post('RecipientFirstName'));
        if (! is_null(RequestWrapper::post('RecipientLastName'))) $xmlParams->addChild('RecipientLastName', RequestWrapper::post('RecipientLastName'));
        if (! is_null(RequestWrapper::post('RecipientMiddleName'))) $xmlParams->addChild('RecipientMiddleName', RequestWrapper::post('RecipientMiddleName'));

        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }


    private function _generateUserDeliveryFields()
    {
        $xmlParams = new SimpleXMLElement('<UserProfileCreateData></UserProfileCreateData>');
        if (RequestWrapper::post('new_recipientFirstName')) $xmlParams->addChild('FirstName', RequestWrapper::post('new_recipientFirstName')); else return false;
        if (RequestWrapper::post('new_recipientLastName')) $xmlParams->addChild('LastName', RequestWrapper::post('new_recipientLastName'));
        if (RequestWrapper::post('new_recipientMiddleName')) $xmlParams->addChild('MiddleName', RequestWrapper::post('new_recipientMiddleName'));
        if (RequestWrapper::post('new_recipientCountry')) $xmlParams->addChild('CountryCode', htmlspecialchars(RequestWrapper::post('new_recipientCountry')));
        if (RequestWrapper::post('new_recipientCity')) $xmlParams->addChild('City', htmlspecialchars(RequestWrapper::post('new_recipientCity')));
        if (RequestWrapper::post('new_recipientAddress')) $xmlParams->addChild('Address', htmlspecialchars(RequestWrapper::post('new_recipientAddress')));
        if (RequestWrapper::post('new_recipientPhone')) $xmlParams->addChild('Phone', RequestWrapper::post('new_recipientPhone'));
        if (RequestWrapper::post('new_recipientPostalCode')) $xmlParams->addChild('PostalCode', RequestWrapper::post('new_recipientPostalCode'));
        if (RequestWrapper::post('new_recipientRegion')) $xmlParams->addChild('Region', RequestWrapper::post('new_recipientRegion'));
        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }
}
