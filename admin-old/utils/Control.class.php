<?php

class Control {

    private $error = '';

    private function _getCurrencyXML() {
        $xmlParams = '<CurrencySettings>';
        $xmlParams .=  "<SyncMode>{$_POST['sync']}</SyncMode>";

        if(isset($_POST['MarginRate']) && $_POST['MarginRate'] !== ''){
            $margin = (float)str_replace(array(',', ' '),array('.', ''),$_POST['MarginRate']);
            $marginProportion = round($margin/100+1, 3);

            $xmlParams .=  "<MarginRate>{$marginProportion}</MarginRate>";
        }

        if(isset($_POST['rate'])) {
            $count = count($_POST['rate']['value']);
            $xmlParams .= '<CurrencyRateList>';

            for($i=0; $i<$count; $i++) {
                $currency_xml =  new SimpleXMLElement('<CurrencyRate>'.$_POST['rate']['value'][$i].'</CurrencyRate>');
                $currency_xml->addAttribute('FirstCode', @htmlspecialchars(@$_POST['rate']['firstcode'][$i]));
                $currency_xml->addAttribute('SecondCode', @htmlspecialchars(@$_POST['rate']['secondcode'][$i]));

                $xmlParams .= str_replace('<?xml version="1.0"?>', '', $currency_xml->asXML());
            }
            $xmlParams .= '</CurrencyRateList>';
        }

        $order = 0;
        $xmlParams .= '<CurrenciesDisplayingOrder>';

        foreach(@$_POST['currency_order'] as $key=>$currency) {
            if($currency == '') continue;
            $currency_xml =  new SimpleXMLElement('<OrderedCurrency></OrderedCurrency>');
            $currency_xml->addAttribute('Code', $currency);
            $currency_xml->addAttribute('Order', $order);
            $xmlParams .= str_replace('<?xml version="1.0"?>', '', $currency_xml->asXML());

            $order++;
        }
        $xmlParams .= '</CurrenciesDisplayingOrder>';
        $xmlParams .= '</CurrencySettings>';

        return $xmlParams;
    }


    function rrmdir($dir, $removeDir = true){
        if (is_dir($dir)) {
            $d = dir($dir);
            while($entry = $d->read()) {
                if ($entry != '.' && $entry != '..') {
                    if (is_dir($dir.'/'.$entry)) {
                        $this->rrmdir($dir.'/'.$entry);
                    } else {
                        unlink($dir.'/'.$entry);
                    }
                }
            }
            $d->close();

            if($removeDir)
                rmdir($dir);
        }
    }

    function defaultAction() {
        if (Login::auth()) {

            list($data, $searchSettings, $instanceOptionsInfo, $siteConfig) = $this->_getMainSettings();

            $cms = new CMS();
            $status = $cms->Check();
            if($status){
            $cms->checkTable('site_langs');
                foreach($data->Settings->Languages->NamedProperty as $v){
                    $lang = (string)$v->Name;
                    $lang_desc = (string)$v->Description;
                    $cms->checkLanguage($lang, $lang_desc);
                }
            }

            $usedSearchSettings = unserialize(General::getConfigValue('orderSearchMethods', $searchSettings));
            $unUsedSearchSettings = unserialize(General::getConfigValue('orderUnUsedSearchMethods'));
            $searchSettings = unserialize($searchSettings);

            $newSearchSettings = array();
            foreach ($searchSettings as $type) {
                if ((! in_array($type, (array)$usedSearchSettings)) and (! in_array($type, (array)$unUsedSearchSettings)))
                    $newSearchSettings[] = $type;
            }
            if (isset($newSearchTypes[0]))
                $usedSearchSettings = array_merge((array)$usedSearchSettings, (array)$newSearchSettings);

            foreach($usedSearchSettings as &$searchType){
                $searchType = str_replace('Taobao_Extended', 'China_Other', $searchType);
                $searchType = str_replace('Taobao', 'China', $searchType);
            }
            if (is_array($unUsedSearchSettings)) {
                foreach($unUsedSearchSettings as &$searchType){
                    $searchType = str_replace('Taobao_Extended', 'China_Other', $searchType);
                    $searchType = str_replace('Taobao', 'China', $searchType);
                }
            }

            $available_langs = $this->GetAvailableLangs($cms);

            include(TPL_DIR.'index.php');
        } else {
            include(TPL_DIR.'login.php');
        }
    }

    function caseAction() {
        if (Login::auth()) {
            global $otapilib;
            $sid = @$_SESSION['sid'];
            $lang = 'RU';

            $data = $this->_getCaseSettings();
            //var_dump($data);
            $currency_list = $otapilib->GetCurrencyList($sid);
            $currency_settings = $otapilib->GetInstanceCurrenciesSettings($sid);
            $sync_model_list = $otapilib->GetCurrencySynchronizationModeList($sid);
            $round_settings = $otapilib->GetPriceFormationSettings($sid);

            include(TPL_DIR.'case.php');
        } else {
            include(TPL_DIR.'/login.php');
        }
    }

    function checkloginAction() {
        if (Login::auth()) {
            global $otapilib;
            $sid = @$_SESSION['sid'];
            $result = $otapilib->GetWebUISettings($sid);
            if ($otapilib->error_message == 'SessionExpired')
            {
                die('SessionExpired');
            }
            die('Ok');
        } else {
            die('SessionExpired');
        }
    }

    /*
     *
     * Работа с группами ЦО
     *
     */
    function pricingAction() {
        if (Login::auth()) {
            global $otapilib;
            $sid = @$_SESSION['sid'];
            $error = '';

            $webui = $otapilib->GetWebUISettings($sid);
            $cats = $otapilib->GetRootCategoryInfoList();
            $round_settings = $otapilib->GetPriceFormationSettings($sid);
            //var_dump($round_settings);
            $hiddenstat = 0;
            if (isset($_GET['tab'])) $tab_number = $_GET['tab'];
            if (isset($_GET['addgroup'])) {
                $new_group = $this->_addPriceGroup();
                if ($new_group) {
                    if (isset($_POST['default_group'])) {
                        $this->_setDefaultGroup((string)$new_group);
                    }
                    header('Location: index.php?sid=&do=pricing&tab=2');
                }
            } elseif (isset($_GET['editgroup'])) {
                $this->_editPriceGroup();
                if (isset($_POST['default_group'])) {
                    $this->_setDefaultGroup($_POST['id']);
                }
            } elseif (isset($_GET['groupcategory'])) {
                $group_categories = $otapilib->GetCategoriesOfPriceFormationGroup($sid, @$_GET['gid']);
                $tab_number = 1;
                $action = 'groupcategory';
            } elseif (isset($_GET['group'])) {
                $tab_number = 2;
                $action = 'edit';
                $group_info = $otapilib->GetPriceFormationGroup($sid, @$_GET['gid']);
            }

            $group_id = (isset($_GET['gid'])) ? $_GET['gid'] : 0;

            $price_groups = $otapilib->GetPriceFormationGroupList($sid);
            if (!$price_groups) {
                $error.= $otapilib->error_message;
            }

            $strategy_list = $otapilib->GetPriceFormationStrategyList($sid);
            if (!$strategy_list) {
                $error.= $otapilib->error_message;
            }
            $error.= $this->error;

            include(TPL_DIR.'pricing/main.php');
        } else {
            include(TPL_DIR.'/login.php');
        }
    }

    function setroundsettingsAction () {

        global $otapilib;
        @define('NO_DEBUG', true);
        $sid = $_SESSION['sid'];
        $round = $_GET['round'];
        if (isset($_GET['round_cfg'])) {
                 $cfg_r = 'true';
        } else {
                $cfg_r = 'false';
        }
        $xml = "<EditablePriceFormationSettings PriceRoundingFactor='".$round."' RoundOriginalInternalDeliveryPrice='".$cfg_r."' />";

        $r = $otapilib->EditPriceFormationSettings($sid, $xml);
        if ($otapilib->error_message == 'SessionExpired') {
            print "RELOGIN";
            die;
        }
        if(!$r) print $otapilib->error_message;
        else print 'Ok';
        die;
    }

    function addcategoryAction() {
        global $otapilib;
        @define('NO_DEBUG', true);
        $sid = $_SESSION['sid'];
        $gid = $_GET['gid'];
        $cid = $_GET['cid'];

        $r = $otapilib->SetPriceFormationGroupToCategory($sid, $cid, $gid);

        if(!$r) print $otapilib->error_message;
        else print 'Ok';
        die;
    }

    function deletecategoryAction() {
        global $otapilib;
        @define('NO_DEBUG', true);
        $sid = $_SESSION['sid'];
        $gid = $_GET['gid'];
        $cid = $_GET['cid'];

        $r = $otapilib->RemoveCategoryFromPriceFormationGroup($sid, $cid, $gid);

        if(!$r) print $otapilib->error_message;
        else print 'Ok';

        die;
    }


    function _addPriceGroup() {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $error = '';

        $xmlParams = '<PriceFormationGroupInfo Name="'.$_POST['name'].
                '" Description="'.$_POST['desc'].'" StrategyType="'.$_POST['strategy'].'">';
        if (@$_POST['id']) $xmlParams .= '<Id>'.@$_POST['id'].'</Id>';

        $settings  = '<Settings>';
        if ($_POST['delivery_all'] != '') {
            $settings .= '<InternalDeliveryPrice>'.$_POST['delivery_all'].'</InternalDeliveryPrice>';
        }
        $settings .= '<PriceFormationIntervals>';
        for ($i=0; $i<count($_POST['margin']); $i++) {
            $settings .= '<PriceFormationIntervalInfo>';
            if ($_POST['margin_type'][$i][0] == 'persent') {
                $settings .= '<MarginPercent>'.(($_POST['margin'][$i]/100)+1).'</MarginPercent>';
                //$settings .= '<MarginFixed></MarginFixed>';
            } elseif ($_POST['margin_type'][$i][0] == 'fixed') {
                //$settings .= '<MarginPercent></MarginPercent>';
                $settings .= '<MarginFixed>'.$_POST['margin_fixed'][$i].'</MarginFixed>';
            }
            $settings .= '<MinimumLimit>'.$_POST['limit'][$i].'</MinimumLimit>';
            if($_POST['delivery'][$i] !== '')
                $settings .= '<InternalDeliveryPrice>'.$_POST['delivery'][$i].'</InternalDeliveryPrice>';
            $settings .= '</PriceFormationIntervalInfo>';
        }
        $settings .= '</PriceFormationIntervals>';
        $settings .= '</Settings>';

        if ($settings) $xmlParams .=  $settings;
        $xmlParams .= '</PriceFormationGroupInfo>';

        //echo ' $xmlParams='.$xmlParams;
        $r = $otapilib->AddPriceFormationGroup($sid, $xmlParams);

        if (!$r) {
            $this->error .= $otapilib->error_message;
        }

        return $r;
    }

    function _editPriceGroup() {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $error = '';

        $xmlParams = '<PriceFormationGroupInfo Name="'.$_POST['name'].
                '" Description="'.$_POST['desc'].'" StrategyType="'.$_POST['strategy'].'">';
        if (@$_POST['id']) $xmlParams .= '<Id>'.@$_POST['id'].'</Id>';

        $settings  = '<Settings>';
        if ($_POST['delivery_all'] != '') {
            $settings .= '<InternalDeliveryPrice>'.$_POST['delivery_all'].'</InternalDeliveryPrice>';
        }
        $settings .= '<PriceFormationIntervals>';
        //var_dump(@$_POST);die;
        for ($i=0; $i<count($_POST['limit']); $i++) {
            $id = (isset($_POST['interval_id'][$i]) && $_POST['interval_id'][$i]!='') ? ' Id="'.$_POST['interval_id'][$i].'"' : '';
            $settings .= '<PriceFormationIntervalInfo '.$id.'>';

            foreach (@$_POST['margin_type'][$i] as $interval_id=>$value) {
                if ($value == 'persent') {
                    $settings .= '<MarginPercent>'.((@$_POST['margin'][$i]/100)+1).'</MarginPercent>';
                    //$settings .= '<MarginFixed></MarginFixed>';
                } elseif ($value == 'fixed') {
                    //$settings .= '<MarginPercent></MarginPercent>';
                    $settings .= '<MarginFixed>'.@$_POST['margin_fixed'][$i].'</MarginFixed>';
                }
            }

            $settings .= '<MinimumLimit>'.$_POST['limit'][$i].'</MinimumLimit>';
            //settype($_POST['delivery'][$i], (string));
            if ($_POST['delivery'][$i] !== '') $settings .= '<InternalDeliveryPrice>'.$_POST['delivery'][$i].'</InternalDeliveryPrice>';
            $settings .= '</PriceFormationIntervalInfo>';
        }
        $settings .= '</PriceFormationIntervals>';
        $settings .= '</Settings>';
        //echo '$settings = '.$settings; die;
        if ($settings) $xmlParams .=  $settings;
        $xmlParams .= '</PriceFormationGroupInfo>';

        $r = $otapilib->EditPriceFormationGroup($sid, @$_POST['id'], $xmlParams);

        if (!$r) {
            $this->error .= $otapilib->error_message;
        }
        return $r;
    }

    function _setDefaultGroup($group_id) {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $r = $otapilib->SetDefaultPriceFormationGroup($sid, $group_id);
    }

    function _editGroupCategory() {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $gid = @$_SESSION['gid'];
        $error = '';

        return $r;
    }


    public function deletegroupAction() {
        global $otapilib;
        @define('NO_DEBUG', true);
        $sid = @$_SESSION['sid'];
        $groupid = @$_GET['id'];

        $r = $otapilib->RemovePriceFormationGroup($sid, $groupid);

        if(!$r) print $otapilib->error_message;
        else print 'Ok';

        die;
    }

    function clearCacheAction(){
        global $otapilib;
        $otapilib->ResetInstanceCaches();
        $this->rrmdir(dirname(dirname(dirname(__FILE__))).'/cache/', false);
        header('Location: index.php');
    }

    function savesettAction() {
        if (Login::auth()) {
            $this->clearCacheAction();
            $settings = '<Settings>';
            if (isset($_POST['translate_type'])){
                $settings.='<SelectedTranslateType>'.$_POST['translate_type'].'</SelectedTranslateType>';
            }


            if (isset($_POST['UsedLanguages'])){
                $langs = json_decode(stripslashes($_POST['UsedLanguages']));
                $json = '';
                if(is_array($langs)){
                    foreach($langs as $lang){
                        if (is_null($lang)) break;
                        $json.='<string>'.htmlspecialchars($lang).'</string>';
                    }
                }
                $settings.='<UsedLanguages>'.$json.'</UsedLanguages>';
                //UsedSystemLanguages
                $langs = array_reverse($langs);
                $json = '';
                if(is_array($langs)){
                    foreach($langs as $lang){
                        if (is_null($lang)) break;
                        $json.='<string>'.htmlspecialchars($lang).'</string>';
                    }
                }
                $settings.='<UsedSystemLanguages>'.$json.'</UsedSystemLanguages>';

            }

            if (isset($_POST['ExternalDeliveryRegionId'])){
                $settings.='<ExternalDeliveryRegionId>'.$_POST['ExternalDeliveryRegionId'].'</ExternalDeliveryRegionId>';
                $settings.='<ExternalDeliveryRegionName>'.$_POST['ExternalDeliveryRegionName'].'</ExternalDeliveryRegionName>';
            }

            if (isset($_POST['StructureType'])){
                $settings.='<SelectedCategoryStructureType>'.$_POST['StructureType'].'</SelectedCategoryStructureType>';
            }
            $settings .= '</Settings>';

            global $otapilib;
            $sid = $_SESSION['sid'];
            $result = $otapilib->SetWebUISettings($sid, $settings);
            if ($otapilib->error_message == 'SessionExpired')
            {
                header('Location: index.php?expired');
                die;
            }

            header('Location: index.php?cmd=Control');
            include(TPL_DIR.'index.php');
        } else {
            include(TPL_DIR.'/login.php');
        }
    }

    function saveSearchMethodsAction() {
        if (Login::auth()) {
            $usedSearchMethods = json_decode($_POST['usedMethods']);
            $unUsedSearchMethods = json_decode($_POST['unUsedMethods']);

            foreach($usedSearchMethods as &$searchType){
                $searchType = str_replace('China_Other', 'Taobao_Extended', $searchType);
                $searchType = str_replace('China', 'Taobao', $searchType);
            }
            if (is_array($unUsedSearchMethods)) {
                foreach($unUsedSearchMethods as &$searchType){
                    $searchType = str_replace('China_Other', 'Taobao_Extended', $searchType);
                    $searchType = str_replace('China', 'Taobao', $searchType);
                }
            }

            $newPparam  = array();
            if (is_array($usedSearchMethods)) {
                $newPparam['orderSearchMethods'] = serialize($usedSearchMethods);
            }
            if (is_array($unUsedSearchMethods)) {
                $newPparam['orderUnUsedSearchMethods'] = serialize($unUsedSearchMethods);
            }
            $cms = new CMS();
            $cms->saveSiteConfig($newPparam);
            $fileMysqlMemoryCache = new FileAndMysqlMemoryCache($cms);
            $fileMysqlMemoryCache->DelCacheEl('GetProviderSearchMethodInfoList:id');

            header('Location: index.php');
            die;
        } else {
            include(TPL_DIR.'/login.php');
        }
    }

    function savecaseAction() {
        if (Login::auth()) {

            $settings = '<Settings>';

            if (isset($_POST['DeliveryTypes'])){
                $dels = json_decode(stripslashes($_POST['DeliveryTypes']));
                $json = '';
                if(is_array($dels)){
                    foreach($dels as $del){
                        $json.='<DeliveryTypes>'.  htmlspecialchars($del).'</DeliveryTypes>';
                    }
                }
                $settings.='<DeliveryTypes>'.$json.'</DeliveryTypes>';
            }

            if (isset($_POST['ExternalDeliveryRegionId'])){
                $settings.='<ExternalDeliveryRegionId>'.$_POST['ExternalDeliveryRegionId'].'</ExternalDeliveryRegionId>';
                $settings.='<ExternalDeliveryRegionName>'.$_POST['ExternalDeliveryRegionName'].'</ExternalDeliveryRegionName>';
            }

            if($_POST['persent'] !== "")
                $settings .= '<MarginPercentage>'.floatval($_POST['persent']).'</MarginPercentage>';

            if($_POST['minimummargin'] !== "")
                $settings .= '<MinimumMargin>'.floatval($_POST['minimummargin']).'</MinimumMargin>';

            if(isset($_POST['usediscount'])) {
                $settings .= '<UseDiscount>true</UseDiscount>';
            } else {
                $settings .= '<UseDiscount>false</UseDiscount>';
            }

            if(isset($_POST['usevipdiscount'])) {
                $settings .= '<UseVipDiscount>true</UseVipDiscount>';
            } else {
                $settings .= '<UseVipDiscount>false</UseVipDiscount>';
            }
            /*if(isset($_POST['cbvalue'])) {
                $settings .= '<IsSinchroCB>true</IsSinchroCB>';
            } else {
                $settings .= '<IsSinchroCB>false</IsSinchroCB>';
            }*/

            $sellDenies = array(
                'IsAuctionTypeItemSellAllowed',
                'IsNotDeliverableItemSellAllowed',
                'IsSecondhandItemSellAllowed',
                'IsFilteredItemsSellAllowed'
            );
            foreach($sellDenies as $deny){
                if(@$_POST[$deny])
                    $settings .= '<'.$deny.'>true</'.$deny.'>';
                else
                    $settings .= '<'.$deny.'>false</'.$deny.'>';
            }

            $settings .= '</Settings>';
            //echo $settings; die;
            global $otapilib;
            $sid = $_SESSION['sid'];

            $result = $otapilib->SetShowcaseSettings($sid, $settings);
            if ($otapilib->error_message == 'SessionExpired')
            {
                header('Location: index.php?expired');
                die;
            }

            if(isset($result->ErrorCode))
                $message = $result->ErrorCode;

            $data = $this->_getCaseSettings();

            header('Location: index.php?do=case');
            die();
        } else {
            include(TPL_DIR.'/login.php');
        }
    }

    function savecurrencyAction() {
        if (Login::auth())
        {
            global $otapilib;
            $sid = $_SESSION['sid'];
            $result = $otapilib->GetWebUISettings($sid);
            if ($otapilib->error_message == 'SessionExpired')
            {
                header('Location: index.php?expired');
                die;
            }
            $xmlParams = $this->_getCurrencyXML();

            $r = $otapilib->UpdateInstanceCurrenciesSettings($sid, $xmlParams);

            header('Location:index.php?sid=&do=case');
        } else {
            include(TPL_DIR.'/login.php');
        }
    }

    function deleterateAction() {
        global $otapilib;
        @define('NO_DEBUG', true);
        $sid = $_SESSION['sid'];
        $first = $_GET['first'];
        $second = $_GET['second'];

        $r = $otapilib->RemoveCurrencyRate($sid, $first, $second);

        if(!$r) print $otapilib->error_message;
        else print 'Ok';

        die;
    }

    function createrateAction() {
        global $otapilib;
        @define('NO_DEBUG', true);
        $sid = $_SESSION['sid'];
        $first = $_GET['first'];
        $second = $_GET['second'];
        $rate = $_GET['rate'];

        $r = $otapilib->AddCurrencyRate($sid, $first, $second, $rate);

        if(!$r) print $otapilib->error_message;
        else print 'Ok';

        die;
    }


    function savecbAction(){
        if (Login::auth()) {
            $settings = '<Settings>';
            if(isset($_POST['cbvalue'])) {
                $settings .= '<IsSinchroCB>true</IsSinchroCB>';
            } else {
                $settings .= '<IsSinchroCB>false</IsSinchroCB>';
            }
            $settings .= '</Settings>';

            global $otapilib;
            //$sid = (string)$GLOBALS['ssid'];
            $sid = $_SESSION['sid'];
            $result = $otapilib->SetShowcaseSettings($sid, $settings);
            if ($otapilib->error_message == 'SessionExpired')
            {
                header('Location: index.php?expired');
                die;
            }

            if(isset($result->ErrorCode))
                $message = $result->ErrorCode;

            $data = $this->_getCaseSettings();
            //var_dump($data);
            include(TPL_DIR.'case.php');


        } else {
            include(TPL_DIR.'login.php');
        }
    }

    function ordersAction() {

        if (Login::auth()) {
            include(TPL_DIR.'orders.php');
        } else {
            include(TPL_DIR.'/login.php');
        }
    }

    public function regionsAction(){
        global $otapilib;

        @define('NO_DEBUG', 1);
        if (Login::auth()) {
            $parent = @$_GET['root'];
            if($parent=='source'){
                $root_regions = $otapilib->GetRootAreaList();
                $json_regions = array();
                foreach($root_regions as $reg){
                    $zip = $reg['Zip'] ? '('.$reg['Zip'].')' : '';
                    $json_regions[] = array('text'=>$reg['Id'].' '.$reg['Name'].' '.$zip.' <a href="#" class="region-select" regid="'.$reg['Id'].'" regname="'.$reg['Name'].'">'.LangAdmin::get('choose').'</a>', 'id'=>$reg['Id'], 'expanded'=>false, 'hasChildren' => true);
                }
                print json_encode($json_regions);
            }
            else{
                $root_regions = $otapilib->GetSubAreaList($parent, 1);
                $json_regions = array();
                foreach($root_regions as $reg){
                    $zip = $reg['Zip'] ? '('.$reg['Zip'].')' : '';
                    $json_regions[] = array('text'=>$reg['Id'].' '.$reg['Name'].' '.$zip.' <a href="#" class="region-select" regid="'.$reg['Id'].'" regname="'.$reg['Name'].'">'.LangAdmin::get('choose').'</a>', 'id'=>$reg['Id'], 'expanded'=>false, 'hasChildren' => true);
                }
                print json_encode($json_regions);
            }
        } else {
            print 'AuthError';
        }
    }

    private function _generateCurrencyFields()
    {
        $xmlParams = new SimpleXMLElement('<CurrencyRateList><CurrencyRateList>');
        if (@$_POST['DeliveryTrackingNum']) $xmlParams->addChild('DeliveryTrackingNum', @htmlspecialchars(@$_POST['DeliveryTrackingNum']));

        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }

    private function _getMainSettings(){
        global $otapilib;
        //$sid = ''.$GLOBALS['ssid'];
        $sid = $_SESSION['sid'];
        $result = $otapilib->GetWebUISettings($sid);
        $searchMethods = $otapilib->GetProviderSearchMethodInfoList();
        $instanceOptionsInfo = $otapilib->GetInstanceOptionsInfo($sid);

        $cms = new CMS();
        $status = $cms->Check();
        if($status){
            $cms->checkTable('site_config');
            $resConf = $cms->getSiteConfig();
        }
        $newSearchMethods = array();
        foreach($searchMethods as $method){
            $newSearchMethods[] = $method['Provider'].'_'.$method['SearchMethod'];
        }

        if ($otapilib->error_message == 'SessionExpired')
        {
            header('Location: index.php?expired');
            die;
        }
        return array($result, serialize($newSearchMethods), $instanceOptionsInfo, @$resConf);
    }

    private function _getCaseSettings(){
        global $otapilib;
        //$sid = ''.$GLOBALS['ssid'];
        $sid = @$_SESSION['sid'];
        $result = $otapilib->GetShowcase($sid);
        if ($otapilib->error_message == 'SessionExpired')
        {
            header('Location: index.php?expired');
            die;
        }
        return $result;
    }

    // страница смены пароля в админке
    function changeOperatorPasswordAction()
    {
        if (Login::auth()) {

            list($data, $searchSettings, $instanceOptionsInfo, $siteConfig) = $this->_getMainSettings();

            $cms = new CMS();
            $status = $cms->Check();
            if($status){
            $cms->checkTable('site_langs');
                foreach($data->Settings->Languages->NamedProperty as $v){
                    $lang = (string)$v->Name;
                    $lang_desc = (string)$v->Description;
                    $cms->checkLanguage($lang, $lang_desc);
                }
            }

            include(TPL_DIR.'change_operator_password.php');
        } else {
//            include(TPL_DIR.'/login.php');
        }
    }

    // смена пароля
    function ajaxChangeOperatorPasswordAction()
    {
        global $otapilib;
        @define('NO_DEBUG', '1');
        $error = 0;
        $data = '';

        $currentPassword = '';
        if (isset($_POST['password_old'])) {
            $currentPassword = $_POST['password_old'];
        }
        $newPassword = '';
        if (isset($_POST['password_new'])) {
            $newPassword = $_POST['password_new'];
        }

        $sid = $_SESSION['sid'];
        $result = $otapilib->ChangeOperatorPassword($sid, $currentPassword, $newPassword);
        if ($result) {
            $error = 1;
            $data = $newPassword;
        } else {
            // обработка ошибок
//            $data = $otapilib->error_message;
            switch ($otapilib->error_message) {
                case 'Wrong current password':
                    $data = LangAdmin::get('this_password_incorrect');
                    break;
                case 'Password must be provided':
                    $data = LangAdmin::get('password_must_be_provided');
                    break;
                case 'Minimum password length is 6 characters':
                    $data = LangAdmin::get('password_min_length_6');
                    break;

                default:
                    $data = $otapilib->error_message;
                    break;
            }
        }

        echo '{"error":' . $error . ',"data":"' . $data . '"}';
    }

    public function redirectAction(){
        $url = $_GET['address'];
        print "<html>
        <meta http-equiv='refresh' content='3; $url'
        <head></head>
        <body>Выполняется переадресация</body>
</html>";
    }

    function GetAvailableLangs($cms) {
        $sdir = array();
        $langs = array();
        // получим все файлы из дирректории
        if (false !== ($files = scandir('../langs/', 0))) {
            foreach ($files as $i => $entry) {
                // если имя файла подходит под маску поика
                if ($entry != '.' && $entry != '..' && fnmatch('*.xml', $entry)) {
                    $names = explode(".", $entry);
                    $sdir[] = $names[0];
                }
            }
        }
        foreach ($sdir as $code) {
            $tmp['name'] = $cms->getLangDescrByCode($code);
            $tmp['code'] = $code;
            $langs[] = $tmp;
        }
        return  $langs;
    }

    function get_files($path, $order = 0, $mask = '*') {
        $sdir = array();
        // получим все файлы из дирректории
        if (false !== ($files = scandir($path, $order))) {
            foreach ($files as $i => $entry) {
                // если имя файла подходит под маску поика
                if ($entry != '.' && $entry != '..' && fnmatch($mask, $entry))
                {
                              $sdir[] = $entry;
                }
            }
        }
    return ($sdir);
}
}