<?php
class LanguageRepository extends Repository {
    public function GetLanguages(){
        return $this->cms->queryMakeArray("SELECT * FROM `site_langs`", array('site_langs'));
    }
}