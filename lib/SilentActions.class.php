<?php

class SilentActions {

    public static function backupMainPage($pageHtml = null)
    {
        if (date('i') % 5 != 0) {
            return;
        }
        MainPage::backup($pageHtml);
    }    

    public static function updateRefferals()
    {
        if (General::getConfigValue('referall_active_time')) {
            ReferallUpdateUserParent::updateUsers(General::getConfigValue('referall_active_time'));
        }        
    }
}
