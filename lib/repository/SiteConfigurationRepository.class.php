<?php
class SiteConfigurationRepository extends Repository {
    private $loadedConfiguration = array();
    private $lang = '';

    public function __construct($cms, $isNeedPreload = true){
        parent::__construct($cms);
        if ($isNeedPreload) {
            $this->LoadConfiguration();
        }
    }

    public function LoadConfiguration(){
        $siteConfig = $this->cms->queryMakeArray("SELECT * FROM `site_config`", array('site_config'));
        $result = array();
        foreach($siteConfig as $configValue){
            $result[$configValue['key']] = $configValue['value'];
        }
        $this->loadedConfiguration = $result;
    }

    public function Get($key, $default = null, $allowEmpty = true)
    {
        $key = $key . ($this->lang ? "_".$this->lang : "");
        $result = isset($this->loadedConfiguration[$key]) ? $this->loadedConfiguration[$key] : $default;
        if ($allowEmpty) {
            return $result;
        } else {
            return !empty($result) ? $result : $default;
        }
    }

    public function GetAll(){
        return $this->loadedConfiguration;
    }

    public function Set($key, $value){
        $langSuffix = $this->lang ? "_".$this->lang : "";

        $keySafe = $this->cms->escape($key.$langSuffix);
        $valueSafe = $this->cms->escape($value);

        $this->cms->query("DELETE FROM `site_config` WHERE `key`='$keySafe'", array('site_config'));
        $this->cms->query("INSERT INTO `site_config` SET `key`='$keySafe', `value` = '$valueSafe'", array('site_config'));

        $this->loadedConfiguration[$keySafe] = $valueSafe;
    }
    
    public function Remove($key){
    	$langSuffix = $this->lang ? "_".$this->lang : "";
    	$keySafe = $this->cms->escape($key.$langSuffix);
    	$this->cms->query("DELETE FROM `site_config` WHERE `key`='$keySafe'", array('site_config'));
    }

    public function RemoveByLang($configName, $configLang)
    {
        if ($configLang === 'all') {
            $this->cms->query("DELETE FROM `site_config` WHERE `key` =  '".$this->cms->escape($configName)."'");
        } else {
            $config = $configName . "_" . $configLang;
            $this->cms->query("DELETE FROM `site_config` WHERE `key` = '".$this->cms->escape($config)."'");
        }
    }

    public function SetActiveLang($lang){
        $this->lang = $lang;
    }
}