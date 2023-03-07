<?php

class RolesProvider
{
    /**
     * @var OTAPIlib
     */
    private $otapilib;

    public function __construct($otapilib = null)
    {
        $this->otapilib = is_null($otapilib) ? new OTAPIlib() : $otapilib;
        $this->otapilib->setUseAdminLangOn();
        $this->otapilib->setErrorsAsExceptionsOn();
    }

    public function GetInstanceUserRoleList($sessionId)
    {
        return $this->otapilib->GetInstanceUserRoleList($sessionId);
    }

    public function GetAvailableRoleList($sessionId)
    {
    	$this->otapilib->setUseAdminLangOn();
    	$result = $this->otapilib->GetAvailableRoleList($sessionId);
    	$this->otapilib->setUseAdminLangOff();
    	return $result;
    }

    public function GetTemplateRoleList($sessionId)
    {
		$this->otapilib->setUseAdminLangOn();
        $result = $this->otapilib->GetTemplateRoleList($sessionId);
        $this->otapilib->setUseAdminLangOff();
        return $result;
    }

    public function AttachRightsToRole($sessionId, $roleName, $xmlIds)
    {
        return $this->otapilib->AttachRightsToRole($sessionId, $roleName, $xmlIds);
    }

    public function DeattachRightsFromRole($sessionId, $roleName, $xmlIds)
    {
        return $this->otapilib->DeattachRightsFromRole($sessionId, $roleName, $xmlIds);
    }

    public function CreateInstanceRoleFromTemplate($sessionId, $templateRoleName)
    {
        return $this->otapilib->CreateInstanceRoleFromTemplate($sessionId, $templateRoleName);
    }

    public function CreateInstanceRole($sessionId, $xml)
    {
        return $this->otapilib->CreateInstanceRole($sessionId, $xml);
    }

    public function DeleteInstanceRole($sessionId, $roleName)
    {
        return $this->otapilib->DeleteInstanceRole($sessionId, $roleName);
    }

    public function GetRightTree($sessionId, $roleName, $isTemplate)
    {
    	$this->otapilib->setUseAdminLangOn();
    	$result = $this->otapilib->GetRightTree($sessionId, $roleName, $isTemplate);
    	$this->otapilib->setUseAdminLangOff();
    	return $result;
    }

    public function GetOperatorRightTree($sessionId)
    {
        return $this->otapilib->GetOperatorRightTree($sessionId);
    }

    public function setUserRoleAndRights($sessionId)
    {
        /** Установка роли авторизованного пользователя */
        $rolesList = $this->GetInstanceUserRoleList($sessionId);
        RightsManager::setCurrentRole($rolesList);

        /** Установка прав авторизованного пользователя */
        $rights = $this->GetOperatorRightTree($sessionId);
        RightsManager::setCurrentRights($this->getRightsList($rights));
    }

    private function getRightsList($rights)
    {
        $rightsList = array();
        foreach ($rights as $item) {
            if ($item['isturnon'] == 'true') {
                $rightsList[] = $item['name'];
                if (isset($item['item'])) {
                    $rightsList = array_merge($this->getRightsList($item['item']), $rightsList);
                }
            }
        }
        return $rightsList;
    }
}