<?php

OTBase::import('system.model.entities.SubscriberEntity');
OTBase::import('system.model.data_mappers.Mapper');
OTBase::import('system.lib.Lang');
OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');
OTBase::import('system.lib.exceptions.*');

class SubscriberMapper extends Mapper
{

    protected $tableName = 'newsletters_subscribers';

    protected $entityType = 'SubscriberEntity';

    protected $defaultSort = 'subscribed DESC';

    protected $columnsMap = array(
        'id' => 'id',
        'otapiId' => 'otapiId',
        'login' => 'login',
        'name' => 'name',
        'surname' => 'surname',
        'middleName' => 'middleName',
        'sex' => 'sex',
        'email' => 'email',
        'skype' => 'skype',
        'registered' => 'registered',
        'subscribed' => 'subscribed',
    );

    /**
     * @param SubscriberEntity $entity
     */
    protected function createNew($entity)
    {
        $this->validateNewSubscriber($entity);

        $otapiId = intval($entity->getOtapiId());
        $login = $this->cms->escape($entity->getLogin());
        $name = $this->cms->escape($entity->getName());
        $surname = $this->cms->escape($entity->getSurname());
        $middleName = $this->cms->escape($entity->getMiddleName());
        $sex = $this->cms->escape($entity->getSex());
        $email = $this->cms->escape($entity->getEmail());
        $skype = $this->cms->escape($entity->getSkype());
        $registered = $this->cms->escape($entity->getRegisteredForSQL());

        $this->cms->query("
                INSERT INTO {$this->tableName} SET
                    otapiId='$otapiId',
                    login='$login',
                    name='$name',
                    surname='$surname',
                    middleName='$middleName',
                    sex='$sex',
                    email='$email',
                    skype='$skype',
                    registered='$registered',
                    subscribed=NOW()
                ", array($this->tableName));
    }

    /**
     * @param SubscriberEntity $entity
     * @throws EntityValidationException
     */
    private function validateNewSubscriber($entity)
    {
        $validator = new Validator(array(
            'otapiId' => $entity->getOtapiId(),
            'login' => $entity->getLogin(),
            'name' => $entity->getName(),
            'surname' => $entity->getSurname(),
            'middleName' => $entity->getMiddleName(),
            'sex' => $entity->getSex(),
            'email' => $entity->getEmail(),
            'skype' => $entity->getSkype(),
            'registered' => $entity->getRegistered(),
            'subscribed' => $entity->getSubscribed(),
        ));
        $validator->addRule(new Number(), 'otapiId', Lang::get('Subscriber_otapi_id_not_filled'));
        $validator->addRule(new NotEmpty(), 'login', Lang::get('Subscriber_login_not_filled'));
        $validator->addRule(new Email(), 'email', Lang::get('Subscriber_email_not_filled'));

        if (!$validator->validate()) {
            $errors = $validator->getLastError();
            throw new EntityValidationException((string)$errors);
        }
    }

    /**
     * @param SubscriberEntity $entity
     */
    protected function update($entity)
    {
        $this->validateExistedSubscriber($entity);

        $id = intval($entity->getId());
        $otapiId = intval($entity->getOtapiId());
        $login = $this->cms->escape($entity->getLogin());
        $name = $this->cms->escape($entity->getName());
        $surname = $this->cms->escape($entity->getSurname());
        $middleName = $this->cms->escape($entity->getMiddleName());
        $sex = $this->cms->escape($entity->getSex());
        $email = $this->cms->escape($entity->getEmail());
        $skype = $this->cms->escape($entity->getSkype());
        $registered = $this->cms->escape($entity->getRegisteredForSQL());
        $subscribed = $this->cms->escape($entity->getSubscribedForSQL());

        $this->cms->query("
                UPDATE {$this->tableName} SET
                    otapiId='$otapiId',
                    login='$login',
                    name='$name',
                    surname='$surname',
                    middleName='$middleName',
                    sex='$sex',
                    email='$email',
                    skype='$skype',
                    registered='$registered',
                    subscribed='$subscribed'
                WHERE
                    id=$id
                ", array($this->tableName));
    }

    /**
     * @param SubscriberEntity $entity
     * @throws EntityValidationException
     */
    private function validateExistedSubscriber($entity)
    {
        $validator = new Validator(array(
            'otapiId' => $entity->getOtapiId(),
            'login' => $entity->getLogin(),
            'name' => $entity->getName(),
            'surname' => $entity->getSurname(),
            'middleName' => $entity->getMiddleName(),
            'sex' => $entity->getSex(),
            'email' => $entity->getEmail(),
            'skype' => $entity->getSkype(),
            'registered' => $entity->getRegistered(),
            'subscribed' => $entity->getSubscribed(),
        ));
        $validator->addRule(new Number(), 'otapiId', Lang::get('Subscriber_otapi_id_not_filled'));
        $validator->addRule(new NotEmpty(), 'login', Lang::get('Subscriber_login_not_filled'));
        $validator->addRule(new Number(SubscriberEntity::FEMALE, SubscriberEntity::MALE), 'sex', Lang::get('Subscriber_sex_not_filled'));
        $validator->addRule(new Email(), 'email', Lang::get('Subscriber_email_not_filled'));
        $validator->addRule(new IsDateTime(), 'registered');
        $validator->addRule(new IsDateTime(), 'subscribed');

        if (!$validator->validate()) {
            $errors = $validator->getLastError();
            throw new EntityValidationException((string)$errors);
        }
    }

    public function findByName($name)
    {
        $name = $this->cms->escape($name);
        return $this->createEntitiesFromData($this->cms->queryMakeArray("
            SELECT * FROM {$this->tableName} WHERE name='$name'
        ", array($this->tableName)));
    }

    public function findByLogin($login)
    {
        $login = $this->cms->escape($login);
        return $this->createEntitiesFromData($this->cms->queryMakeArray("
            SELECT * FROM {$this->tableName} WHERE login='$login'
        ", array($this->tableName)));
    }

    public function findByOtapiId($id)
    {
        $id = $this->cms->escape($id);
        return $this->createEntitiesFromData($this->cms->queryMakeArray("
            SELECT * FROM {$this->tableName} WHERE otapiId='$id'
        ", array($this->tableName)));
    }

    public function findByEmail($email)
    {
        $email = $this->cms->escape($email);
        return $this->createEntitiesFromData($this->cms->queryMakeArray("
            SELECT * FROM {$this->tableName} WHERE email='$email'
        ", array($this->tableName)));
    }

    /**
     * @param $newsletterId
     * @param $limit
     * @return SubscriberEntity[]
     * @deprecated
     */
    public function findSubscribersForNewsletterSendWithError($newsletterId, $page, $perPage)
    {
        $newsletterId = intval($newsletterId);
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT n_m.subscriberId
                FROM newsletters_mails n_m
                WHERE n_m.newsletterId=$newsletterId AND n_m.status LIKE 'ERROR%'
                ORDER BY n_m.sent ASC";

        $total = count($this->cms->queryMakeArray($sql));

        $sql = "SELECT n_s.*, n_m.status as error FROM newsletters_subscribers n_s 
            INNER JOIN newsletters_mails n_m ON n_m.subscriberId=n_s.id
            WHERE n_m.newsletterId=$newsletterId AND n_m.status LIKE 'ERROR%' 
            ORDER BY n_m.sent ASC
            LIMIT {$offset},{$perPage}";

        $result = $this->cms->queryMakeArray($sql);

        $this->columnsMap['error'] = 'error';

        return array(
            'content' => $this->createEntitiesFromData($result),
            'totalCount' => $total,
            'page' => $page,
            'perPage' => $perPage == 'all' ? $total : $perPage
        );
    }

    public function findAllEmails()
    {
        $result = array();

        $data = $this->cms->queryMakeArray("
            SELECT email FROM {$this->tableName}", array($this->tableName));

        foreach ($data as $item) {
            $result[] = $item['email'];
        }

        return $result;
    }
}