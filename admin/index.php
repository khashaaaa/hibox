<?php

header('Content-Type: text/html; charset=utf-8');

chdir('../');
include('config.php');
chdir('admin/');
include('cfg/main.cfg.php');
include('cfg/error.cfg.php');

OTBase::import('system.admin.lib.ErrorHandler');

$request = new RequestWrapper();
$isAjax = $request->isAjax();

if ($request->getValue('authpage') && $request->getValue('lang')) {
    Session::setActiveAdminLang($request->getValue('lang'));
}

require BASE_PATH.'lib/LangAdmin.class.php';
LangAdmin::getTranslations(BASE_ADMIN_PATH.'langs/', Session::getActiveAdminLang(), $request->get('section', false));
Lang::getTranslations('', Session::getActiveAdminLang());

if ($request->getValue('debug')) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

General::init();
General::$siteConf['price_rounding'] = 2; // в админке все цены округляем до 2 знаков

ErrorHandler::init();
try {
    $authenticationListener = new AuthenticationListener();
    Request::handle($request, $authenticationListener);
}
catch (ServiceException  $e) {
    ErrorHandler::registerError($e);
}
catch (Exception  $e) {
    ErrorHandler::registerError($e);
    Session::setError($e->getMessage(), $e->getCode());
}
