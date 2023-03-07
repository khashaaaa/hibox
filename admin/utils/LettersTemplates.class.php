<?php

class LettersTemplates extends GeneralUtil
{
    protected $_template = 'letters_templates';
    protected $_template_path = 'letters_templates/';

    /**
     * @param RequestWrapper $request
     */
    public function defaultAction($request)
    {
        $lang = Session::getActiveAdminLang();
        $providers = array();
        $providerSettings = array();

        $events = array();
        try {
            OTAPILib2::GetMessageEventList($lang, Session::get('sid'), $eventsList);
            OTAPILib2::makeRequests();
            
            $eventsList = $eventsList->GetResult()->GetContent();
            foreach ($eventsList->GetItem() as $item) {
                $events[] = $item;
            }
        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }

        $this->tpl->assign('events', $events);
        $this->tpl->assign('updateTemplateUrl', 'index.php?cmd=LettersTemplates&do=updateTemplate');

        print $this->fetchTemplate();
    }

    public function getTemplateAction($request)
    {
        $html = '';
        
        try {
            $type = $request->getRequestValueSafe('type');
            $langs = $this->languagesProvider->GetActiveLanguages();
            $lang = Session::get('active_lang_letterstemplates') ? Session::get('active_lang_letterstemplates') : Session::getActiveAdminLang();
            if (! isset($langs[$lang])) {
                $lang = isset($langs[Session::getActiveAdminLang()]) ? Session::getActiveAdminLang() : array_shift(array_keys($langs));
                Session::setActiveAdminLang($lang);
            }
            Session::set('active_lang_letterstemplates', $lang);
            
            OTAPILib2::GetMessageTemplate(Session::getActiveAdminLang(), Session::get('sid'), $lang, $type, 'true', $request);
            OTAPILib2::makeRequests();
            
            if ($request && $request->GetResult()) {
                $eventTemplate = $request->GetResult()->GetRawData();
                $html = General::viewFetch('letters_templates/letter_template', array(
                    'path' => TPL_PATH,
                    'vars' => array(
                        'type' => $type,
                        'eventTemplate' => $eventTemplate,
                        'updateTemplateUrl' => $this->pageUrl->Add('do', 'updateTemplate')->Get() . '&eventType=' . $type,
                        'testTemplateServerUrl' => $this->pageUrl->Add('do', 'testTemplate')->Get() . '&eventType=' . $type,
                    )
                ));
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        
        $this->sendAjaxResponse(array(
            'html' => $html
        ));
    }

    public function updateTemplateAction($request)
    {
        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->get('type');
        $eventType = $request->get('eventType');
        
        try {
            $langs = $this->languagesProvider->GetActiveLanguages();
            $lang = Session::get('active_lang_letterstemplates') ? Session::get('active_lang_letterstemplates') : Session::getActiveAdminLang();
            if (! isset($langs[$lang])) {
                $lang = isset($langs[Session::getActiveAdminLang()]) ? Session::getActiveAdminLang() : array_shift(array_keys($langs));
                Session::setActiveAdminLang($lang);
            }
            Session::set('active_lang_letterstemplates', $lang);
            
            $xmlUpdateData = MetaUI::generateSingleParamXml('MessageTemplateUpdateData', array(
                $name
            ), $value, $type);
            $answer = false;
            
            OTAPILib2::UpdateMessageTemplate(Session::getActiveAdminLang(), Session::get('sid'), $lang, $eventType, $xmlUpdateData, $answer);
            OTAPILib2::makeRequests();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
    }

    public function testTemplateAction($request)
    {
        $lang = Session::getActiveAdminLang();
        $sid = Session::get('sid');

        try {
            $eventType = $request->get('eventType');
            $recipientInfo = $request->getValue('recipient');

            $inputLanguage = $this->getActiveLang($request);
            Session::set('active_lang_letterstemplates', $inputLanguage);
            
            OTAPILib2::TestMessageTemplate($lang, $sid, $inputLanguage, $eventType, $recipientInfo, $answer);
            OTAPILib2::makeRequests();
    
        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse(array('message' => LangAdmin::get('Test_letter_successfuly_sent')));
    }
}
