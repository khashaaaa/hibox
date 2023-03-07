<?php

class BackgroundActivities extends GeneralUtil
{
    const TYPE_TEST = 'Test';
    const TYPE_PROVIDER_ORDERS_INTEGRATION_EXPORTING = 'ProviderOrdersIntegrationExporting';
    const TYPE_PROVIDER_ORDERS_INTEGRATION_LINKING = 'ProviderOrdersIntegrationLinking';
    const TYPE_PROVIDER_ORDERS_INTEGRATION_UNLINKING = 'ProviderOrdersIntegrationUnlinking';
    const TYPE_PROVIDER_ORDERS_INTEGRATION_SYNCHRONIZING = 'ProviderOrdersIntegrationSynchronizing';
    const TYPE_YANDEX_MARKET_INTAGRATOR_YML_GENERATION = 'YandexMarketIntagratorYmlGeneration';
    const TYPE_AUTO_RATING_LISTS_UPDATING = 'AutoRatingListsUpdating';
    const TYPE_BASKET_CHECKING = 'BasketChecking';
    const TYPE_SELECTOR_EXPORTING = 'SelectorExporting';

    private static function getBackgroundActivities(array $types)
    {
        $result = array();
        try {
            $language =  Session::getActiveAdminLang();
            $sessionId = Session::get('sid');

            $xmlParameters = new SimpleXMLElement('<BackgroundActivitySearchParameters></BackgroundActivitySearchParameters>');
            $orderLineIdsXml = $xmlParameters->addChild('Types');
            foreach ($types as $key => $type) {
                $orderLineIdsXml->addChild('Type', $type);
            }
            $xml = str_replace('<?xml version="1.0"?>','',$xmlParameters->asXML());

            $result = array();
            /** @var OtapiBackgroundActivityInfoListAnswer $answer */
            OTAPILib2::SearchBackgroundActivities($language, $sessionId, $xml, $answer);
            OTAPILib2::makeRequests();

            if ($answer && $answer->GetResult()->GetContent()->GetItem()) {
                $result = $answer->GetResult()->GetContent()->GetItem()->toArray();
            }

        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }
        return $result;
    }

    private static function checkShowAwatingActivities(array $types)
    {
        $checkKey = implode('_',$types);
        $checkKey = 'showAwatingActivities_' . md5($checkKey);

        $showActivityTime = Session::get($checkKey);
        if (strtotime($showActivityTime) > time()) {
            return false;
        }

        $newShowActivityTime = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        Session::set($checkKey, $newShowActivityTime);
        return true;
    }

    public static function showAwatingActivitiesAdmin(array $types)
    {
        try {
            if (!self::checkShowAwatingActivities($types)) {
                return false;
            }
            $htmlMessage = '';

            $allActivities = self::getBackgroundActivities($types);
            $count = 0;
            foreach ($allActivities as $activity) {
                if ($count >= 10) {
                    break;
                }
                if  (!$activity->IsAwaitingAction()) {
                    continue;
                }
                $name = $activity->GetDescription();
                $id = $activity->GetId()->asString();
                $type = $activity->GetType();
                $finished = $activity->IsFinished();
                $htmlMessage .= '<p><a onclick=openActivity("'. $id .'","'. $type .'",null,"'. $finished .'") href="javascript:void(0)" title="'. LangAdmin::get('Open') .'">'. $name .'</a></p>';
                $count++;
            }
            if(!$count) {
                return false;
            }

            $htmlMessage = '<div><p>' . LangAdmin::get('You_have_awating_processes') . '</p>' . $htmlMessage . '</div>';
            AssetsMin::jsBegin();
            $strScript = "<script>$(function() { showMessage('" . $htmlMessage . "',undefined,true);});</script>";
            AssetsMin::registerJs($strScript);
            AssetsMin::registerJsFile('/admin/js/ot-activities.js');
            AssetsMin::registerJsFile('/admin/js/ot-activity.js');
            AssetsMin::jsEnd();

        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }
    }
}