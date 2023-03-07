<?php

OTBase::import('system.model.data_mappers.SubscriberMapper');
OTBase::import('system.admin.controllers.*');
OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');
OTBase::import('system.uploader.php.UploadHandler');

class Subscribers extends GeneralUtil
{
    const PseudoUserId = -1;

    protected $_template = 'list';
    protected $_template_path = 'newsletters/subscribers/';

    public function __construct()
    {
        parent::__construct();
        $this->dataMappper = new SubscriberMapper($this->cms);
        $this->entitiesList = new EntitiesList($this->dataMappper);
    }

    /**
     * @param RequestWrapper $request
     */
    public function defaultAction($request)
    {
        $filter = $request->getValue('filter') ? $request->getValue('filter') : array();

        $subscribersPaginated = $this->entitiesList->getPaginatedList($request->getValue('page'), $request->getValue('perPage'), $filter);

        $this->tpl->assign('filter', $filter);
        $this->tpl->assign('subscribers', $subscribersPaginated['content']);
        $this->tpl->assign('totalCount', $subscribersPaginated['totalCount']);
        $this->tpl->assign('perPage', $subscribersPaginated['perPage']);
        $this->tpl->assign('paginator', new Paginator($subscribersPaginated['totalCount'], $subscribersPaginated['page'], $subscribersPaginated['perPage']));
        $this->tpl->assign('PseudoUserId', self::PseudoUserId);

        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     * @throws Exception
     */
    public function addAction($request)
    {
        $login = $request->getValue('value');
        $id = $request->getValue('pk');

        try {
            $users = $this->dataMappper->findByLogin($login);
            if (count($users)) {
                throw new Exception(LangAdmin::get('User_already_exists') . ' (' . htmlspecialchars($login) . ')');
            }

            OTAPILib2::GetUserInfoForOperator(Session::get('sid'), $id, $user);
            OTAPILib2::makeRequests();

            /**
             * @var OtapiUserInfoAnswer $user
             */
            if ($user->GetUserInfo()->GetLogin() != $login) {
                throw new Exception(LangAdmin::get('Login_and_id_are_mismatch'));
            }
            $userInfo = $user->GetUserInfo();

            $S = new SubscriberEntity();
            $S->setLogin($login);
            $S->setOtapiId($userInfo->GetId()->asString());
            $S->setRegistered($userInfo->GetRegistrationDate());
            $S->setSex($userInfo->GetSex() == 'Male' ? SubscriberEntity::MALE : SubscriberEntity::FEMALE);
            $S->setEmail($userInfo->GetEmail());
            $S->setSkype($userInfo->GetSkype());
            $S->setName($userInfo->GetFirstName() ? $userInfo->GetFirstName() : $userInfo->GetRecipientFirstName());
            $S->setSurname($userInfo->GetLastName() ? $userInfo->GetLastName() : $userInfo->GetRecipientLastName());
            $S->setMiddleName($userInfo->GetMiddleName() ? $userInfo->GetMiddleName() : $userInfo->GetRecipientMiddleName());

            $this->dataMappper->save($S);
        } catch (ServiceException $e) {
            if ((string)$e->getErrorCode() == 'NotFound') {
                $this->respondAjaxError(new Exception(LangAdmin::get('User_not_exists') . ' (' . htmlspecialchars($login) . ')'));
            } else {
                $this->respondAjaxError((string)$e->getErrorMessage());
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }

    /**
     * @param RequestWrapper $request
     * @throws Exception
     */
    public function addSubscriberAction($request)
    {
        $subscriberName = $request->getValue('subscriberName');
        $subscriberMail = $request->getValue('subscriberMail');
        try {
            $validator = new Validator(array(
                'subscriberName' => $subscriberName,
                'subscriberMail' => $subscriberMail
            ));
            $validator->addRule(new NotEmptyString(), 'subscriberName', LangAdmin::get('Name_of_subscriber_cannot_be_empty'));
            $validator->addRule(new NotEmptyString(), 'subscriberMail', LangAdmin::get('Mail_of_subscriber_cannot_be_empty'));
            if (!$validator->validate()) {
                $errors = $validator->getLastError();
                throw new Exception((string)$errors);
            }
            $S = new SubscriberEntity();
            $S->setLogin($subscriberName);
            $S->setOtapiId(self::PseudoUserId);
            $S->setEmail($subscriberMail);
            $S->setName($subscriberName);
            $S->setSurname('');
            $S->setMiddleName('');
            $this->dataMappper->save($S);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse();
    }

    /**
     * @param RequestWrapper $request
     */
    public function deleteAction($request)
    {
        try {
            $this->dataMappper->remove($request->getValue('id'));
            $this->redirect($request->env('HTTP_REFERER'));
        } catch (Exception $e) {
            Session::setError($e->getMessage());
            $this->redirect($request->env('HTTP_REFERER'));
        }
    }

    /**
     * @param RequestWrapper $request
     */
    public function searchUserInOtapiAction($request)
    {
        OTAPILib2::FindBaseUserInfoListFrame(Session::get('sid'),
            '<UserFilterParameters><Login>' . htmlspecialchars($request->getValue('query')) . '</Login></UserFilterParameters>',
            0, 20, $users);
        OTAPILib2::makeRequests();

        /**
         * @var OtapiBaseUserInfoListFrameAnswer $users
         */
        $usersJson = array();
        foreach ($users->GetResult()->GetContent()->GetItem() as $item) {
            $usersJson[] = array(
                'name' => $this->escape($item->GetLogin()),
                'id' => $item->GetId()->asString()
            );
        }
        $this->sendAjaxResponse($usersJson, false, true);
    }

    /**
     * @param RequestWrapper $request
     */
    public function exportAction($request)
    {
        /**
         * @var SubscriberEntity[] $subscribers
         */
        $subscribers = $this->dataMappper->findAll();

        $xml = new SimpleXMLElement('<Users/>');
        foreach ($subscribers as $subscriber) {
            $el = $xml->addChild('User');
            $el->addChild('OtapiId', htmlspecialchars($subscriber->getOtapiId()));
            $el->addChild('Login', htmlspecialchars($subscriber->getLogin()));
            $el->addChild('Email', htmlspecialchars($subscriber->getEmail()));
            $el->addChild('FirstName', htmlspecialchars($subscriber->getName()));
            $el->addChild('LastName', htmlspecialchars($subscriber->getSurname()));
            $el->addChild('MiddleName', htmlspecialchars($subscriber->getMiddleName()));
            $el->addChild('Skype', htmlspecialchars($subscriber->getSkype()));
            $el->addChild('Registered', htmlspecialchars($subscriber->getRegisteredForSQL()));
            $el->addChild('Subscribed', htmlspecialchars($subscriber->getSubscribedForSQL()));
        }

        header("Content-Type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Content-Disposition: attachment; filename=subscribers.xml");
        print $xml->asXML();
    }

    public function importAction()
    {
        ob_start();
        new UploadHandler(array(
            'accept_file_types' => '/\.xml$/i',
            'param_name' => 'subscribers_file',
        ), true, null, '/cache/');
        $result = ob_get_contents();
        ob_end_clean();

        $resultInfo = json_decode($result);
        $fileUploadResult = $resultInfo->subscribers_file[0];

        if (isset($fileUploadResult->error) && !empty($fileUploadResult->error)) {
            $this->respondAjaxError(new Exception($fileUploadResult->error));
        }

        $this->importSubscribersFromXML(simplexml_load_file(CFG_APP_ROOT . '/cache/' . $fileUploadResult->name));
    }

    /**
     * @param SimpleXMLElement $xml
     */
    private function importSubscribersFromXML($xml)
    {
        $subscribers = $this->dataMappper->findAllEmails();

        foreach ($xml->User as $user) {
            if (!in_array($user->Email, $subscribers)) {
                $S = new SubscriberEntity();
                $S->setOtapiId((string)$user->OtapiId);
                $S->setLogin((string)$user->Login);
                $S->setEmail((string)$user->Email);
                $S->setName((string)$user->FirstName);
                $S->setSurname((string)$user->LastName);
                $S->setMiddleName((string)$user->MiddleName);

                try {
                    $this->dataMappper->save($S);
                } catch (Exception $e) {

                }
            }
        }
    }
}