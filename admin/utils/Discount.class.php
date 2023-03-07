<?php

OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');

class Discount extends GeneralUtil
{
    protected $_template = 'discount';
    protected $_template_path = 'pricing/';

    /**
     * @var DiscountProvider
     */
    protected $discountProvider;
    protected $categoriesProvider;

    public function __construct()
    {
        parent::__construct();

        if (! CMS::IsFeatureEnabled('Discount')) {
            $this->redirect($this->getPageUrl()->generate(array('cmd' => 'pricing', 'do' => 'default')));
        }

        $this->discountProvider = new DiscountProvider($this->getOtapilib());
        $this->categoriesProvider = new CategoriesProvider($this->cms, $this->getOtapilib());
    }

    public function defaultAction($request)
    {
        try {
            $this->tpl->assign('discounts', $this->discountProvider->getDiscountGroupList());
            $this->tpl->assign('providers', $this->getProviderInfoListAsArray());
        }
        catch (ServiceException $e) {
            $this->errorHandler->CheckSessionExpired($e, $request);
        }
        print $this->fetchTemplate();
    }

    public function editDiscountAction($request)
    {
        $this->_template = 'discount_form';
        $discountGroup = array();
        try {
            if ($request->valueExists('groupId')) {
                $discounts = $this->discountProvider->getDiscountGroupList();
                foreach ($discounts as $discount) {
                    if ($discount['id'] == $request->get('groupId')) {
                       $discountGroup = $discount;
                    }
                }
            }

            $this->tpl->assign('providers', $this->getProviderInfoListAsArray());
        } catch (ServiceException $e) {
            $this->errorHandler->CheckSessionExpired($e, $request);
        }

        $this->tpl->assign('discountGroup', $discountGroup);
        $this->tpl->assign('isNew', $request->valueExists('groupId') ? false : true);
        print $this->fetchTemplate();
    }

    public function saveDiscountAction($request)
    {
        try {
            $params = $this->checkDiscountData($request);
            $this->discountProvider->saveDiscountGroup($params);
        }
        catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function groupInfoAction($request)
    {
        $this->_template = 'discount_group_info';

        $perpage = $request->valueExists('perpage') ? $request->get('perpage') : 10;
        $page = $request->valueExists('page') ? $request->get('page') : 1;

        $discountGroup = array();
        try {
            $discountUsers = $this->discountProvider->searchUsersOfDiscountGroup($request, $perpage, $page);
            $discounts = $this->discountProvider->getDiscountGroupList();
            foreach ($discounts as $discount) {
                if ($discount['id'] == $request->get('groupId')) {
                   $discountGroup = $discount;
                }
            }

        }
        catch (ServiceException $e) {
            $this->errorHandler->CheckSessionExpired($e, $request);
        }

        if ($username = RequestWrapper::get('username')) {
            $this->tpl->assign('username', $username);
        }
        $this->tpl->assign('discounts', $discounts);
        $this->tpl->assign('discountGroup', $discountGroup);
        $this->tpl->assign('discountUsers', $discountUsers);
        $this->tpl->assign('paginator', new Paginator($discountUsers['TotalCount'], $page, $perpage));
        print $this->fetchTemplate();
    }

    public function deleteOrReplaceUserDiscountAction($request)
    {
        try {
            if (! $request->post('newGroupId')) {
                $this->discountProvider->removeUserFromDiscount($request->post('groupId'), $request->post('userId'));
            } elseif ($request->post('isAutomateSetted')){
                $this->discountProvider->addUserToDiscount($request->post('newGroupId'), $request->post('userId'));
            } else {
               $this->discountProvider->removeUserFromDiscount($request->post('groupId'), $request->post('userId'));
               $this->discountProvider->addUserToDiscount($request->post('newGroupId'), $request->post('userId'));
            }
        }
        catch (ServiceException $e) {
           $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function deleteGroupAction($request)
    {
        try {
            $this->discountProvider->removeDiscountGroup($request->post('groupId'));
        }
        catch (ServiceException $e) {
           $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function addUserDiscountAction($request)
    {
        try {
            $this->discountProvider->addUserToDiscount($request->get('groupId'), $request->post('userId'));
        }
        catch (ServiceException $e) {
            if (($e->getErrorCode() === 'AlreadyExists') && ($e->getSubErrorCode() === 'UserAlreadyExistsInOtherDiscountGroup')) {
                $discountGroup = $this->discountProvider->getUserDiscountGroupForOperator($request->post('userId'));

                $discounts = $this->discountProvider->getDiscountGroupList();
                $newGroupId = $request->get('groupId');
                $newDiscountGroupName = '';
                foreach ($discounts as $discount) {
                    if ($discount['id'] == $newGroupId) {
                        $newDiscountGroupName = $discount['name'];
                    }
                }

                $this->sendAjaxResponse(
                    array(
                        'confirm' => 1,
                        'userId' => $request->post('userId'),
                        'login' => $request->post('login'),
                        'groupId' => $discountGroup['id'],
                        'newGroupId' => $newGroupId,
                        'message' => LangAdmin::get('Move_user_to_discount', array(
                            'login'=>$request->post('login'),
                            'groupName' => $discountGroup['name'],
                            'newGroupName' => $newDiscountGroupName,
                        )),
                    ));
            }
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function getUsersForDiscountAction($request)
    {
        try {
            $users = $this->discountProvider->findBaseUserInfoList($request);
            $usersJson = array();
            $fullUsersJson = array();
            foreach ($users['Content'] as $user) {
                $usersJson[] = $user['Login'];
                $fullUsersJson[] = array($user['Login'], $user['id']);
            }
        }
        catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'options' => $usersJson,
            'full' => $fullUsersJson
        ));
    }

    /* TODO: дубликат из admin/utils/Pricing.class.php */
    private function getProviderInfoListAsArray()
    {
        $array = array();

        $providerInfoList = $this->categoriesProvider->GetProviderInfoList();
        foreach ($providerInfoList as $value) {
            $array[$value['Type']] = $value;
        }

        return $array;
    }

    private function checkDiscountData($request)
    {
        $params = $request->getValue('discountGroup');
        if (! isset($params['Name'])) {
            $params['Name'] = '';
        } else {
            $params['Name'] = $params['Name'];
        }
        if (isset($params['Description'])) {
            $params['Description'] = $params['Description'];
        }
        if (! isset($params['Discount'])) {
            $params['Discount'] = '';
        }
        if (empty($params['DiscountIdentificationParametr']['PurchaseVolume'])) {
            $params['DiscountIdentificationParametr']['PurchaseVolume'] = 0;
        }
        if (isset($params['IsDefault']) && $params['IsDefault']) {
            $params['IsDefault'] = 'true';
        }

        $validator = new Validator(array(
            'name'      => $params['Name'],
            'discount'  => $params['Discount'],
            'min_price' => $params['DiscountIdentificationParametr']['PurchaseVolume']
        ));
        $validator->addRule(new NotEmpty(), 'name', LangAdmin::get('Must_be_enter_discount_name'));
        $validator->addRule(new Number(), 'discount', LangAdmin::get('Must_be_enter_discount_value'));

        $validator->addRule(new Range(null, 99), 'discount', LangAdmin::get('Discount_value_is_wrong'));
        $validator->addRule(new Range(0, PHP_INT_MAX), 'min_price', LangAdmin::get('Minprice_must_be_greater_than_zero'));
        

        if (! $validator->validate()) {
           $errors = $validator->getLastError();
           throw new Exception((string)$errors);
        }
        return $params;
    }

}
