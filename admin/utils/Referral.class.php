<?php
	
OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');

OTBase::import('system.lib.referral_system.lib.*');

class Referral extends GeneralUtil 
{
    protected $_template = 'index';
    protected $_template_path = 'referral/';
    /**
     * @var OTAPILib
     */
    protected $otapilib;

    /**
     * @var ReferralUserManager
     */
    protected $referralUserManager;

    /**
     * @var ReferralCategoryManager
     */
    protected $referralCategoryManager;
    
    /**
    * @var PricingProvider
    */
    protected $pricingProvider;
    
    public function __construct()
    {
        parent::__construct();

        $this->referralUserManager = new ReferralUserManager($this->cms);
        $this->referralCategoryManager = new ReferralCategoryManager($this->cms);
        $this->pricingProvider = new PricingProvider($this->getOtapilib());
    }

    public function defaultAction()
    {
        $categories = $this->referralCategoryManager->GetAllCategories();
        $firstCat = '';
		foreach ($categories as $cat) {
			if ($cat['catId']==1) {
				$firstCat = $cat['groupName'];
				break;
			}
		}
		$this->tpl->assign('firstCat', $firstCat);
        $this->tpl->assign('categories', $categories);
        print $this->fetchTemplate();
    }
    
    public function configAction()
    {
        $this->_template = 'config';
        print $this->fetchTemplate();
    }

    public function addGroupFormAction()
    {
        try {
            $this->tpl->assign('currenciesSettings', $this->pricingProvider->GetCurrenciesSettings());
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }        
        $this->_template = 'add_group';
        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function editGroupFormAction($request)
    {
        try {
            $this->tpl->assign('currenciesSettings', $this->pricingProvider->GetCurrenciesSettings());
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }        
        
        $this->_template = 'edit_group';
        $this->tpl->assign('Category', $this->referralCategoryManager->GetById($request->get('id')));
        print $this->fetchTemplate();	    
    }

    /**
     * @param RequestWrapper $request
     */
    public function addGroupAction($request)
    {
        $C = new ReferralCategory();
        $C->SetMinOverallPayment($request->getValue('minPayment'));
        $C->SetProfitPercent($request->getValue('profit')/100);
        $C->SetGroupName($request->getValue('name'));
        $C->SetId(null);

        try {
            $this->referralCategoryManager->Add($C);
        }
        catch (DBException $e) {
            Session::setError($e->getMessage(), $e->getCode());
        }
        $request->LocationRedirect('index.php?cmd=referral&do=default');
    }

    /**
     * @param RequestWrapper $request
     */
    public function editGroupAction($request)
    {
        $C = new ReferralCategory();
        $C->SetMinOverallPayment($request->getValue('minPayment'));
        $C->SetProfitPercent($request->getValue('profit')/100);
        $C->SetGroupName($request->getValue('name'));
        $C->SetId($request->getValue('id'));

        try {
            $this->referralCategoryManager->Remove($request->getValue('id'));
            $this->referralCategoryManager->Add($C);
        }
        catch (DBException $e) {
            Session::setError($e->getMessage(), $e->getCode());
        }

        $request->LocationRedirect('index.php?cmd=referral&do=default');
    }
	
	
	public function delGroupAction($request)
    {
        $group = $request->getValue('id');

        try {
            if ($group == ReferalSystem::DEFAULT_GROUP)
                throw new Exception('Forbidden to delete the default group');

            $this->referralCategoryManager->Remove($group);

        } catch (DBException $e) {
            Session::setError($e->getMessage(), $e->getCode());
        }
        $request->LocationRedirect('index.php?cmd=referral&do=default');
    }

	public function replaceGroupAction($request)
    {
        try {
			$users = $this->referralCategoryManager->GetUsersByCategory($request->getValue('id'));
			foreach ($users as $user) {
				$user->SetCategory(ReferalSystem::DEFAULT_GROUP);
            	$this->referralUserManager->Save($user);
			}			
            $this->referralCategoryManager->Remove($request->getValue('id'));            
        }
        catch (DBException $e) {
            Session::setError($e->getMessage(), $e->getCode());
        }

        $request->LocationRedirect('index.php?cmd=referral&do=default');
    }

    /**
     * @deprecated не используется с 25.10.18
     * @param $request
     */
	public function getCountAction($request)
    {  
		try {
        	$users = $this->referralCategoryManager->GetUsersByCategory($request->getValue('id'));
		}
		catch (DBException $e) {
            Session::setError($e->getMessage(), $e->getCode());
        }
		print count($users);
    }

    /**
     * @param RequestWrapper $request
     */
    public function viewGroupAction($request)
    {
        $this->_template = 'group';
        $page = $this->getPageDisplayParams($request);
        $users = $this->referralCategoryManager->GetUsersByCategory($request->getValue('id'));
        $category = $this->referralCategoryManager->GetById($request->getValue('id'));
        $count = count($users);
        $users = array_slice($users, $page['offset'], $page['limit']);        
        $this->tpl->assign('paginator', new Paginator($count, $page['number'], $page['limit']));       
        
        $this->tpl->assign('users', $users);
		$this->tpl->assign('category', $category);
        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function removeUserAction($request)
    {
        $this->referralUserManager->Remove($request->getValue('id'));
        $request->RedirectToReferrer();
    }

    /**
     * @param RequestWrapper $request
     */
    public function searchUsersAction($request)
    {
        try {
            $users = $this->searchUsers($request->getValue('login'));
            $usersJson = array();
            foreach ($users['Content'] as $user) {
                $usersJson[] = $user['Login'];
            }
        }
        catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }        
        $this->sendAjaxResponse(array('options' => $usersJson));
    }

    private function searchUsers($login)
    {
        $xmlParams = new SimpleXMLElement('<UserFilterParameters></UserFilterParameters>');
        if ($login) {
            $xmlParams->addChild('Login', htmlspecialchars($login));
        }        
        return $users = $this->otapilib->FindBaseUserInfoListFrame(Session::get('sid'), $xmlParams->asXML(), 0, 100);        
    }

    /**
     * @param RequestWrapper $request
     */
    public function addUserToGroupAction($request)
    {
        $validator = new Validator(array(
                'login'        => $request->getValue('login')
        ));
        $validator->addRule(new NotEmptyString(), 'login', LangAdmin::get('User_login_can_not_be_empty')); 
        if (! $validator->validate()) {
            $this->respondAjaxError($validator->getErrors());
        }
        try {                    
            $users = $this->searchUsers($request->getValue('login'));
        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }
        if (empty($users['Content'])) {            
            $this->respondAjaxError(LangAdmin::get('user_not_exist'));
        }

        $userId = 0;
        foreach ($users['Content'] as $userData) {
            if ($userData['Login'] == $request->getValue('login')) {
                $userId = $userData['Id'];
            }
        }
        $userId = $userId ? $userId : $users['Content'][0]['Id'];
        try {
            $user = $this->referralUserManager->GetById($userId);
            $user->SetCategory($request->get('id'));
            $this->referralUserManager->Save($user);
        }
        catch (NotFoundException $e) {
            $userInfo = $this->otapilib->GetUserInfoForOperator(Session::get('sid'), $userId);
            $categoryId =  $request->get('id') ?  $request->get('id') : 1;
            $this->referralUserManager->Add($userInfo['Login'], $userInfo['Id'], 0, $categoryId, 0.0);
            $user = $this->referralUserManager->GetByLogin($userInfo['Login']);
        }
        $category = $this->referralUserManager->GetCategoryInfo($user);
        
        $this->sendAjaxResponse(array(
            'login' => $user->GetLogin(),
            'userId' => $user->GetId(),
            'group' => $category->GetGroupName(),
            'groupId' => $category->GetId(),
        ));        
    }
}