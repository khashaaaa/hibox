<?php
class Administrators extends GeneralUtil
{
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'admins';
    protected $_template_path = 'users/admins/';

    /**
     * @var AdminsProvider
     */
    protected $adminsProvider;

    /**
     * @var Array
     */
    protected $addedIds;

    /**
     * @var Array
     */
    protected $deletedIds;

    const SUPER_ADMIN_ROLE = 'SuperAdmin';

    public function __construct()
    {
        parent::__construct();

        $this->adminsProvider = new AdminsProvider();
    }

    /**
     * @param RequestWrapper $request
     */
    public function defaultAction($request)
    {
        try {
            $users = $this->adminsProvider->GetInstanceUserList(Session::get('sid'));
            $this->tpl->assign('users', $users);
        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }

        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function addAction($request)
    {
        $sid = Session::get('sid');

        try {
            $this->rolesProvider = new RolesProvider();
            $this->tpl->assign('roles', $this->rolesProvider->GetAvailableRoleList($sid));
        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }

        if (RequestWrapper::post('Login')) {
            $user = array();
            $user['Login'] = RequestWrapper::post('Login');
            $user['Password'] = RequestWrapper::post('Password');
            $user['Email'] = RequestWrapper::post('Email');
            $user['Roles'][0]['Name'] = RequestWrapper::post('RoleName');
            $user['Name'] = RequestWrapper::post('Name');

            $this->tpl->assign('user', $user);

        }

        $this->tpl->assign('actionTitle', LangAdmin::get('adding'));

        $this->_template = 'form';
        print $this->fetchTemplate();
    }


    /**
     * @param RequestWrapper $request
     */
    public function removeAction($request)
    {
        $sid = Session::get('sid');
        $login = $request->getValue('login');
        try {
            $result = $this->adminsProvider->DeleteInstanceUser($sid, $login);
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', $this->adminsProvider->getError(), 1);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }


    /**
     * @param RequestWrapper $request
     */
    public function saveAction($request)
    {
        $sid = Session::get('sid');
        $id = RequestWrapper::post('Id');
        $login = RequestWrapper::post('CurrentLogin');

        try {
            if ($id) {
                $fields = $this->generateUserFields(false);
                $this->adminsProvider->UpdateInstanceUser($sid, $fields);

                if (RequestWrapper::post('RoleName') != RequestWrapper::post('CurrentRoleName')) {
                    if (RequestWrapper::post('CurrentRoleName')) {
                        $this->adminsProvider->RemoveInstanceUserFromRole($sid, RequestWrapper::post('CurrentRoleName'), $login);
                    }

                    if (RequestWrapper::post('RoleName')) {
                        $this->adminsProvider->AddInstanceUserToRole($sid, RequestWrapper::post('RoleName'), $login);
                    }

                }

                Session::setMessage(LangAdmin::get('Data_updated_successfully'));
                $this->redirect('index.php?cmd=administrators&do=default');
            } else {
                $fields = $this->generateUserFields();
                $newUser = $this->adminsProvider->CreateInstanceUser($sid, $fields);
                if (empty($newUser)) {
                    throw new ServiceException(__METHOD__, '', $this->usersProvider->getError(), 1);
                } elseif (RequestWrapper::post('RoleName')) {
                    $this->adminsProvider->AddInstanceUserToRole($sid, RequestWrapper::post('RoleName'), RequestWrapper::post('Login'));
                }

                Session::setMessage(LangAdmin::get('Data_added_successfully'));
                $this->redirect('index.php?cmd=administrators&do=default');
            }
        } catch (ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
        } catch (Exception $e) {
            Session::setError($e->getMessage(), $e->getCode());
        }

        if ($id) {
            $this->redirect('index.php?cmd=administrators&do=edit&login=' . $login);
        } else {
            $this->addAction($request);
        }
    }

    public function editAction()
    {
        $sid = Session::get('sid');
        $login = RequestWrapper::get('login');

        try {
            $user = $this->adminsProvider->GetInstanceUserByLogin($sid, $login);
            if (empty($user['id'])) {
                throw new ServiceException(__METHOD__, '', 'User not found', 1);
            }
            $this->rolesProvider = new RolesProvider();
            $this->tpl->assign('roles', $this->rolesProvider->GetAvailableRoleList($sid));
            $this->tpl->assign('user', $user);
        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }

        $this->tpl->assign('actionTitle', LangAdmin::get('editing'));

        $this->_template = 'form';
        print $this->fetchTemplate();

    }

    private function generateUserFields($create = true)
    {
        if ($create) {
            $xmlParams = new SimpleXMLElement('<InstanceUserCreateData></InstanceUserCreateData>');
            if (RequestWrapper::post('Login')) {
                $xmlParams->addChild('Login', $this->escape(RequestWrapper::post('Login')));
            }
        } else {
            $xmlParams = new SimpleXMLElement('<InstanceUserUpdateData></InstanceUserUpdateData>');
            $xmlParams->addChild('Login', RequestWrapper::post('CurrentLogin'));
        }

        if (RequestWrapper::post('Name')) $xmlParams->addChild('Name', $this->escape(RequestWrapper::post('Name')));
        if (RequestWrapper::post('Email')) $xmlParams->addChild('Email', $this->escape(RequestWrapper::post('Email')));
        if (RequestWrapper::post('Password')) $xmlParams->addChild('Password', $this->escape(trim(RequestWrapper::post('Password'))));

        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }
}
