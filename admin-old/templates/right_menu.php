<?php
global $otapilib;



$SupportRepository = new SupportRepository(new CMS());

$unreadCount = $SupportRepository->getTicketMessagesCount(false, 'In', 0);

$newMessages = $unreadCount>0 ? ' ('.$unreadCount.')':'';

$menu = array();
$menu[] = array('cmd' => '', 'label' => LangAdmin::get('settings'), 'active' => !isset($_GET['cmd']) && !isset($_GET['do']));
$menu[] = array('cmd' => 'siteConfiguration', 'label' => LangAdmin::get('site_configuration'), 'active' => null);
//Кэш
$menu[] = array('cmd' => 'caching', 'label' => LangAdmin::get('caching'), 'active' => null);
//
$menu[] = array('cmd' => '', 'do' => 'case', 'label' => LangAdmin::get('pricing'), 'active' => !isset($_GET['cmd']) && @$_GET['do'] == 'case');
if(CMS::IsFeatureEnabled('CurrencyRateEnhance'))
    $menu[] = array('cmd' => '', 'do' => 'pricing', 'label' => LangAdmin::get('pricing').' NEW', 'active' => !isset($_GET['cmd']) && @$_GET['do'] == 'pricing' );
$menu[] = array('cmd' => 'category', 'label' => LangAdmin::get('categories'), 'active' => null);
if($_SESSION['active_lang_admin']==='ru'&&CMS::IsFeatureEnabled('SberbankInvoice'))
    $menu[] = array('cmd' => 'sb_invoice', 'label' => LangAdmin::get('quittance'), 'active' => null);
$menu[] = array('cmd' => 'orders', 'label' => LangAdmin::get('orders'), 'active' => null);
$menu[] = array('cmd' => 'users', 'label' => LangAdmin::get('members'), 'active' => null);
if(CMS::IsFeatureEnabled('Discount'))
    $menu[] = array('cmd' => 'discount', 'label' => LangAdmin::get('adm_discount'), 'active' => null);
if(CMS::IsFeatureEnabled('News'))
    $menu[] = array('cmd' => 'news', 'label' => LangAdmin::get('news'), 'active' => null);
if(CMS::IsFeatureEnabled('Digest'))
    $menu[] = array('cmd' => 'digest', 'label' => LangAdmin::get('digest'), 'active' => null);
$menu[] = array('cmd' => 'reviews', 'label' => LangAdmin::get('reviews'), 'active' => null);
if(CMS::IsFeatureEnabled('ShopComments'))
    $menu[] = array('cmd' => 'shopreview', 'label' => LangAdmin::get('shopreview'), 'active' => null);

$menu[] = array('cmd' => 'set', 'label' => LangAdmin::get('collections'), 'active' => null);
$menu[] = array('cmd' => 'Set2', 'label' => LangAdmin::get('collections').' New', 'active' => null);

$menu[] = array('cmd' => 'brands', 'label' => LangAdmin::get('brands'), 'active' => null);
$menu[] = array('cmd' => 'banners', 'label' => LangAdmin::get('banners'), 'active' => null);
$menu[] = array('cmd' => 'update', 'label' => LangAdmin::get('update'), 'active' => null);
$menu[] = array('cmd' => 'ipaccess', 'label' => LangAdmin::get('ipaccess_config'), 'active' => null);
if (defined('KEY_REFERRAL_SYSTEM'))
    $menu[] = array('cmd' => 'referrals', 'label' => LangAdmin::get('bonus_program'), 'active' => null);

$result = Plugins::invokeEvent('onAdminMainMenuRender');
if($result && count($result) == 1)
    $menu[] = $result;
elseif($result){
    $menu = array_merge($menu, $result);
}
	
$menu[] = array('cmd' => 'langTranslations', 'label' => LangAdmin::get('translations'), 'active' => null);
$menu[] = array('cmd' => 'support', 'label' => LangAdmin::get('support_requests').$newMessages, 'active' => null);
//if(CMS::IsFeatureEnabled('DeliveryCalculator'))
    //$menu[] = array('cmd' => 'calculator', 'label' => LangAdmin::get('calculator'), 'active' => null);
if (defined('MY_GOODS_SYSTEM')) {
    $menu[] = array('cmd' => 'my_categories', 'label' => LangAdmin::get('support_requests'), 'active' => null);
    $menu[] = array('cmd' => 'my_goods', 'label' => LangAdmin::get('support_requests'), 'active' => null);
}
$menu[] = array('cmd' => 'filtering', 'label' => LangAdmin::get('content_filtering'), 'active' => null);

if(CMS::IsFeatureEnabled('Order'))
    $menu[] = array('cmd' => 'order_settings', 'label' => LangAdmin::get('order_settings'), 'active' => null);

$menu[] = array('cmd' => 'ServiceCallCounter', 'label' => LangAdmin::get('service_counter'), 'active' => null);
$menu[] = array('cmd' => 'Delivery', 'label' => LangAdmin::get('delivery'), 'active' => null);
if (defined('CFG_BUYINCHINA')) $menu[] = array('cmd' => 'FinReport', 'label' => LangAdmin::get('support_requests'), 'active' => null);

if (defined('CFG_ADMIN_USERS')) $menu[] = array('cmd' => 'adminusers', 'label' => LangAdmin::get('Adminusers'), 'active' => null);

if (CMS::IsFeatureEnabled('ReferralProgram'))
    $menu[] = array('cmd' => 'Referralold', 'label' => LangAdmin::get('Referral_system'), 'active' => null);

if(@$_SESSION['sid'])
    $menu[] = array('cmd' => 'logout', 'label' => LangAdmin::get('logout'), 'active' => null);
else
    $menu[] = array('cmd' => 'login', 'label' => LangAdmin::get('logout'), 'active' => null);

$newMenu = Plugins::invokeEvent('onTopAdminMenuRender', array('menu' => $menu));
if($newMenu){
    $menu = $newMenu;
}

if (defined('SEND_EMAIL_NOTIFICATION')) {
$notify = array('cmd' => 'notification', 'label' => LangAdmin::get('notify'), 'active' => null);//$onRenderNotificationForm
    array_unshift($menu, $notify);
}

if (isset($_SESSION['sid']) && defined('BUY_IN_CHINA')) { // GetInstanceUserRoleList
    $notify = array('cmd' => 'adminusers', 'label' => LangAdmin::get('Adminusers'), 'active' => null);
    array_unshift($menu, $notify);
    /*if (!count($current_roles)) {
       array_unshift($menu, $notify);
    } else {
        foreach ($current_roles as $r) {
            if ($r['name'] == 'SuperAdmin') {
                array_unshift($menu, $notify);
                break;
            }
        }
    }*/
}
/*
$randomset = array('cmd' => 'randomset', 'label' => LangAdmin::get('randomset'), 'active' => null);
array_unshift($menu, $randomset);
*/
foreach ($menu as $key => $item) {
    if ($item['active'] === null) {
        $item['active'] = @$_GET['cmd'] == $item['cmd'];
    }

    $item['url'] = BASE_DIR . 'index.php?sid=' . $GLOBALS['ssid'];

    if (!empty($item['cmd'])) {
        $item['url'] .= '&amp;cmd=' . $item['cmd'];
    }
    if (isset($item['do'])) {
        $item['url'] .= '&amp;do=' . $item['do'];
    }

    $menu[$key] = $item;
}

?>

<form action="index.php" method="post">
    <input type="hidden" id="lang" name="lang" value="" />
    <input type="hidden" id="from-lang" name="from" value="<?=$_SERVER['REQUEST_URI']?>" />
</form>

<script type="text/javascript">
$(function(){
    $('#ru, #en, #zh-chs').click(function(){
        $('#lang').val( $(this).attr('id') );
        $('#lang').closest('form').submit();
        return false;
    });
    $('#<?=$_SESSION['active_lang_admin']?>').wrap('<span class="active" />');
});
<? if (@$_GET['cmd'] != 'login') {?>
function checklogin()
{
    $.ajax({
        url: "index.php?do=checklogin",
    }).done(function ( data ) {
        if (data == 'SessionExpired') location.href='index.php?expired';
    });
}
setInterval('checklogin();', 1000*60*5);
<? } ?>
</script>

<ul id="navigation">
    <li><a href="#" id="ru">Russian</a></li>
    <li><a href="#" id="en">English</a></li>
	<li><a href="#" id="zh-chs">Chinese (中国的)</a></li>
    <li style="clear: both; display: block; height: 10px"> </li>

    <? if (isset($_SESSION['sid']) /*&& defined('BUY_IN_CHINA')*/) { ?>
        <? $menu = Permission::filter_menu($menu); ?>
    <? } ?>

    <? if (isset($_SESSION['sid'])) foreach ($menu as $item) { ?>
        <? if ($item['active']){ ?>
            <li><span class="active"><?= $item['label'] ?></span></li>
        <? } else { ?>
            <li><a href="<?= $item['url'] ?>" <?=isset($item['blank'])?'target="_blank"':''?>><?= $item['label'] ?></a></li>
        <? } ?>
    <? } ?>
</ul>

