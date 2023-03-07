<?php
class ReferalSystem
{
    const DEFAULT_GROUP = 1;

    public static function onPrivateOfficeMainPageRender($login,$id)
    {	
	   $cms = new CMS();
       $RefData = new ReferralUserManager($cms, new SupportRepository(new CMS()));
	   $RefCats = new ReferralCategoryManager($cms);

        try{
            $RefUser = $RefData->GetById($id);
			$RefUserCategory = $RefCats->GetById($RefUser->GetCategory());
            //Если юзер есть в реферале то выводим рефералку - нет знаит нет.
            $block = file_get_contents(dirname(__FILE__) . '/tpl/main.html');
            $link = base64_encode($login."|".$RefUser->GetId());

            $referrerPrefix = self::getReferrerKey();

            if (in_array('Seo2', General::$enabledFeatures)) {
                $referalUrl = UrlGenerator::getProtocol() . '://'.IDN::decodeIDN($_SERVER['HTTP_HOST']) . '/register?' . $referrerPrefix . '=' . $link;
            } else {
                $referalUrl = UrlGenerator::getProtocol() . '://'.IDN::decodeIDN($_SERVER['HTTP_HOST']) . '/?p=register&' . $referrerPrefix . '=' . $link;
            }

            $block = str_replace(
                array(
                    '[+main_text+]',
                    '[+url_text+]',
                    '[+url+]',
                    '[+balance_text+]',
                    '[+balance+]',
                    '[+category_text+]',
                    '[+category+]',
                    '[+button_copy+]',
                ),
                array(
                    Lang::get('user_referal'),
                    Lang::get('referal_link'),
                    $referalUrl,
                    Lang::get('referral_bonus'),
                    number_format((float)$RefUser->GetBalance(), (int)General::getNumConfigValue('price_rounding'), '.', ' '),
                    Lang::get('category'),
                    $RefUserCategory->GetGroupName(),
                    General::viewFetch('/referral/referal_link_copy', array(
                        'path' => CFG_BASE_TPL_ROOT,
                        'vars' => array('referalUrl' => $referalUrl)
                    )),
                ),
                $block
            );
            return $block;
        }
        catch(NotFoundException $e){
            $block = file_get_contents(dirname(__FILE__) . '/tpl/mainNoFound.html');
            $link = base64_encode($login."|".$id);

            $referrerPrefix = self::getReferrerKey();
            if (in_array('Seo2', General::$enabledFeatures)) {
                $referalUrl = UrlGenerator::getProtocol() . '://'.IDN::decodeIDN($_SERVER['HTTP_HOST']) . '/register?' . $referrerPrefix . '=' . $link;
            } else {
                $referalUrl = UrlGenerator::getProtocol() . '://'.IDN::decodeIDN($_SERVER['HTTP_HOST']) . '/?p=register&' . $referrerPrefix . '=' . $link;
            }

            $block = str_replace(
                array(
                    '[+main_text+]', '[+url_text+]','[+url+]','[+button_copy+]'
                ),
                array(
                    Lang::get('user_referal'),
                    Lang::get('referal_link'),
                    $referalUrl,
                    General::viewFetch('/referral/referal_link_copy', array(
                        'path' => CFG_BASE_TPL_ROOT,
                        'vars' => array('referalUrl' => $referalUrl)
                    )),
                ),
                $block
            );
            return $block;
			
        }

    }

    public static function onUserRegister($userInfo)
    {
        $refUserManager = new ReferralUserManager(new CMS());

        try {
            $referrerUser = $refUserManager->GetByLogin($userInfo['parent']);
        } catch (NotFoundException $e) {
            $referrerUser = new ReferralUser(0);
            if ((! empty($userInfo['parent'])) && (! empty($userInfo['parent_id']))) {
                $refUserManager->Add($userInfo['parent'], $userInfo['parent_id'], $referrerUser->GetId());
                $referrerUser = $refUserManager->GetByLogin($userInfo['parent']);
            }
        } catch (DBException $e) {}

        if (!empty($userInfo['username']) && !empty($userInfo['id'])) {
            try {
                $referralUser = $refUserManager->GetByLogin($userInfo['username']);
            } catch (NotFoundException $e) {
                return (bool) $refUserManager->Add($userInfo['username'], $userInfo['id'], $referrerUser->GetId());
            }
        }

        return false;
    }

    public static function onRenderAddBestItemsForm(){

    }

    public static function onRegisterFormRender(){
        $refId = self::getReferrerKeyValue();
        ob_start();
        include dirname(__FILE__) . '/tpl/register.php';
        $c = ob_get_contents();
        ob_end_clean();
        return $c;
    }

    /**
     * Генерация реферального ключа пользователя
     *
     * @param $id - id реферала
     * @param $login - login реферала
     * @return string
     */
    public static function generateReferralKey($id, $login)
    {
        return base64_encode($login . '|' . $id);
    }

    /**
     * Запомнить реферальный ключ в случае
     * если он пришел в $_GET параметре
     */
    public static function defineReferrerKey()
    {
        $referrerKey = self::getReferrerKey();
        if (RequestWrapper::get($referrerKey)) {
            Cookie::set($referrerKey, RequestWrapper::get($referrerKey), time()+60*60*24*30, '/', '.' . TS_HOST_NAME);
            Session::set($referrerKey, RequestWrapper::get($referrerKey));
        }
    }

    /** Получить префикс реферального ключа
     *
     * @return string
     */
    public static function getReferrerKey()
    {
        return (defined('REFERER_KEY')) ? REFERER_KEY : 'refId';
    }

    /**
     * Получить значение реферального ключа
     *
     * @return string
     */
    public static function getReferrerKeyValue()
    {
        $referrerPrefix = self::getReferrerKey();
        $refId = Cookie::get($referrerPrefix, Session::get($referrerPrefix));
        $refId = $refId ? $refId : RequestWrapper::get($referrerPrefix);

        return $refId;
    }

    /**
     * Получить информацию будущем реферале
     *
     * @return array
     */
    public static function getReferrerInfo()
    {
        $refId = self::getReferrerKeyValue();
        $userData = explode('|', base64_decode($refId));
        $parentLogin = isset($userData[0]) ? $userData[0] : '';
        $parentId = isset($userData[1]) ? $userData[1] : '';

        return [$parentId, $parentLogin];
    }
}

