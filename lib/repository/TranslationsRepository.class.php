<?php
class TranslationsRepository extends Repository {
    /**
     * @var CMS
     */
    protected $cms;
    /**
     * @var LanguageRepository
     */
    protected $languageRepository;

    private $languages = array();

    public function __construct($cms){
        parent::__construct($cms);
        $this->languageRepository = new LanguageRepository($cms);
        $this->languages = $this->languageRepository->GetLanguages();
    }

    public function GetAllTranslationsByLangCodes($languages){
        $result = array();
        foreach($languages as $key => $lang){
            $fromFile = $this->GetTranslationsFromFile($key);
            $fromDB = $this->GetAllTranslationsFromDBByLang($key);
            $result[$key] = $this->MergeFileAndDBTranslations($fromFile, $fromDB);
        }
        return $result;
    }

    public function GetTranslationsFromFile($lang){
        $filePath = dirname(dirname(dirname(__FILE__))) . '/langs/'.$lang.'.xml';
        if(file_exists($filePath)){
            $result = array();
            $translations = simplexml_load_file($filePath);
            foreach($translations->key as $t){
                $result[(string)$t['name']] = array(
                    'translation' => (string)$t,
                    'from' => 'box'
                );
            }
            return $result;
        }
        else{
            return array();
        }
    }

    public function GetAllTranslationsFromDBByLang($langCode){
        $code = $this->cms->escape($langCode);
        return $this->cms->queryMakeArray("
            SELECT `sl`.`lang_code`, `sk`.`name` as `key`, `st`.`translation`
            FROM `site_translations` `st`
                INNER JOIN `site_langs` `sl` ON `sl`.`id` = `st`.`langid`
                INNER JOIN `site_translation_keys` `sk`  ON `sk`.`id` = `st`.`key`
            WHERE `sl`.`lang_code` = '$code'
        ", array('site_translations', 'site_langs', 'site_translation_keys'));
    }

    public function GetAllTranslationsFromDB(){
        return $this->cms->queryMakeArray("
            SELECT `sl`.`lang_code`, `sk`.`name` as `key`, `st`.`translation`
            FROM `site_translations` `st`
                INNER JOIN `site_langs` `sl` ON `sl`.`id` = `st`.`langid`
                INNER JOIN `site_translation_keys` `sk`  ON `sk`.`id` = `st`.`key`
        ", array('site_translations', 'site_langs', 'site_translation_keys'));
    }

    private function MergeFileAndDBTranslations($fromFile, $fromDB){
        $result = $fromFile;
        foreach($fromDB as $translation){
            $result[$translation['key']] = array(
                'translation' => (string)$translation['translation'],
                'from' => 'db'
            );
        }
        return $result;
    }

    public function GetAllTranslationsByKeys($languages){
        $result = array();
        $transByCodes = $this->GetAllTranslationsByLangCodes($languages);

        foreach($transByCodes as $langCode => $transList){
            foreach($transList as $transKey => $trans){
                if(!isset($result[$transKey]))
                    $result[$transKey] = $this->GetTemplateArrayForKey($languages);
                $result[$transKey][$langCode] = $trans;
            }
        }

        return $result;
    }

    private function GetTemplateArrayForKey($languages){
        $result = array();
        foreach($languages as $key => $lang){
            $result[$key] = array();
        }
        return $result;
    }

    public function DeleteTranslationsByKeyFromDB($code){
        $codeSafe = $this->cms->escape($code);
        $this->cms->query("
            DELETE FROM `site_translations`
            WHERE `key` IN (
                SELECT `id` FROM `site_translation_keys` WHERE `name` = '$codeSafe'
            )
       ");
    }
    
    public function DeleteTranslationsKeyFromDB($code){
        $codeSafe = $this->cms->escape($code);
        $this->cms->query("DELETE FROM `site_translation_keys` WHERE `name` = '$codeSafe'");
    }
    
    public function DeleteTranslationsByKeyAndLangFromDB($code, $language){
        $codeSafe = $this->cms->escape($code);
        $languageId = $this->cms->_getLangCodeId($language);
        $this->cms->query("
            DELETE FROM `site_translations`
            WHERE `key` = '$codeSafe' AND `langid` = '$languageId'
       ");
    }

    public function GetTranslationsByKey($key){
        $code = $this->cms->escape($key);
        return $this->cms->queryMakeArray("
            SELECT `st`.`translation`, `sl`.`lang_code`
            FROM `site_translations` `st`
                INNER JOIN `site_langs` `sl` ON `sl`.`id` = `st`.`langid`
                INNER JOIN `site_translation_keys` `sk`  ON `sk`.`id` = `st`.`key`
            WHERE `sk`.`name` = '$code'
        ", array('site_translations', 'site_langs', 'site_translation_keys'));
    }

    public function AddTranslation($key, $translations){
        $keyId = $this->AddKey($key);
        foreach($translations as $lang=>$trans){
            $this->AddTranslationForLang($keyId, $lang, $trans);
        }
    }

    private function AddKey($key){
        $keySafe = $this->cms->escape($key);
        try {
            $exists = $this->cms->queryMakeArray("
            SELECT `id`,`name` FROM `site_translation_keys` WHERE `name`='$keySafe'
        ");
            if (!empty($exists[0])) {
                if ($exists[0]['name'] !== $key) {
                    $this->DeleteTranslationsByKeyFromDB($key);
                    $this->DeleteTranslationsKeyFromDB($key);
                    $this->cms->query("INSERT INTO `site_translation_keys` SET `name`='$keySafe'");
                    return $this->cms->insertedId();
                } else {
                    return $exists[0]['id'];
                }
            } else {
                $this->cms->query("INSERT INTO `site_translation_keys` SET `name`='$keySafe'");
                return $this->cms->insertedId();
            }
        } catch (DBException $e) {
            Session::setError($e->getMessage());
        }
    }

    private function AddTranslationForLang($keyId, $lang, $trans){
        if($trans) {
            try {
                $langSafe = $lang;
                $q = $this->cms->querySingleValue("SELECT COUNT(`id`) FROM `site_langs` WHERE `lang_code`='$langSafe'");
                if (!$q)
                    throw new Exception(LangAdmin::get('language_was_not_found'));

                $langId = $this->cms->querySingleValue("SELECT `id` FROM `site_langs` WHERE `lang_code`='$langSafe'");
                $transSafe = $this->cms->escape($trans);
                $this->cms->query("DELETE FROM `site_translations` WHERE `langid`='$langId' AND `key`='$keyId'");
                $this->cms->query("INSERT INTO `site_translations` SET `langid`='$langId', `key`='$keyId', `translation`='$transSafe'");
            } catch (DBException $e) {
                Session::setError($e->getMessage());
            }
        }
    }
}