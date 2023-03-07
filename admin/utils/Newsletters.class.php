<?php

OTBase::import('system.model.data_mappers.NewsletterMapper');
OTBase::import('system.model.data_mappers.SubscriberMapper');
OTBase::import('system.model.data_mappers.NewsletterMailMapper');
OTBase::import('system.admin.controllers.*');

// НЕ УБИРАТЬ
// Это надо для супер-рассылок агентов, которые вставляют картинки base64-encoded
ini_set('memory_limit', '512M');

class Newsletters extends GeneralUtil {
    protected $_template = 'list';
    protected $_template_path = 'newsletters/';

    private $newsletterMapper;
    private $subscriberMapper;
    private $newsletterMailMapper;

    public function __construct()
    {
        parent::__construct();
        $this->newsletterMapper = new NewsletterMapper($this->cms);
        $this->subscriberMapper = new SubscriberMapper($this->cms);
        $this->newsletterMailMapper = new NewsletterMailMapper($this->cms);
        $this->entitiesList = new EntitiesList($this->newsletterMapper);
    }

    /**
     * @param RequestWrapper $request
     */
    public function defaultAction($request)
    {

        $newslettersPaginated = $this->entitiesList->getPaginatedList($request->getValue('page'), $request->getValue('perPage'));

        $this->tpl->assign('newsletterMapper', $this->newsletterMapper);
        $this->tpl->assign('newsletterMailMapper', $this->newsletterMailMapper);
        $this->tpl->assign('newsletters', $newslettersPaginated['content']);
        $this->tpl->assign('totalCount', $newslettersPaginated['totalCount']);
        $this->tpl->assign('perPage', $newslettersPaginated['perPage']);
        $this->tpl->assign('paginator', new Paginator($newslettersPaginated['totalCount'], $newslettersPaginated['page'], $newslettersPaginated['perPage']));

        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function addAction($request)
    {
        $this->_template = 'edit_form';
        $newsletter = new NewsletterEntity();
        $this->tpl->assign('newsletter', $newsletter);
        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function reportAction($request)
    {
        $this->_template = 'report';
        $perPage = $request->getValue('perPage') ? $request->getValue('perPage') : 10;
        $page = intval($request->getValue('page')) ? intval($request->getValue('page')) : 1;
        $newsletterId = $request->getValue('id');

        $newsletter = $this->newsletterMapper->findById($newsletterId);

        $subscriberMapper = new SubscriberMapper($this->cms);
        $subscriberWithErrorPaginated  = $subscriberMapper->findSubscribersForNewsletterSendWithError($newsletterId, $page, $perPage);

        $newslettersPaginated = $this->entitiesList->getPaginatedList($request->getValue('page'), $request->getValue('perPage'));

        $this->tpl->assign('subscribers', $subscriberWithErrorPaginated['content']);
        $this->tpl->assign('totalCount', $subscriberWithErrorPaginated['totalCount']);
        $this->tpl->assign('perPage', $perPage);
        $this->tpl->assign('paginator', new Paginator($subscriberWithErrorPaginated['totalCount'], $newslettersPaginated['page'], $newslettersPaginated['perPage']));


        $this->tpl->assign('newsletter', $newsletter);
        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function editAction($request)
    {
        $this->_template = 'edit_form';
        $newsletter = $this->newsletterMapper->findById($request->getValue('id'));
        $this->tpl->assign('newsletter', $newsletter);
        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function stopAction($request)
    {
        try {
            $newsletter = $this->newsletterMapper->findById($request->getValue('id')); 
            $newsletter->setCompleted(true);
            $this->newsletterMapper->save($newsletter);
            $this->redirect('index.php?cmd=Newsletters');
        } catch (Exception $e) {
            Session::setError($e->getMessage());
            $this->redirect($request->env('HTTP_REFERER'));
        }
    }

    /**
     * @param RequestWrapper $request
     */
    public function restartAction($request)
    {
        try {
            $newsletterId = $request->getValue('id');
            
            $newsletter = $this->newsletterMapper->findById($newsletterId); 
            $newsletter->setCompleted(false);
            $this->newsletterMapper->save($newsletter);

            $this->newsletterMapper->clearSentMailsWithError($newsletterId);

            $this->redirect('index.php?cmd=Newsletters');
        } catch (Exception $e) {
            Session::setError($e->getMessage());
            $this->redirect($request->env('HTTP_REFERER'));
        }
    }

    /**
     * @param RequestWrapper $request
     */
    public function startAction($request)
    {
        try {
            $newsletter = $this->newsletterMapper->findById($request->getValue('id'));
            $newsletter->setIsBeingSent(true);
            $newsletter->setCompleted(false);
            $this->newsletterMapper->save($newsletter);

            /* Построим очередь отправки */
            $this->buildMailQueue($newsletter);

            $this->redirect('index.php?cmd=Newsletters');
        } catch (Exception $e) {
            Session::setError($e->getMessage());
            $this->redirect($request->env('HTTP_REFERER'));
        }
    }


    private function buildMailQueue(NewsletterEntity $newsletter)
    {
        $subscribers = $this->subscriberMapper->findAll();
        $this->newsletterMailMapper->initMails($newsletter->getId(), $subscribers);
    }

    /**
     * @param RequestWrapper $request
     */
    public function deleteAction($request)
    {
        try {
            $this->newsletterMapper->remove($request->getValue('id'));
            $this->redirect('index.php?cmd=Newsletters');
        } catch (Exception $e) {
            Session::setError($e->getMessage());
            $this->redirect($request->env('HTTP_REFERER'));
        }
    }

    /**
     * @param RequestWrapper $request
     * @throws Exception
     */
    public function saveAction($request)
    {
        try {
            if ($request->getValue('id')) {
                $newsletter = $this->newsletterMapper->findById($request->getValue('id'));
            } else {
                $newsletter = new NewsletterEntity();
            }

            $newsletter->setTitle($request->getValue('title'));
            $newsletter->setText($request->getValue('text'));
            if ($request->getValue('send')) {
                $newsletter->setIsBeingSent(true);
                $newsletter->setCompleted(false);
                $this->newsletterMapper->save($newsletter);

                /* Построим очередь отправки */
                $this->buildMailQueue($newsletter);
            } else {
                $this->newsletterMapper->save($newsletter);
            }

            $this->redirect('index.php?cmd=Newsletters');
        } catch (Exception $e) {
            Session::setError($e->getMessage());
            $this->redirect($request->env('HTTP_REFERER'));
        }
    }

    /**
     * @param RequestWrapper $request
     */
    public function configAction($request)
    {
        $this->_template = 'config';
        print $this->fetchTemplate();
    }

    /**
     * @deprecated не используется с 25.10.2018
     * @param RequestWrapper $request
     */
    public function saveConfigAction($request)
    {
        $siteConfigRepository = new SiteConfigurationRepository($this->cms);
        foreach ($request->getAll() as $name => $value) {
            $siteConfigRepository->Set($name, $value);
        }
        $this->redirect($request->env('HTTP_REFERER'));
    }

    /**
     * @param RequestWrapper $request
     */
    public function testAction($request)
    {
        try {
            $mailParams = Notifier::getMailParams(true);

            if (! $mailParams['mailFrom']) {
                throw new Exception(''.LangAdmin::get('Mailing_sender_email_not_set').'');
            }
            if (! $mailParams['nameFrom']) {
                throw new Exception(''.LangAdmin::get('Mailing_sender_name_not_set').'');
            }
            if (!$request->getValue('test-email')) {
                throw new Exception(''.LangAdmin::get('Mailing_receiver_email_not_set').'');
            }

            $mailBody = NewsletterEntity::prepareText($request->getValue('text'), $request->getValue('test-email'));

            General::mail_utf8($request->getValue('test-email'), $mailParams['nameFrom'],
                $mailParams['mailFrom'], $request->getValue('title'),
                $mailBody, false, array('useException' => true));
            $this->sendAjaxResponse(array('error' => 'Ok'));
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }
}