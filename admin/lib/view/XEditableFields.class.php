<?php
class XEditableFields {
    private static $fields = array();
    private static $config;
    private static $configWithLangs = null;
    /**
     * @var AdminUrlWrapper
     */
    private static $pageUrl;

    public static function Init($configName, $pageUrl, $path = false){
        self::$fields = array();
        $path = ($path) ? $path : BASE_ADMIN_PATH . 'cfg/site_configuration/';
        self::$config = json_decode(file_get_contents($path . $configName.'.json'));
        self::$pageUrl = $pageUrl;
        self::$configWithLangs = (is_null(self::$configWithLangs)) ? self::PrepareConfigWithLangs() : self::$configWithLangs;
    }

    public static function Register($fieldName, $fieldValue, $parameters = array(), $valuesList = array()){
        try {
            $field = self::SearchFieldConfigByName($fieldName);
            $field->value = stripslashes((string)$fieldValue);
            $field->saveUrl = isset($field->saveUrl) ?
                self::PrepareSaveUrl($field->saveUrl) : self::PrepareDefaultSaveUrl();
            $field = self::LoadNotExistedParametersFromDefaults($field);
            if (key_exists($fieldName, self::$configWithLangs)) {
                $parameters['langVersions'] = self::$configWithLangs[$fieldName];
            }
            $field->parameters = $parameters;
            if (count($valuesList)) {
                $field->valuesList = $valuesList;
            }
            self::$fields[] = $field;
        }
        catch (NotFoundException $e) {
            echo $e->getMessage();
            die();
        }
    }

    private static function SearchFieldConfigByName($name){
        foreach(self::$config->fields as $field){
            if($field->name == $name){
                return clone $field;
            }
        }
        throw new NotFoundException("Field $name not found in config");
    }

    private static function LoadNotExistedParametersFromDefaults($field){
        $defaults = isset(self::$config->defaultFieldsParameters->{$field->type}) ?
            self::$config->defaultFieldsParameters->{$field->type} : new stdClass();
        foreach($defaults as $key=>$value){
            if(isset($field->{$key}))
                continue;

            if(isset($value->trans))
                $value = LangAdmin::get($value->trans);
            $field->{$key} = $value;
        }
        return $field;
    }

    public static function GetFields(){
        return self::$fields;
    }

    private static function PrepareDefaultSaveUrl(){
        $defaultUrl = self::$config->defaultSaveUrl;
        return self::$pageUrl->AssignCmdAndDo($defaultUrl->cmd, $defaultUrl->do);
    }

    private static function PrepareSaveUrl($urlData){
        return self::$pageUrl->AssignCmdAndDo($urlData->cmd, $urlData->do);
    }

    private static function PrepareConfigWithLangs(){
        $cmd = self::$pageUrl->GetCmd();
        $activeLang = Session::get('active_lang_' . strtolower($cmd));

        $cms = new CMS();
        $configs = new SiteConfigurationRepository($cms);
        $configs = $configs->getAll();

        $languageRepository = new LanguageRepository($cms);
        $langs = $languageRepository->GetLanguages();

        $configsWithLangs = array();

        foreach ($configs as $config => $configValue) {
            foreach ($langs as $lang) {
                $baseConfig = preg_replace('/_' . $lang['lang_code'] . '\Z/i', '', $config);
                if (!empty($configValue) || $configValue === '0') {
                    if ($config !== $baseConfig) {
                        if ($lang['lang_code'] === $activeLang) {
                            $configsWithLangs[$baseConfig][$lang['lang_code']] = true;
                        } else {
                            $configsWithLangs[$baseConfig][$lang['lang_code']] = false;
                        }
                    } else {
                        $configsWithLangs[$baseConfig]['all'] = ($activeLang) ? false: true;
                    }
                }
            }
        }

        return $configsWithLangs;
    }
}