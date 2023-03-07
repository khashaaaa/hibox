<?php

class LangAdmin {
    static $lexicon = array();

    public static function getTranslations($path = '', $lang_external = '', $section = FALSE)
    {
        $lang = $lang_external ? $lang_external : Session::getActiveAdminLang();
        if ($section !== FALSE) {
            $section = strtolower($section);
        }

        $cms = new CMS();

        if ($section && file_exists('langs/' . $section . '/' . $lang . '.xml')) {
            $langfile_path =  'langs/' . $section . '/' . $lang . '.xml';
            if (empty(self::$lexicon)) {
                self::getTranslations($path, $lang_external, FALSE);
            }
        } else {
            $langfile_path = $path ? $path.$lang.'.xml' : 'langs/'.$lang.'.xml';
            $section = false;
        }
        $forceFile = false;
        if($path){
            $forceFile = true;
        }

        if($cms->Check() && $cms->existTranslations() && !$forceFile){
            $translations = $cms->getTranslations('', $lang);
            foreach($translations as $t){
                self::$lexicon[$t['key']] = $t['translation'];
            }
        }
        elseif($langfile_path && file_exists($langfile_path)){
            $translations = simplexml_load_file($langfile_path);
            foreach($translations->key as $t){
                if ($section) {
                    @self::$lexicon[$section . '::' . (string)$t['name']] = (string)$t;
                } else {
                    @self::$lexicon[(string)$t['name']] = (string)$t;
                }
            }
        }

        $langfile_custom_path = 'langscustom/'.$lang.'.xml';
        if(file_exists($langfile_custom_path)){
            $translations = simplexml_load_file($langfile_custom_path);
            foreach($translations->key as $t){
                @self::$lexicon[(string)$t['name']] = (string)$t;
            }
        }
    }

    public static function get($k, $params = array())
    {
        $phrase = isset(self::$lexicon[$k]) ? self::$lexicon[$k] : $k;
        if (strstr($k,'::') != FALSE && strstr($phrase, '::') != FALSE ) {
            // need load file
            $section = explode('::', $phrase);
            if (count($section) > 0 ) {
                $path = 'langs/' . $section[0] . '/';
                @self::getTranslations($path, Session::getActiveAdminLang(), $section[0]);
                $phrase = isset(self::$lexicon[$k]) ? self::$lexicon[$k] : $k;
            }
        }
        return str_replace(array_map('LangAdmin::applyWrapper', array_keys($params)), array_values($params), $phrase);
    }

    public static function getPlural($key, $count)
    {
        $pluralIndex = 5;
        if ($count % 10 == 1 && $count % 100 != 11) {
            $pluralIndex = 1;
        }
        if ($count % 10 >= 2 && $count % 10 <= 4 && ($count % 100 < 10 || $count % 100 >= 20)){
            $pluralIndex = 2;
        }
        $pluralKey = sprintf('%s_plural:%d', $key, $pluralIndex);

        return array_key_exists($pluralKey, self::$lexicon) ? self::$lexicon[$pluralKey] : $key;
    }

    public static function getAllTranslations()
    {
        return self::$lexicon;
    }

    public static function applyWrapper($value)
    {
        return "{{{$value}}}";
    }

    public static function loadJSTranslation($keys)
    {
        $trans = array();
        foreach($keys as $key){
            $trans[$key] = self::get($key);
        }

        return '<script type="text/javascript">transAdmin = '.json_encode($trans).';</script>';
    }
}
