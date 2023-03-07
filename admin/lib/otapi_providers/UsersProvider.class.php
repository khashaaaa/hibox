<?php

OTBase::import('system.lib.Cache');
OTBase::import('system.lib.cache.Key');
OTBase::import('system.lib.service.UserRecord');

class UsersProvider
{
    /**
     * @var OTAPIlib
     */
    private $otapilib;

    private $registry;

    public function __construct($otapilib)
    {
        $this->otapilib = $otapilib;
    }

    public function FindBaseUserInfoListFrame($sessionId, $userFilter, $framePosition = 0, $frameSize = 18)
    {
        $cacheKey = substr(md5(implode('/', array(__METHOD__, $sessionId, $userFilter, $framePosition, $frameSize))), 0, 10);
        if (! empty($this->registry[$cacheKey])) {
            return $this->registry[$cacheKey];
        }
        $result = $this->otapilib->FindBaseUserInfoListFrame($sessionId, $userFilter, $framePosition, $frameSize);
        $users = array();
        if (! empty($result)) {
            foreach ($result['content'] as $user) {
                $users[$user['id']] = new UserRecord($user);
            }
            $result['content'] = $users;
            $this->registry[$cacheKey] = $result;
        }
        return $result;
    }

    public function GetUserInfoForOperator($sessionId, $userId)
    {
        $cacher = new Cache(new Key('GetUserInfoForOperator', $sessionId, $userId));
        if (! $cacher->has()) {
            $result = new UserRecord($this->otapilib->GetUserInfoForOperator($sessionId, $userId));
            if (! $result->isEmpty()) {
                $cacher->set($result);
            }
        }
        return $cacher->get();
    }
    
    public function GetUserProfileInfoListForOperator($sessionId, $userId, $predefinedData = "")
    {
        $countries = $this->otapilib->GetCountryInfoList();
        $clist = array();
        if ($countries) {
            foreach ($countries as $key => $country) {
                $clist[$country['id']] = $country['name'];
            }
        }
        $profiles = $this->otapilib->GetUserProfileInfoListForOperator($sessionId, $userId);
        if ($profiles) {
            foreach ($profiles as $key => &$profile) {
                if (isset($profile['countrycode']) && ! empty($profile['countrycode'])) {
                    $profile['country'] = $clist[$profile['countrycode']];
                }
            }
        }
        return $profiles;
    }

    public function GetAccountInfoForOperator($sessionId, $userId)
    {
        return $this->otapilib->GetAccountInfoForOperator($sessionId, $userId);
    }

    public function GetStatementForOperator($sessionId, $customerId, $fromDate = '', $toDate = '')
    {
        return $this->otapilib->GetStatementForOperator($sessionId, $customerId, $fromDate, $toDate);
    }

    public function EditUser($sessionId, $userId, $userParameters)
    {
        $cacher = new Cache(new Key('GetUserInfoForOperator', $sessionId, $userId));
        $cacher->drop();
        return $this->otapilib->EditUser($sessionId, $userParameters);
    }

    public function CreateUserProfileForOperator($sessionId, $userId, $userParameters)
    {
        return $this->otapilib->CreateUserProfileForOperator($sessionId, $userId, $userParameters);
    }

    public function AddUser($sessionId, $userParameters)
    {
        return $this->otapilib->AddUser($sessionId, $userParameters);
    }

    public function SetUserBan($sessionId, $userId, $isBanned)
    {
        $cacher = new Cache(new Key('GetUserInfoForOperator', $sessionId, $userId));
        $cacher->drop();
        return $this->otapilib->SetUserBan($sessionId, $userId, $isBanned);
    }

    public function VerifyUserEmail($sessionId, $userId)
    {
        list($user, $sid, $lang) = $this->GetVerifyUserData($sessionId, $userId);
        $code = $user['EmailConfirmationCode'];

        return $this->otapilib->ConfirmEmail($sid, $code);
    }

    public function VerifyUserPhone($sessionId, $userId)
    {
        list($user, $sid, $lang) = $this->GetVerifyUserData($sessionId, $userId);
        $code = $user['PhoneConfirmationCode'];

        OTAPILib2::ConfirmPhone($lang, $sid, $code, $response);
        OTAPILib2::makeRequests();

        return $response;
    }

    private function GetVerifyUserData($sessionId, $userId)
    {
        $user = $this->otapilib->GetUserInfoForOperator($sessionId, $userId);
        $cacher = new Cache(new Key('GetUserInfoForOperator', $sessionId, $userId));
        $cacher->drop();

        $sid = User::getObject()->getSid();
        $lang = Session::getActiveAdminLang();

        return [$user, $sid, $lang];
    }

    public function DeleteUser($sessionId, $userId)
    {
        $cacher = new Cache(new Key('GetUserInfoForOperator', $sessionId, $userId));
        $cacher->drop();
        return $this->otapilib->DeleteUser($sessionId, $userId);
    }

    public function AuthenticateAsUser($sessionId, $userLogin)
    {
        return $this->otapilib->AuthenticateAsUser($sessionId, $userLogin);
    }

    public function getError()
    {
        return $this->otapilib->error_message;
    }

    public function getUsersIdsByPhone($phone, $from, $perpage)
    {
        $xmlParams = new SimpleXMLElement('<UserFilterParameters></UserFilterParameters>');
        $xmlParams->addChild('Phone', htmlspecialchars($phone));
        $filters = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());

        $users = $this->otapilib->FindBaseUserInfoListFrame(Session::get('sid'), $filters, $from, $perpage);

        $uids = array();
        if (is_array($users) && !empty($users['content'])) {
            foreach ($users['content'] as $user) {
                $uids[] = (string)$user['id'];
            }
        }
        return $uids;
    }

    public function getUsersByIds($userIds)
    {
        $userIds = is_array($userIds) ? $userIds : array($userIds);
        $xmlParams = new SimpleXMLElement('<UserFilterParameters></UserFilterParameters>');
        $xmlParams->addChild('IdList', implode(';', $userIds));
        $filters = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());

        $raw = $this->otapilib->FindBaseUserInfoListFrame(Session::get('sid'), $filters, 0, count($userIds) + 1);

        $users = array();
        if (is_array($raw) && !empty($raw['content'])) {
            foreach ($raw['content'] as $user) {
                $users[$user['id']] = new UserRecord($user);
            }
        }
        return $users;
    }

    public function getUsersByFilters($filter, $from, $perpage)
    {
        $xmlParams = new SimpleXMLElement('<UserFilterParameters></UserFilterParameters>');
        if (! empty($filter['phone'])) {
            $xmlParams->addChild('Phone', htmlspecialchars($filter['phone']));
        }
        if (! empty($filter['email'])) {
            $xmlParams->addChild('Email', htmlspecialchars($filter['email']));
        }
        $filter = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
        $users = $this->FindBaseUserInfoListFrame(Session::get('sid'), $filter, $from, $perpage);

        $uids = array();
        if (is_array($users) && !empty($users['content'])) {
            foreach ($users['content'] as $user) {
                $uids[] = (string)$user['id'];
            }
        }
        return $uids;
    }
}