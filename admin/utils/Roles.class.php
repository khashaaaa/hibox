<?php
class Roles extends GeneralUtil
{
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'roles';
    protected $_template_path = 'users/roles/';
    /**
     * @var SiteConfigurationRepository
     */
    protected $siteConfigRepository;
    /**
     * @var RolesProvider
     */
    protected $rolesProvider;

    /**
     * @var Array
     */
    protected $addedIds;

    /**
     * @var Array
     */
    protected $deletedIds;

    public function __construct()
    {
        parent::__construct();

        $this->cms->checkTable('site_config');
        $this->cms->checkTable('site_langs');
        $this->siteConfigRepository = new SiteConfigurationRepository($this->cms);
        $this->rolesProvider = new RolesProvider();
    }

    public function defaultAction($request)
    {
        $roles = array();

        try {
            $roles = $this->rolesProvider->GetAvailableRoleList(Session::get('sid'));
        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }

        $this->tpl->assign('roles', $roles);
        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function showAction($request)
    {
        $sid = Session::get('sid');
        $roleName = $request->getValue('name') ? urldecode($request->getValue('name')): '';

        if (empty($roleName)) {
            Session::setError(LangAdmin::get('not_selected_role'));
            $urlRedirect = $this->pageUrl->AssignCmdAndDo('roles', 'default');
            RequestWrapper::LocationRedirect($urlRedirect);
        }

        try {
            $roles = $this->rolesProvider->GetAvailableRoleList($sid);
            $roleRights = $this->rolesProvider->GetRightTree($sid, $roleName, 'false');

            $roleOptions = self::getRoleByName($roleName, $roles);

            $this->tpl->assign('view_only', true);
            $this->tpl->assign('role_name', $roleName);
            $this->tpl->assign('role_options', $roleOptions);
            $this->tpl->assign('role_rights', $roleRights);
        } catch (ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
        }

        $this->tpl->assign('actionTitle', LangAdmin::get('viewing') . ' - ' . $roleName);

        $this->_template = 'form';
        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function editAction($request)
    {
        $sid = Session::get('sid');
        $roleName = $request->getValue('name') ? urldecode($request->getValue('name')): '';

        try {
            $availableRoles = $this->rolesProvider->GetAvailableRoleList($sid);
            $templateRoles = $this->rolesProvider->GetTemplateRoleList($sid);

            if ($roleName) {
                $roleRights = $this->rolesProvider->GetRightTree($sid, $roleName, 'false');
            } else {
                $roleRights = $this->rolesProvider->GetRightTree($sid, $roleName, 'true');
            }

            $roleOptions = self::getRoleByName($roleName, $availableRoles);

            $this->tpl->assign('roles', $availableRoles);
            $this->tpl->assign('role_name', $roleName);
            $this->tpl->assign('role_options', $roleOptions);
            $this->tpl->assign('role_rights', $roleRights);
            $this->tpl->assign('template_roles', $templateRoles);
        } catch (ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
        }

        $actionTitle = empty($roleName) ? LangAdmin::get('adding') : LangAdmin::get('editing') . ' - ' . $roleName;
        $this->tpl->assign('actionTitle', $actionTitle);

        $this->_template = 'form';
        print $this->fetchTemplate();
    }


    /**
     * @param RequestWrapper $request
     */
    public function getRightsListAction($request)
    {
        $sid = Session::get('sid');
        $roleName = RequestWrapper::post('rolename') ? urldecode(RequestWrapper::post('rolename')): '';
        $roleRights = $this->rolesProvider->GetRightTree($sid, $roleName, 'true');

        $data = array();
        try {
            $data['ids'] = $this->getIdsRoleRights($roleRights);

        } catch (ServiceException $e) {
            if ($e->getCode() != 'NotFound') {
                $this->respondAjaxError($e->getMessage());
            }
        } catch (DBException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse($data);

    }

    /**
     * @param RequestWrapper $request
     */
    public function saveAction($request)
    {
        $sid = Session::get('sid');
        $existRoleName = RequestWrapper::post('exists');

        try {
            if ($existRoleName) {
                $rights = $this->rolesProvider->GetRightTree($sid, $existRoleName, 'false');

                $this->generateIdsForUpdate($rights);

                /**  добавление новых прав */
                if (count($this->addedIds)) {
                    $xmlParams = $this->generateXmlIds($this->addedIds);
                    if ($xmlParams) {
                        $r = $this->rolesProvider->AttachRightsToRole($sid, $existRoleName, $xmlParams);
                    }
                }

                /** удаление привязанных прав */
                if (count($this->deletedIds)) {
                    $xmlParams = $this->generateXmlIds($this->deletedIds);
                    if ($xmlParams) {
                        $r = $this->rolesProvider->DeattachRightsFromRole($sid, $existRoleName, $xmlParams);
                    }
                }
            } elseif (RequestWrapper::post('TemplateRole')) {
                /** создание новой шаблонной роли */
                $newRoleId = $this->rolesProvider->CreateInstanceRoleFromTemplate($sid, RequestWrapper::post('TemplateRole'));
                if (empty($newRoleId)) {
                    throw new ServiceException(__METHOD__, '', $this->rolesProvider->getError(), 1);
                }
            } else {
                /** создание новой роли с произвольным набором действий и фич  */
                $xmlParams = $this->generateCreateRoleXml();

                $newRoleId = $this->rolesProvider->CreateInstanceRole($sid, $xmlParams);
                if (empty($newRoleId)) {
                    throw new ServiceException(__METHOD__, '', $this->rolesProvider->getError(), 1);
                }

                $this->generateIdsForUpdate();
                $xmlParams = $this->generateXmlIds($this->addedIds);
                if ($xmlParams) {
                    $r = $this->rolesProvider->AttachRightsToRole($sid, RequestWrapper::post('RoleName'), $xmlParams);
                }
            }
        } catch (ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
        } catch (Exception $e) {
            Session::setError($e->getMessage(), '');
        }
        $this->redirect('index.php?cmd=roles');
    }

    public function removeAction()
    {
        $sid = Session::get('sid');
        $name = RequestWrapper::post('name');
        try {
            $result = $this->rolesProvider->DeleteInstanceRole($sid, $name);
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', $this->rolesProvider->getError(), 1);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    static public function printRoleTree($item, $level, $roleRights = array(), $view_only = false)
    {
        if (!isset($item['Item'])) {
            return;
        }

        foreach ($item['Item'] as $child) {
            $id = $child['Id'];
            $name = "Rights[]";
            $style = 'style="margin-left:' . ($level) * 20 . 'px"';
            $checked = ($child['IsTurnOn'] == 'true') ? "checked='checked'" : "";
            $diabled = ($view_only == 'true') ? "disabled='disabled'" : "";
            $title = OTBase::isTest() ? $child['Description'] . ' (<code>' . $child['Name'] . '</code>)' : $child['Description'];

            echo '<li>';
            echo '<label class="checkbox">';
            echo '<input data-right="' . $child['Name'] . '" class="not_from_template right-'. $id . '" type="checkbox" name="' .
                $name . '" value="' . (string)$id . '" ' . $checked .'" ' . $diabled .'" ' . '> ';

            echo ! empty($child['Description']) ? $title : LangAdmin::get('Feature_' . $child['Name']);

            echo '</label>';
            if (isset($child['Item'])) {
                echo '<ul class="unstyled" data-level="' . $level . '" ' . $style . '>';
                    self::printRoleTree($child, $level+1, $roleRights, $view_only);
                echo '</ul>';
            }
            echo '</li>';
        }
    }


    static public function getRoleByName($roleName, $roles)
    {
        foreach ($roles as $item) {
            if ((string)$item['Name'] == $roleName) {
                return $item;
            }
        }
        return false;
    }

    private function generateXmlIds ($idsArray)
    {
        return implode(';', $idsArray);
    }

    private function generateCreateRoleXml()
    {
        return "<InstanceUserRoleCreateData Name='" .
            $this->escape(RequestWrapper::post('RoleName'))."' Description='".
            $this->escape(RequestWrapper::post('RoleName'))."' Enabled='true'/>";
    }

    private function generateIdsForUpdate($rights = false)
    {
        $incomeRights = RequestWrapper::post('Rights') ? RequestWrapper::post('Rights') : array();

        $this->addedIds = $this->deletedIds = array();

        if ($rights) {
            $currentIds = $this->getIdsRoleRights($rights);
        } else {
            $currentIds = array();
        }

        $this->deletedIds = array_diff($currentIds, $incomeRights);
        $this->addedIds = array_diff($incomeRights, $currentIds);
    }

    private function getIdsRoleRights($rights)
    {
        $ids = array();
        foreach ($rights as $item) {
            if ($item['isturnon'] == 'true') {
                $ids[] = $item['id'];
            }
            if (isset($item['item'])) {
                $ids = array_merge($this->getIdsRoleRights($item['item']), $ids);
            }
        }
        return $ids;
    }
}
