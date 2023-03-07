<?php

OTBase::import('system.model.data_mappers.SubscriberMapper');
OTBase::import('system.model.entities.SubscriberEntity');

class Subscribe extends GenerateBlock {

    protected $_cache = false;
    protected $_life_time = 600;
    protected $_template = 'subscribe';
    protected $_template_path = '/privateoffice/';
    protected $_hash = '';

    public function __construct() {
        parent::__construct(true);
        $this->dataMapper = new SubscriberMapper(new CMS());
    }

    protected function setVars() {
        if (isset($_GET['subscribe'])) {
            if ($_GET['subscribe'])
                self::SetSubscribe();
            else
                $this->UnsetSubscribe();
        }
        global $otapilib;
        $sid = User::getObject()->getSid();
        if (isset($GLOBALS['$otapilib->GetUserInfo'])) {
            $userData = $GLOBALS['$otapilib->GetUserInfo'];
        } else {
            $userData = $otapilib->GetUserInfo($sid);
            $GLOBALS['$otapilib->GetUserInfo'] = $userData;
        }
        $this->tpl->assign('subscribed', $this->GetUserSubscribe($userData['Email']));
    }

    private function GetUserSubscribe($email) {
        $found = $this->dataMapper->findByEmail($email);
        return count($found);
    }

    public static function SetSubscribe($email = '', $login = '', $userId = 0) {
        try {
            $S = new SubscriberEntity();
            if (!empty($email) && !empty($login) && $userId != 0) {
                // подписка переданного пользователя
                $S->setLogin($login);
                $S->setOtapiId($userId);
                $S->setEmail($email);

                $S->setSex(SubscriberEntity::FEMALE);
                $S->setName('');
                $S->setSurname('');
                $S->setRegistered(new DateTime());

            } else {
                // подписка текущего пользователя
                /**
                 * @var OtapiUserInfoAnswer $user
                 */
                OTAPILib2::GetUserInfo(Session::getUserSession(), $user);
                OTAPILib2::makeRequests();
                $userInfo = $user->GetUserInfo();

                $S->setLogin($userInfo->GetLogin());
                $S->setOtapiId($userInfo->GetId()->asString());
                $S->setRegistered($userInfo->GetRegistrationDate());
                $S->setSex($userInfo->GetSex() == 'Male' ? SubscriberEntity::MALE : SubscriberEntity::FEMALE);
                $S->setEmail($userInfo->GetEmail());
                $S->setSkype($userInfo->GetSkype());
                $S->setName($userInfo->GetFirstName() ? $userInfo->GetFirstName() : $userInfo->GetRecipientFirstName());
                $S->setSurname($userInfo->GetLastName() ? $userInfo->GetLastName() : $userInfo->GetRecipientLastName());
                $S->setMiddleName($userInfo->GetMiddleName() ? $userInfo->GetMiddleName() : $userInfo->GetRecipientMiddleName());
            }
            $dataMapper = new SubscriberMapper(new CMS());
            $dataMapper->save($S);
        } catch (Exception $e) {}
    }

    private function UnsetSubscribe() {
        if (RequestWrapper::get('hash', false)) {
            $email = base64_decode(RequestWrapper::get('hash'));
            $found = $this->dataMapper->findByEmail($email);
        } else {
            $found = $this->dataMapper->findByLogin(Session::getUserDataByKey('username'));
        }
        if ($found) {
            /**
             * @var SubscriberEntity $user
             */
            $user = $found[0];
            $this->dataMapper->remove($user->getId());
        }
        if (RequestWrapper::get('hash', false)) {
            header('Location: ' . UrlGenerator::generateContentUrl('unsubscription'));
        } else {
            header('Location: ' . UrlGenerator::generatePrivateOfficeUrl());
        }        
    }

}
