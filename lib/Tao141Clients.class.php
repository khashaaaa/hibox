<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dima
 * Date: 20.08.12
 * Time: 11:18
 * To change this template use File | Settings | File Templates.
 */
class Tao141Clients{
    public static function onRenderMoneyInfo($sid){
        global $otapilib;

        $user = $otapilib->GetUserInfo($sid);


        $cms = new CMS();
        $status = $cms->Check();
        if(!$status)
            return false;
        $cms->checkTable('site_users_additional_data');
        return array('iframe' => stripslashes(self::getUserAttrs((string)$user['Id'], 'iframe')));
    }

    public static function onRenderUserEditForm(&$user){
        $cms = new CMS();
        $status = $cms->Check();
        if(!$status)
            return ;
        $cms->checkTable('site_users_additional_data');
        $user['UserIframe'] = stripslashes(self::getUserAttrs((string)$user['Id'], 'iframe'));
        ob_start();
        require dirname(__FILE__).'/tpl/clients.tao141.onRenderUserEditForm.html';
        $c = ob_get_contents();
        ob_end_clean();
        return $c;
    }

    public static function onAddUser(&$user, $newUserId){
        $cms = new CMS();
        $status = $cms->Check();
        if(!$status)
            return ;
        $cms->checkTable('site_users_additional_data');

        $result = General::getCms()->query('
            INSERT INTO `site_users_additional_data`
            SET
                `userid`='.(int)$newUserId.',
                `attribute_title`="iframe",
                `attribute_value`=""
        ');

        return $result;
    }

    public static function onEditUser(&$user){
        $cms = new CMS();
        $status = $cms->Check();
        if(!$status)
            return ;
        $cms->checkTable('site_users_additional_data');

        if(self::userIframeExists($user['Id']))
            $result = self::addUserIframe(@$user['UserIframe'], $user['Id']);
        else
            $result = self::updateUserIframe(@$user['UserIframe'], $user['Id']);

        return $result;
    }

    private static function userIframeExists($id){
        return General::getCms()->querySingleValue('
                SELECT COUNT(*)
                FROM `site_users_additional_data`
                WHERE
                    `userid`="'.(int)$id.'"
                    AND `attribute_title`="iframe"
            ');
    }

    private static function addUserIframe($iframe, $id){
        return General::getCms()->query('
                UPDATE `site_users_additional_data`
                SET
                    `attribute_value`="'.General::getCms()->escape($iframe).'"
                WHERE `userid`='.(int)$id.' AND `attribute_title`="iframe"
            ');
    }

    private static function updateUserIframe($iframe, $id){
        return General::getCms()->query('
                INSERT INTO `site_users_additional_data`
                SET
                    `userid`='.(int)$id.',
                    `attribute_title`="iframe",
                    `attribute_value`="'.General::getCms()->escape($iframe).'"
            ');
    }

    public static function getUserAttrs($id, $attr){
        return General::getCms()->querySingleValue('
            SELECT `attribute_value`
            FROM `site_users_additional_data`
            WHERE
                `userid`="'.$id.'"
                AND `attribute_title`="'.General::getCms()->escape($attr).'"
        ');
    }

    public static function onCreateOrder($sid, $model){
        global $otapilib;
        return $otapilib->CreateMultiSalesOrder($sid, $model);
    }
}
