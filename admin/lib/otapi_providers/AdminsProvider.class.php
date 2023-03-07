<?php

class AdminsProvider {
    /**
     * @var OTAPIlib
     */
    private $otapilib;

    public function __construct()
	{
        $this->otapilib = new OTAPIlib();
        $this->otapilib->setUseAdminLangOn();
        $this->otapilib->setErrorsAsExceptionsOn();
    }

    public function GetInstanceUserList ($sid) 
    {
        $result = $this->otapilib->GetInstanceUserList($sid);
        return $result;
    }

    public function GetInstanceUserByLogin ($sid, $login) 
    {
        foreach ($this->otapilib->GetInstanceUserList($sid) as $user) {
            if ($user['Login'] == $login) {
                return $user;
            }
        }
        return false;
    }

    public function DeleteInstanceUser ($sid, $userId) 
    {
        $result = $this->otapilib->DeleteInstanceUser($sid, $userId);
        return $result;
    }

    public function CreateInstanceUser ($sid, $fields)  
    {
        $result = $this->otapilib->CreateInstanceUser($sid, $fields);
        return $result;
    }

    public function UpdateInstanceUser ($sid, $fields)  
    {
        $result = $this->otapilib->UpdateInstanceUser($sid, $fields);
        return $result;
    }

    public function AddInstanceUserToRole ($sid, $roleName, $userLogin)  
    {
        $result = $this->otapilib->AddInstanceUserToRole($sid, $roleName, $userLogin);
        return $result;
    }

    public function RemoveInstanceUserFromRole ($sid, $roleName, $userLogin)  
    {
        $result = $this->otapilib->RemoveInstanceUserFromRole($sid, $roleName, $userLogin);
        return $result;
    }
}