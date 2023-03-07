<?php

OTBase::import('system.model.entities.Entity');

class NewsletterEntity extends Entity
{
    protected $title = null;

    protected $text = null;

    /**
     * @var bool
     */
    protected $isBeingSent = null;

    /**
     * @var DateTime
     */
    protected $created = null;

    /**
     * @var bool
     */
    protected $completed = null;

    /**
     * @param boolean $isBeingSent
     */
    public function setIsBeingSent($isBeingSent)
    {
        $this->isBeingSent = $isBeingSent;
    }

    /**
     * @return boolean
     */
    public function isBeingSent()
    {
        return $this->isBeingSent;
    }

    /**
     * @param bool $completed
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;
    }

    /**
     * @return bool
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated($created)
    {
        if ($created instanceof DateTime) {
            $this->created = $created;
        } else {
            $this->created = new DateTime($created);
        }
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return string
     */
    public function getCreatedForSQL()
    {
        return $this->created->format('Y-m-d H:i:s');
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    public static function prepareText($text, $email) {
        $url = UrlGenerator::generateContentUrl('privateoffice', true);
        $url .= CMS::IsFeatureEnabled('Seo2') ? '?subscribe=0' : '&subscribe=0';
        $url .= '&hash=' . base64_encode($email);
        $mailBody = str_replace("{{unsubscribeLink}}", $url, $text);
        return $mailBody;
    }


}