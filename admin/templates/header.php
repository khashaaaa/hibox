<?
OTBase::import('system.lib.Assets');
OTBase::import('system.lib.helpers.IDN');

?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <title>☼ <?=LangAdmin::get('Admin_panel')?></title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="<?=LangAdmin::get('Admin_panel')?>">
    <meta name="viewport" content="width=device-width">
    <meta name="referrer" content="always">

    <?=AssetsMin::printCssFilesGroup('admin_vendor.css', '/admin/cfg')?>
    <?=AssetsMin::printCssFilesGroup('admin_ot.css', '/admin/cfg')?>

    <?=AssetsMin::printCssFiles()?>

    <script src="js/vendor/jquery-1.9.1.min.js?<?=CFG_ADMIN_VERSION?>"></script>
    <script src="js/vendor/underscore.js?<?=CFG_ADMIN_VERSION?>"></script>
    <script src="js/vendor/backbone.js?<?=CFG_ADMIN_VERSION?>"></script>
    <script src="js/vendor/bootstrap.min.js?<?=CFG_ADMIN_VERSION?>"></script>

    <?=Plugins::runEvent('onAdminAfterRenderHead');?>
</head>
<body>

<!-- global header -->
<header id="header" class="header fixed">

    <!-- topbar starts -->
    <div class="navbar ot_utility_nav">

        <div class="row-fluid">

            <div class="brand_wrap">
                <a target="_blank" class="brand" href="<?=UrlGenerator::getProtocol()?>://<?=IDN::decodeIDN($_SERVER['SERVER_NAME'])?>" title="<?=LangAdmin::get('Go_to_site')?>"> <?=IDN::decodeIDN($_SERVER['SERVER_NAME'])?> </a>
            </div><!-- .brand_wrap -->

            <ul class="nav">

                <li>
                    <? if (RightsManager::isAvailableCmd('support')) { ?>
                    <!-- Support -->
                    <div class="btn-group">
                        <?php
                            $newOrderTickets = SupportRepositoryNew::getTotalCountNew(true);
                            $newOtherTickets = SupportRepositoryNew::getTotalCountNew(false);

                            $allOrderTickets = SupportRepositoryNew::getTotalCountNotAnswered(true);
                            $allOtherTickets = SupportRepositoryNew::getTotalCountNotAnswered(false);
                        ?>
                        <a class="btn dropdown-toggle btn-small" data-toggle="dropdown" href="?cmd=Support">
                            <i class="icon-envelope-alt"></i><span class="hidden-phone"> <?=LangAdmin::get('Support')?></span>
                            <strong>
                                (<span class="text-success"><?=$newOrderTickets + $newOtherTickets?></span> /
                                <span class="text-error"><?=$allOrderTickets + $allOtherTickets?></span>)
                            </strong>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="?cmd=Support&ticket_new=on&ticket_notanswer=on"><?=LangAdmin::get('Tickets_to_orders')?>
                                    (<span class="text-success"><?=$newOrderTickets?></span> /
                                    <span class="text-error"><?=$allOrderTickets?></span>)
                                </a>
                            </li>

                            <li>
                                <a href="?cmd=Support&do=other&ticket_new=on&ticket_notanswer=on"><?=LangAdmin::get('General_tickets')?>
                                    (<span class="text-success"><?=$newOtherTickets?></span> /
                                    <span class="text-error"><?=$allOtherTickets?></span>)
                                </a>
                            </li>
                        </ul>
                    </div><!-- /Support -->
                    <? } ?>
                </li>

            </ul><!-- /.nav -->

            <ul class="nav pull-right topmenu">

                <li>
                    <!-- help -->
                    <div class="btn-group">
                        <a class="btn dropdown-toggle btn-small" data-toggle="dropdown" href="#">
                            <i class="icon-question-sign"></i><span class="hidden-phone"> <?=LangAdmin::get('Help')?></span>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <? if (General::isSellFree(Session::getActiveAdminLang())) { ?>
                                <li><a href="http://docs.otcommerce.com/pages/viewpage.action?pageId=20973698" target="_blank"><?=LangAdmin::get('Faq')?></a></li>
                                <li><a href="http://docs.otcommerce.com/display/OTDOCS/SellFree" target="_blank"><?=LangAdmin::get('Spravka')?></a></li>
                            <? } else { ?>
                                <li><a href="http://docs.otcommerce.com/pages/viewpage.action?pageId=14155932" target="_blank"><?=LangAdmin::get('Faq')?></a></li>
                                <li><a href="http://<?=(Session::getActiveAdminLang()!='ru'?'en.':'')?>docs.otcommerce.com/" target="_blank"><?=LangAdmin::get('Spravka')?></a></li>
                                <li class="divider"></li>
                                <li>
                                    <a href="<?=CFG_SUPPORT_URL?>" target="_blank">
                                        <?=LangAdmin::get('Contact_support')?>
                                        <i class="icon-external-link"></i>
                                    </a>
                                </li>
                            <? } ?>
                        </ul>
                    </div>
                    <!-- /help -->
                </li>

                <li>
                    <!-- settings dropdown -->
                    <div class="btn-group ot_globall_settings">
                        <a class="btn dropdown-toggle btn-small" data-toggle="dropdown" href="#"><i class="icon-cogs"></i> <span class="hidden-phone"><?=LangAdmin::get('config')?></span> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#" class="cacheClean"><?=LangAdmin::get('Reset_cache')?> <span class="ot-preloader-micro"></span></a></li>
                            <? if (RightsManager::isSuperAdmin() && !General::isSellFree(Session::getActiveAdminLang())) { ?>
                            <li><a href="<?=$PageUrl->AssignCmdAndDo('Update', 'default')?>"><?=LangAdmin::get('Version')?> <span class="label label-info"><?=OTBase::getVersion()?></span></a></li>
                            <? } ?>
                        </ul>
                    </div>
                    <!-- settings dropdown ends -->
                </li>

                <li>
                    <!-- user dropdown -->
                    <div class="btn-group">
                        <? if (General::isSellFree(Session::getActiveAdminLang())) { ?>
                        <a class="btn btn-small" href="index.php?cmd=login&do=logout"><?=LangAdmin::get('Logout')?></a>
                        <? } else { ?>
                        <a class="btn dropdown-toggle btn-small" data-toggle="dropdown" href="#">
                            <i class="icon-user icon-white"></i><span class="hidden-phone">
                            <?
                                $role = RightsManager::getCurrentRole();
                                $roleName = ! empty($role) ? LangAdmin::get($role) : LangAdmin::get('Administrator');
                                echo $roleName;
                            ?>
                            </span>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!--li><a href="#">Личный кабинет</a></li-->

                            <li><a href="index.php?cmd=login&do=logout"><?=LangAdmin::get('Logout')?></a></li>
                        </ul>
                        <? } ?>                        
                    </div>
                    <!-- user dropdown ends -->
                </li>

                <li>
                    <!-- admin interface language -->
                    <div class="btn-group">
                        <a class="btn dropdown-toggle btn-small" data-toggle="dropdown" href="#" title="<?=LangAdmin::get('Language_select_for_admin')?>">
                         <i class="icon-flag icon-white"></i>
                            <span class="currentLang hidden-phone"><?=Session::getActiveAdminLang()?></span>
                            <span class="caret"></span>
                        </a>
                        <? require TPL_ABSOLUTE_PATH . '/page_langs.php'; ?>
                    </div>
                    <!-- /admin interface language -->
                </li>

            </ul>

        </div><!-- /row-fluid -->

    </div><!--/.navbar .ot_utility_nav -->

</header><!-- /.header -->

<div id="hiddenElements" style="display:none">
    <div id="activeLanguages"><?=$activeLanguages?></div>
</div>


<!-- global content -->
<section id="wrapper">

    <div class="row-fluid">

        <!-- span side-left -->
        <div class="span2">

            <!--sidebar-->
            <aside class="side-left fixed">

                <!-- main navigation -->
                <nav>
                    <ul class="ot_main_nav">

                        <? include("inc/main_nav.php"); ?>

                    </ul>
                </nav><!-- /main navigation -->

            </aside><!--/sidebar -->

        </div><!-- span side-left -->


        <!-- span content -->
        <div class="span10">

            <!-- content -->
            <div class="ot_content">

                <!--[if lt IE 7]>
                <div class="alert alert-block">
                    <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
                </div>
                <![endif]-->

                <noscript>
                    <div class="alert alert-block span10">
                        <h4 class="alert-heading">Warning!</h4>
                        <p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a> enabled to use this site.</p>
                    </div>
                </noscript>
