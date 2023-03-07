<?php

class LangTranslations {
    function defaultAction(){
        global $otapilib;
        
        if (!Login::auth()){
            include(TPL_DIR.'login.php');
            return false;
        } 
        
        $cms = new CMS();
        if (!$cms->Check()){
            include(TPL_DIR . 'db_connection_fail.php');
            die;
        }
        
        $cms->checkTable('site_langs');
        $sid = $_SESSION['sid'];
        $data = $otapilib->GetWebUISettings($sid);
        foreach($data->Settings->Languages->NamedProperty as $v){
            $lang = (string)$v->Name;
            $lang_desc = (string)$v->Description;
            $cms->checkLanguage($lang, $lang_desc);
        }
            
        $langs = $cms->getLanguages();
        $current_lang = $this->setActiveLang();
        
        $cms->checkTable('site_translations');
        $cms->checkTable('site_translation_keys');
        $cms->checkTranslations();
        
        $translations = $cms->getTranslations('', $current_lang);
        include(TPL_DIR . 'lang.php');
    }
    
    private function setActiveLang(){
        if(@$_GET['lang'])
            $_SESSION['translate_lang'] = @$_GET['lang'];
        if(!@$_SESSION['translate_lang']){
            $_SESSION['translate_lang'] = 'en';
        }
        return $_SESSION['translate_lang'];
    }
    
    public function addTranslationAction(){
        if (!Login::auth()){
            include(TPL_DIR.'login.php');
            return false;
        } 
        
        $cms = new CMS();
        if (!$cms->Check()){
            include(TPL_DIR . 'db_connection_fail.php');
            die;
        }
        
        $langs = $cms->getLanguages();
        include(TPL_DIR . 'translation/editlang.php');
    }
    
    public function editAction(){
		global $otapilib;
        if (!Login::auth()){
            include(TPL_DIR.'login.php');
            return false;
        } 
        
        $cms = new CMS();
        if (!$cms->Check()){
            include(TPL_DIR . 'db_connection_fail.php');
            die;
        }
		
		$isSearchTranslation = false;        
		if ($_GET['key'] == 'Taobao_Extended_Flag') {
			$isSearchTranslation = true;			
		}
		
        $trans = $cms->getTranslations('','',$_GET['key']);
        $langs = $cms->getLanguages();
        include(TPL_DIR . 'translation/editlang.php');
    }
    
    public function delAction(){
        if (!Login::auth()){
            include(TPL_DIR.'login.php');
            return false;
        } 
        $cms = new CMS();
        if (!$cms->Check()){
            include(TPL_DIR . 'db_connection_fail.php');
            die;
        }
        mysql_query("DELETE FROM `site_translations` WHERE `id`='".(int)$_GET['id']."'");
        header('Location: index.php?cmd=langTranslations');
        die;
    }
    
    public function saveTranslationAction(){
        if (!Login::auth()){
            include(TPL_DIR.'login.php');
            return false;
        } 
        $cms = new CMS();
        if (!$cms->Check()){
            include(TPL_DIR . 'db_connection_fail.php');
            die;
        }
        $_SESSION['translation_error'] = '';
        if(isset($_POST) && $_POST){
            if (isset($_POST['key']) && ($_POST['key'] !== '')) {
                $keyid = $this->_addKey($_POST['key']);
                foreach($_POST['translations'] as $lang=>$trans){
                    $res = $this->_addTranslation($keyid, $lang, $trans);
                    if(!$res[0]){
                        $_SESSION['translation_error'] = $res[1];
                    }
                }
            } else {
                $_SESSION['translation_error'] = '"Key" field can not be empty';
            }
        }
        
        header('Location: index.php?cmd=LangTranslations');
        die;
    }

    public function exportAction(){
        @define('NO_DEBUG', 1);

        if (!Login::auth()){
            include(TPL_DIR.'login.php');
            return false;
        }
        $cms = new CMS();
        if (!$cms->Check()){
            include(TPL_DIR . 'db_connection_fail.php');
            die;
        }
        $langs = $cms->getLanguages();
        setlocale(LC_ALL, 'ru_RU.UTF-8');

        foreach($langs as $l){
            $translations = $cms->getTranslations('', $l['lang_code']);

            $xml = new DOMDocument('1.0', 'utf-8');

            $xml->formatOutput = true;
            $xml->preserveWhiteSpace = false;

            $translationsXML = $xml->createElement('translations');

            foreach($translations as $t){
                $el = $xml->createElement('key', htmlspecialchars($t['translation']));
                $el->setAttribute('name', htmlspecialchars($t['key']));
                $translationsXML->appendChild($el);
            }
            $xml->appendChild($translationsXML);

            $xml->save('../langs/'.$l['lang_code'].'.xml');
        }
    }

    public function importAction(){
        @define('NO_DEBUG', 1);
        if (!Login::auth()){
            include(TPL_DIR.'login.php');
            return false;
        }
        $cms = new CMS();
        if (!$cms->Check()){
            include(TPL_DIR . 'db_connection_fail.php');
            die;
        }

        $langs = $cms->getLanguages();
        foreach($langs as $l){
            if(!file_exists('../langs/'.$l['lang_code'].'.xml')) continue;
            $xml = simplexml_load_file('../langs/'.$l['lang_code'].'.xml');
            foreach($xml->key as $k){
                $t = $cms->getTranslations('', $l['lang_code'], $k['name']);
                if(!$t){
                    $keyid = $this->_addKey($k['name']);
                    $res = $this->_addTranslation($keyid, $l['lang_code'], (string)$k[0]);
                }
            }
        }
    }

    private function _getTranslation(){
    }
    
    private function _addKey($key){
        $key = mysql_real_escape_string($key);
        $q = mysql_query("SELECT `id` FROM `site_translation_keys` WHERE BINARY `name`='$key'");
        
        if(mysql_num_rows($q))
            return mysql_result($q, 0);
        
        mysql_query("INSERT INTO `site_translation_keys` SET `name`='$key'");
        return mysql_insert_id();
    }
    
    private function _addTranslation($keyid, $lang, $trans){
        if(!$trans)
            return array(true);
        
        $lang = mysql_real_escape_string($lang);
        $q = mysql_query("SELECT `id` FROM `site_langs` WHERE `lang_code`='$lang'");
        if(!mysql_num_rows($q))
            return array(false, LangAdmin::get('language_was_not_found'));
        
        $langid = mysql_result($q, 0);
        $trans = mysql_real_escape_string($trans);
        
        mysql_query("DELETE FROM `site_translations` WHERE `langid`='$langid' AND `key`='$keyid'");
        mysql_query("INSERT INTO `site_translations` SET `langid`='$langid', `key`='$keyid', `translation`='$trans'");
        return array(true);
    }
}