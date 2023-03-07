<?php

OTBase::import('system.model.data_mappers.SubscriberMapper');
OTBase::import('system.model.data_mappers.NewsletterMailMapper');
OTBase::import('system.model.data_mappers.NewsletterMapper');

ini_set('memory_limit', '512M');

class NewslettersSender
{
    /**
     * @var NewsletterMapper
     */
    protected $newsletterMapper;

    /**
     * @var SubscriberMapper
     */
    protected $subscriberMapper;

    /**
     * @var NewsletterMailMapper
     */
    protected $newsletterMailMapper;

    public function __construct()
    {
        $cms = new CMS();
        $this->newsletterMapper = new NewsletterMapper($cms);
        $this->subscriberMapper = new SubscriberMapper($cms);
        $this->newsletterMailMapper = new NewsletterMailMapper($cms);
    }

    public function sendQueue($limit, $fromEmail, $fromName)
    {
        /** Проверяем, закончилась ли ежедневная квота на отправку писем по рассылке*/
        if ($this->newsletterMapper->isEndedDayQuota()) {
            return false;
        }

        try {
            $lastSentTime = $this->newsletterMailMapper->findLastSentTime();
            $now = new DateTime();

            if (! OTBase::isTest()) {
                $interval = defined('CFG_NEWSLETTER_INTERVAL_SEND_QUEUE') ? CFG_NEWSLETTER_INTERVAL_SEND_QUEUE : 120; // sek
                if ($now->getTimestamp() - $lastSentTime->getTimestamp() < $interval) {
                    return false;
                }
            }

            $newsletter = $this->newsletterMapper->findFirstNewsletterToSend();
            if (is_null($newsletter)) {
                return false;
            }

            /**
             * @var NewsletterMailEntity[] $mails
             */
            $mails = $this->newsletterMailMapper->findMailsToSent($newsletter->getId(), $limit);
            $this->lockMails($mails);

            foreach ($mails as $mail) {
                $subscriber = $this->subscriberMapper->findById($mail->getSubscriberId());
                $mailBody = NewsletterEntity::prepareText($newsletter->getText(), $subscriber->getEmail());
                try {
                    General::mail_utf8($subscriber->getEmail(), $fromName, $fromEmail, $newsletter->getTitle(), $mailBody, false, array('useException' => true));
                    $mail->setStatus('OK');
                } catch (Exception $e) {
                    $mail->setStatus('ERROR: ' . $e->getMessage());
                }
                $mail->setSent(new DateTime());
                $this->newsletterMailMapper->save($mail);
            }

            // check newsletter completed
            $mails = $this->newsletterMailMapper->findMailsToSent($newsletter->getId(), $limit);
            if (count($mails) == 0) {
                $newsletter->setCompleted(true);
                $this->newsletterMapper->save($newsletter);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param NewsletterMailEntity[] $mails
     */
    private function lockMails($mails)
    {
        $ids = array();
        foreach ($mails as $mail) {
            $ids[] = $mail->getId();
        }

        $this->newsletterMailMapper->lockMails($ids);
    }
}