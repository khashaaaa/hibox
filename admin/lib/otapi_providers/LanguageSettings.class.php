<?php

OTBase::import('system.lib.Cache');
OTBase::import('system.lib.cache.Key');
OTBase::import('system.lib.cache.adapter.*');

class LanguageSettings
{
    /**
     * @var OTAPIlib
     */
    private $otapilib;

    public function __construct($otapilib)
    {
        $this->otapilib = $otapilib;
        $this->cacher = new Cache('GetWebUISettings');
    }

    public function GetLanguages()
    {
        return $this->getWebUISettings();
    }

    public function GetActiveLanguages()
    {
        $languages = array();

        $webUISettings = $this->getWebUISettings();
        if ($webUISettings) {
            $allLanguages = array();
            foreach ($webUISettings->Languages->NamedProperty as $l) {
                $allLanguages[(string)$l->Name] = (string)$l->Description;
            }
            foreach ($webUISettings->UsedLanguages->string as $l) {
                $languages[(string)$l] = $allLanguages[(string)$l];
            }
            //добавляем текущий активный язык админки
            if (!array_key_exists(Session::getActiveAdminLang(), $languages)){
                $languages[(string)Session::getActiveAdminLang()] = $allLanguages[(string)Session::getActiveAdminLang()];
            }
        }

        return $languages;
    }

    public function AddLanguageToWebUI($lang)
    {
        $webUISettings = $this->getWebUISettings();

        $settings = new SimpleXMLElement('<Settings></Settings>');
        $usedLanguages = $settings->addChild('UsedLanguages');
        foreach ($webUISettings->UsedLanguages->string as $l) {
            $usedLanguages->addChild('string', (string) $l);
        }
        $usedLanguages->addChild('string', $lang);

        $this->cacher->drop();
        $this->otapilib->SetWebUISettings(Session::get('sid'), $settings->asXML());
    }

    public function DeleteLanguageFromWebUI($lang)
    {
        $webUISettings = $this->getWebUISettings();

        $settings = new SimpleXMLElement('<Settings></Settings>');
        $usedLanguages = $settings->addChild('UsedLanguages');
        foreach ($webUISettings->UsedLanguages->string as $l) {
            if ((string)$lang != (string)$l) {
                $usedLanguages->addChild('string', (string) $l);
            }
        }
        $this->cacher->drop();
        $this->otapilib->SetWebUISettings(Session::get('sid'), $settings->asXML());
    }

    public function SaveLanguagesOrder($langs)
    {
        $settings = new SimpleXMLElement('<Settings></Settings>');
        $usedLanguages = $settings->addChild('UsedLanguages');
        foreach($langs as $l){
            $usedLanguages->addChild('string', $l);
        }
        $this->cacher->drop();
        $this->otapilib->SetWebUISettings(Session::get('sid'), $settings->asXML());
    }

    private function getWebUISettings()
    {
        $result = null;
        if (! $this->cacher->has()) {
            try {
                $result = $this->otapilib->GetWebUISettings(Session::get('sid'));
            } catch (ServiceException $e) {}
            if (! empty($result) && ! Session::get('isMultiCurlRunning')) {
                if (! isset($result->Settings)) {
                    throw new ServiceException('', '', 'Settings are empty', 'Error');
                }
                if (empty($result->HasTranslateErrors)) {
                    $this->cacher->set(preg_replace('#>\s+<#si', '><', $result->Settings->asXML()));
                }
                $result = preg_replace('#>\s+<#si', '><', $result->Settings->asXML());
            }
        } else {
            $result = $this->cacher->get();
        }
        return simplexml_load_string($result);
    }
}
