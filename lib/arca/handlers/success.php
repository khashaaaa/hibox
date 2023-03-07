<?php
header('Content-Type: text/html; charset=utf-8');
$GLOBALS['script_start_time'] = microtime(true);
$GLOBALS['trace'] = array();

include(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
include(dirname(dirname(dirname(dirname(__FILE__)))).'/config/config.php');
General::init();

$_SESSION['active_lang_admin'] = @$_SESSION['active_lang_admin'] ? $_SESSION['active_lang_admin'] : 'ru';

if(!isset($_POST['orderID'])){
    header('Location: /?p=paymentfail');
}
else{
    try{
        $A = new Arca();
        $A->onUserPaidSuccess($_POST['orderID']);		

        $cms = new CMS();
        $cms->Check();
		$cRep = new ContentRepository($cms);
        $page = $cRep->GetPageByAlias('arka_success');
        if(!$page){
            $pageId = $cRep->CreatePage(
                array(
                    'alias' => 'arka_success',
                    'title' => Lang::get('made_payment_success'),
                    'lang'  => Session::getActiveLang(),
                    'pagetitle' => '',
                    'seo_keywords' => '',
                    'seo_description' => '',
                )
            );
            $pageInfo = $cRep->GetFullPageById($pageId);
            $cRep->UpdateBlockByID($pageInfo['block_id'], '<p>'.Lang::get('made_payment_success_details').'</p>');
        }

        header('Location: /?p=arka_success');
    }
    catch(Exception $e){
        header('Location: /?p=paymentfail');
    }
}
