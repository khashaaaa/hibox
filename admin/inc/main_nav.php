<?php

function getItemUrlByRights($cmdArray)
{
    $url = new AdminUrlWrapper();
    $url->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

    $href = false;
    foreach ($cmdArray as $cmd) {
        if (RightsManager::isAvailableCmd($cmd)) {
            $href = $url->AssignClearCmd($cmd);
            break;
        }
    }
    return $href;
}

$request = new RequestWrapper();
$path = RightsManager::defaultPath();
$cmd = $request->getValue('cmd') ? ucfirst($request->getValue('cmd')) : $path['cmd'];
$action = $request->getValue('do') ? $request->getValue('do') : $path['do'];
$url = new AdminUrlWrapper();
$url->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

$adminStructure = simplexml_load_file(dirname(dirname(__FILE__)) . '/cfg/admin_structure.xml');
$currentAdminPartXPath = $adminStructure->xpath('//part/page[@cmd="'.strtolower($cmd).'" and (@do="*" or @do="'.$action.'")]/..');
$currentAdminPart = (string)$currentAdminPartXPath[0]['name'];
?>

<? $href = getItemUrlByRights(array('Orders'))?>
<? if ($href) { ?>
<li data-route="orders"">
    <a href="<?=$href?>">
        <i class="icon-shopping-cart"></i><?=LangAdmin::get('Orders')?>
    </a>
</li>
<? } ?>

<? $href = getItemUrlByRights(array('Pricing'))?>
<? if ($href) { ?>
<li data-route="pricing"">
    <a href="<?=$href?>">
        <i class="icon-usd"></i><?=LangAdmin::get('Pricing')?>
    </a>
</li>
<? } ?>

<? $href = getItemUrlByRights(array('Promo','Referral','Newsletters'))?>
<? if ($href) { ?>
<li data-route="promo">
    <a href="<?=$href?>"><i class="icon-flag"></i><?=LangAdmin::get('Seo')?></a>
</li>
<? } ?>

<? $href = getItemUrlByRights(array('Contents','Blog'))?>
<? if ($href) { ?>
<li data-route="contents">
    <a href="<?=$href?>">
        <i class="icon-file"></i><?=LangAdmin::get('Contents')?>
    </a>
</li>
<? } ?>

<? $href = getItemUrlByRights(array('Categories','Sets','Reviews','Brands','Pristroy','Warehouse','Restrictions','Items'))?>
<? if ($href) { ?>
<li data-route="catalog">
    <a href="<?=$href?>">
        <i class="icon-list-alt"></i><?=LangAdmin::get('Catalog')?>
    </a>
</li>
<? } ?>

<? $href = getItemUrlByRights(array('Users'))?>
<? if ($href) { ?>
<li data-route="users">
    <a href="<?=$href?>">
        <i class="icon icon-black icon-group"></i><?=LangAdmin::get('Users')?>
    </a>
</li>
<? } ?>

<? $href = getItemUrlByRights(array('SiteConfiguration','Shipment','ProviderConfiguration','LettersTemplates'))?>
<? if ($href) { ?>
    <li data-route="site_configuration">
        <a href="<?=$href?>">
            <i class="icon-wrench"></i><?=LangAdmin::get('Configuration')?>
        </a>
    </li>
<? } ?>

<? $href = getItemUrlByRights(array('Reports'))?>
<? if ($href) { ?>
<li data-route="reports">
    <a href="<?=$href?>">
        <i class="icon-bar-chart"></i><?=LangAdmin::get('Reports')?>
    </a>
</li>
<? } ?>

<? $href = getItemUrlByRights(array('PluginsUtil'))?>
<? if ($href) { ?>
    <li data-route="pluginsutil">
        <a href="<?=$href?>">
            <i class="icon-cog"></i><?=LangAdmin::get('Plugins')?>
        </a>
    </li>
<? } ?>

<?=Plugins::invokeEvent('onAdminNewMainMenuRender', array('url' => $url))?>

<script>
    $('[data-route="<?=$currentAdminPart?>"]').addClass('active');
</script>
