<?php

OTBase::import('system.model.entities.Entity');

class SubscriberEntity extends Entity {
    const MALE = 1;
    const FEMALE = 0;

    /**
     * @var int
     */
    protected $otapiId;

    /**
     * @var string
     */
    protected $login;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $surname;

    /**
     * @var string
     */
    protected $middleName;

    /**
     * @var int
     */
    protected $sex;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $skype;

    /**
     * @var DateTime
     */
    protected $subscribed;

    /**
     * @var DateTime
     */
    protected $registered;

    /**
     * @var string
     */
    protected $error;

    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @param string $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @param int $otapiId
     */
    public function setOtapiId($otapiId)
    {
        $this->otapiId = $otapiId;
    }

    /**
     * @return int
     */
    public function getOtapiId()
    {
        return $this->otapiId;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \DateTime $registered
     */
    public function setRegistered($registered)
    {
        if ($registered instanceof DateTime) {
            $this->registered = $registered;
        } else {
            $this->registered = new DateTime($registered);
        }
    }

    /**
     * @return \DateTime
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * @return string
     */
    public function getRegisteredForSQL()
    {
        return $this->registered ? $this->registered->format('Y-m-d H:i:s') : '';
    }

    /**
     * @param int $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
    }

    /**
     * @return int
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param string $skype
     */
    public function setSkype($skype)
    {
        $this->skype = $skype;
    }

    /**
     * @return string
     */
    public function getSkype()
    {
        return $this->skype;
    }

    /**
     * @param \DateTime $subscribed
     */
    public function setSubscribed($subscribed)
    {
        if ($subscribed instanceof DateTime) {
            $this->subscribed = $subscribed;
        } else {
            $this->subscribed = new DateTime($subscribed);
        }
    }

    /**
     * @return string
     */
    public function getSubscribedForSQL()
    {
        return $this->subscribed->format('Y-m-d H:i:s');
    }

    /**
     * @return \DateTime
     */
    public function getSubscribed()
    {
        return $this->subscribed;
    }

    /**
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }
}