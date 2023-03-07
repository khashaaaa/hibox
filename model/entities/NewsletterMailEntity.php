<?php

OTBase::import('system.model.entities.Entity');

class NewsletterMailEntity extends Entity {

    /**
     * @var int
     */
    protected $newsletterId;

    /**
     * @var int
     */
    protected $subscriberId;

    /**
     * @var DateTime
     */
    protected $sent;

    protected $status;

    /**
     * @param int $newsletterId
     */
    public function setNewsletterId($newsletterId)
    {
        $this->newsletterId = $newsletterId;
    }

    /**
     * @return int
     */
    public function getNewsletterId()
    {
        return $this->newsletterId;
    }

    /**
     * @param DateTime $sent
     */
    public function setSent($sent)
    {
        if ($sent instanceof DateTime) {
            $this->sent = $sent;
        } else {
            $this->sent = new DateTime($sent);
        }
    }

    /**
     * @return DateTime
     */
    public function getSent()
    {
        return $this->sent;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param int $subscriberId
     */
    public function setSubscriberId($subscriberId)
    {
        $this->subscriberId = $subscriberId;
    }

    /**
     * @return int
     */
    public function getSubscriberId()
    {
        return $this->subscriberId;
    }

}