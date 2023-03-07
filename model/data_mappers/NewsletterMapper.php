<?php

OTBase::import('system.model.entities.NewsletterEntity');
OTBase::import('system.model.data_mappers.Mapper');
OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');

class NewsletterMapper extends Mapper
{
    protected $tableName = 'newsletters';

    protected $entityType = 'NewsletterEntity';

    protected $defaultSort = 'is_being_sent ASC, created DESC';

    protected $columnsMap = array(
        'id' => 'id',
        'title' => 'title',
        'content' => 'text',
        'created' => 'created',
        'completed' => 'completed',
        'is_being_sent' => 'isBeingSent',
    );

    /**
     * @param NewsletterEntity $newsletter
     */
    protected function createNew($newsletter)
    {
        $this->validateNewsletter($newsletter);
        $title = $this->cms->escape($newsletter->getTitle());
        $text = $this->cms->escape($newsletter->getText());
        $isSent = intval($newsletter->isBeingSent());
        $this->cms->query("
                INSERT INTO newsletters SET
                    title='$title',
                    content='$text',
                    created=NOW(),
                    is_being_sent='$isSent',
                    completed=0
                ", array('newsletters'));
    }

    /**
     * @param NewsletterEntity $newsletter
     */
    protected function update($newsletter)
    {
        $this->validateNewsletter($newsletter);
        $id = intval($newsletter->getId());
        $title = $this->cms->escape($newsletter->getTitle());
        $text = $this->cms->escape($newsletter->getText());
        $created = $newsletter->getCreatedForSQL();
        $completed = intval($newsletter->getCompleted());
        $isBeingSent = intval($newsletter->isBeingSent());
        $this->cms->query("
                UPDATE newsletters SET
                    title='$title',
                    content='$text',
                    created='$created',
                    is_being_sent='$isBeingSent',
                    completed='$completed'
                WHERE
                    id=$id
                ", array('newsletters'));
    }

    /**
     * @param NewsletterEntity $newsletter
     * @throws ValidationException
     */
    private function validateNewsletter($newsletter)
    {
        $validator = new Validator(array(
            'title' => $newsletter->getTitle(),
            'text' => $newsletter->getText()
        ));
        $validator->addRule(new NotEmpty(), 'title', LangAdmin::get('Newsletter_title_must_not_be_empty'));
        $validator->addRule(new NotEmpty(), 'text', LangAdmin::get('Newsletter_text_must_not_be_empty'));

        if (!$validator->validate()) {
            $errors = $validator->getLastError();
            throw new ValidationException((string)$errors);
        }
    }

    /**
     * @return NewsletterEntity[]
     */
    public function findActiveNewslettersOrderedByCreatedDesc()
    {
        $newsletters =  $this->cms->queryMakeArray("SELECT * FROM {$this->tableName} WHERE isBeingSent=1 ORDER BY created DESC");
        return $this->createEntitiesFromData($newsletters);
    }

    /**
     * @param $title
     * @return NewsletterEntity[]
     */
    public function findNewslettersByTitle($title)
    {
        $title = $this->cms->escape($title);
        $newsletters =  $this->cms->queryMakeArray("SELECT * FROM {$this->tableName} WHERE title='$title'");
        return $this->createEntitiesFromData($newsletters);
    }

    public function clearSentMailsWithError($newsletterId)
    {
        $newsletterId = intval($newsletterId);
        $sql = 'UPDATE `newsletters_mails` SET status="WAIT" WHERE newsletterId=' . $newsletterId . ' AND `status` LIKE "ERROR%"';
        return $this->cms->query($sql, array('newsletters_mails')
        );
    }

    public function isEndedDayQuota()
    {
        $dayQuota = General::getConfigValue('newsletter_day_quota', 100);
        $currentDate = date('Y-m-d');
        $dayCountMails = $this->cms->querySingleValue(
            "SELECT COUNT(id) FROM newsletters_mails WHERE DATE(sent)='$currentDate' AND status <> 'WAIT'");

        return ($dayCountMails >= $dayQuota) ? true : false;
    }

    /**
     * @return NewsletterEntity
     */
    public function findFirstNewsletterToSend()
    {
        $result = $this->cms->queryMakeArray("SELECT * FROM {$this->tableName} WHERE completed=0 and is_being_sent=1 ORDER BY created ASC LIMIT 1",
            array($this->tableName));
        if (!$result)
            return null;

        return $this->createEntityFromData($result[0]);
    }
}