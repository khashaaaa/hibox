<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dima
 * Date: 13.06.13
 * Time: 11:08
 * To change this template use File | Settings | File Templates.
 */

require_once dirname(dirname(dirname(__FILE__))) . '/lib/referral_system/lib/ReferralUserManager.php';
require_once dirname(dirname(dirname(__FILE__))) . '/lib/referral_system/lib/ReferralCategoryManager.php';
require_once dirname(dirname(dirname(__FILE__))) . '/lib/referral_system/lib/ReferralCategory.php';
require_once dirname(dirname(dirname(__FILE__))) . '/lib/referral_system/lib/ReferralUser.php';

class Referralold extends GeneralUtil {
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'index';
    protected $_template_path = '/referral/';

    /**
     * @var ReferralUserManager
     */
    protected $referralUserManager;

    /**
     * @var ReferralCategoryManager
     */
    protected $referralCategoryManager;
	

    public function __construct(){
        parent::__construct();


        if (! Login::auth()) {
            header('Location: index.php?expired');
            die;
        } 

        $this->referralUserManager = new ReferralUserManager($this->cms);
        $this->referralCategoryManager = new ReferralCategoryManager($this->cms);
		
    }

    public function defaultAction(){
        $categories = $this->referralCategoryManager->GetAllCategories();
		
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

    public function addGroupFormAction(){
        $this->_template = 'add_group';
        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function editGroupFormAction($request){
        $this->_template = 'edit_group';
        $this->tpl->assign('Category', $this->referralCategoryManager->GetById($request->get('id')));
        print $this->fetchTemplate();	    
    }

    /**
     * @param RequestWrapper $request
     */
    public function addGroupAction($request){
        $C = new ReferralCategory();
        $C->SetMinOverallPayment($request->getValue('minPayment'));
        $C->SetProfitPercent($request->getValue('profit')/100);
        $C->SetGroupName($request->getValue('name'));
        $C->SetId(null);

        try{
            $this->referralCategoryManager->Add($C);
        }
        catch(DBException $e){
            Session::setError($e->getMessage(), $e->getCode());
        }

        $request->LocationRedirect('index.php?cmd=referralold');
    }

    /**
     * @param RequestWrapper $request
     */
    public function editGroupAction($request){
        $C = new ReferralCategory();
        $C->SetMinOverallPayment($request->getValue('minPayment'));
        $C->SetProfitPercent($request->getValue('profit')/100);
        $C->SetGroupName($request->getValue('name'));
        $C->SetId($request->getValue('id'));

        try{
            $this->referralCategoryManager->Remove($request->getValue('id'));
            $this->referralCategoryManager->Add($C);
        }
        catch(DBException $e){
            Session::setError($e->getMessage(), $e->getCode());
        }

        $request->LocationRedirect('index.php?cmd=referralold');
    }
	
	
	public function delGroupAction($request){
        try{
            $this->referralCategoryManager->Remove($request->getValue('id'));            
        }
        catch(DBException $e){
            Session::setError($e->getMessage(), $e->getCode());
        }

        $request->LocationRedirect('index.php?cmd=referralold');
    }
	
	
	public function replaceGroupAction($request){
        try{
			$users = $this->referralCategoryManager->GetUsersByCategory($request->getValue('id'));
			foreach($users as $user){
				$user->SetCategory(1);
            	$this->referralUserManager->Save($user);
			}			
            $this->referralCategoryManager->Remove($request->getValue('id'));            
        }
        catch(DBException $e){
            Session::setError($e->getMessage(), $e->getCode());
        }

        $request->LocationRedirect('index.php?cmd=referralold');
    }
	
	public function getCountAction($request){  
		try{
        	$users = $this->referralCategoryManager->GetUsersByCategory($request->getValue('id'));
		}
		catch(DBException $e){
            Session::setError($e->getMessage(), $e->getCode());
        }
		print count($users);
		die();
    }

    /**
     * @param RequestWrapper $request
     */
    public function viewGroupAction($request){
        $this->_template = 'group';
        $users = $this->referralCategoryManager->GetUsersByCategory($request->getValue('id'));
        $this->tpl->assign('users', $users);
		$this->tpl->assign('cat', $request->getValue('id'));
        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function removeUserAction($request){
        $this->referralUserManager->Remove($request->getValue('id'));
        $request->RedirectToReferrer();
    }

    /**
     * @param RequestWrapper $request
     */
    public function searchUsersAction($request){
        $users = $this->searchUsers($request->getValue('login')); 		
		
		    
		$userSerach="<ul>";
        foreach ($users['Content'] as $user) {
            $userSerach.="<li><a onclick=\"SetUser('{$user['Login']}')\">".$user['Login']."</a></li>";
        }
        $userSerach.="</ul>";
        echo $userSerach;
		
		die();        
    }

    private function searchUsers($login){
		global $otapilib;
        $xmlParams = new SimpleXMLElement('<UserFilterParameters></UserFilterParameters>');
        if($login)
            $xmlParams->addChild('Login', htmlspecialchars($login));
		
        try{
            return $users = $otapilib->FindBaseUserInfoListFrame(Session::get('sid'), $xmlParams->asXML(), 0, 100);
        }
        catch(ServiceException $e){
            $this->throwAjaxError($e);
        }
    }

    /**
     * @param RequestWrapper $request
     */
    public function addUserToGroupAction($request){
		global $otapilib;
        try{
            $users = $this->searchUsers($request->getValue('login'));
        }
        catch(ServiceException $e){
            $this->throwAjaxError($e);
        }

        if(empty($users['Content'])){
            $e = new ServiceException('', '', LangAdmin::get('user_not_exist'), LangAdmin::get('illegal_login'));
            $this->throwAjaxError($e);
        }

        try{
            $user = $this->referralUserManager->GetById($users['Content'][0]['Id']);
            $user->SetCategory($request->get('id'));
            $this->referralUserManager->Save($user);
        }
        catch(NotFoundException $e){
            $userInfo = $otapilib->GetUserInfoForOperator(Session::get('sid'), $users['Content'][0]['Id']);
            $categoryId =  $request->get('id') ?  $request->get('id') : 1;
            $this->referralUserManager->Add($userInfo['Login'], $userInfo['Id'], 0, $categoryId, 0.0);
            $user = $this->referralUserManager->GetByLogin($userInfo['Login']);
        }
        $category = $this->referralUserManager->GetCategoryInfo($user);
        
		$request->LocationRedirect('index.php?cmd=referralold&do=viewGroup&id='.$request->get('id'));
    }
}