<?php

class TabsGenerator
{
    /**
     * @param string $tabsXMLPath
     * @param AdminUrlWrapper $pageUrl
     * @return string
     */
    public static function GetTabs($tabsXMLPath, $pageUrl)
    {
        return self::generateTabs($tabsXMLPath, $pageUrl, 'ot_sub_nav', 'nav nav-tabs');
    }

    /**
     * @param $tabsXMLPath
     * @param AdminUrlWrapper $pageUrl
     * @return mixed|string
     */
    public static function GetSubTabs($tabsXMLPath, $pageUrl)
    {
        return self::generateTabs($tabsXMLPath, $pageUrl, 'tabbable ot_sub_sub_nav', 'nav nav-pills');
    }

    private static function generateTabs($tabsXMLPath, $pageUrl, $cssMunuClass, $cssUlClass)
    {
        if (! file_exists($tabsXMLPath)) {
            return "File $tabsXMLPath does not exist";
        }

        $tabs = simplexml_load_file($tabsXMLPath);

        $menu = new SimpleXMLElement('<div></div>');
        $menu['class'] = $cssMunuClass;
        $ul = $menu->addChild('ul');
        $ul['class'] = $cssUlClass;

        foreach ($tabs->tab as $tab) {
            if (isset($tab['feature'])) {
                $tabFeatures = explode(',', (string)$tab['feature']);
                $featureEnabled = false;
                foreach ($tabFeatures as $feature) {
                    if (! CMS::IsFeatureEnabled($feature)) {
                        continue;
                    }
                    if (! RightsManager::isFeatureAvailable($feature)) {
                        continue;
                    }
                    $featureEnabled = true;
                    break;
                }
                if (! $featureEnabled) {
                    continue;
                }
            }
            if (isset($tab['hidesellfree']) && (string)$tab['hidesellfree'] == 'true' && General::isSellFree(Session::getActiveAdminLang())) {
                continue;
            }
            if (isset($tab['onlysellfree']) && (string)$tab['onlysellfree'] == 'true' && !General::isSellFree(Session::getActiveAdminLang())) {
                continue;
            }
            if (! RightsManager::isAvailableCmd((string)$tab['cmd'], (string)$tab['action'])) {
                $isAvailable = false;
                if (isset($tab['sub-tabs'])) {
                    foreach (simplexml_load_file($tab['sub-tabs']) as $subTab) {
                        if (RightsManager::isAvailableCmd((string)$subTab['cmd'], (string)$subTab['action'])) {
                            $tab['cmd'] = $subTab['cmd'];
                            $tab['action'] = $subTab['action'];
                            $isAvailable = true;
                            break;
                        }
                    }
                }
                if ($isAvailable === false) {
                    continue;
                }
            }

            $isActive = $tab->xpath('page[@data-action="' . $pageUrl->GetAction($pageUrl->GetCmd()) . '" and @data-cmd="' . strtolower($pageUrl->GetCmd()) . '"]');
            if (!$isActive)
                $isActive = $tab->xpath('page[@data-action="*" and @data-cmd="' . strtolower($pageUrl->GetCmd()) . '"]');

            $li = $ul->addChild('li');
            if ($isActive) {
                $li['class'] = 'active';
            }

            $a = $li->addChild('a', LangAdmin::get((string)$tab['title']));
            if (empty($tab['disabled'])) {
                $generateParams = array();
                $generateParams['cmd'] = (string)$tab['cmd'];
                $generateParams['do'] = (string)$tab['action'];

                if (isset($tab['get-params'])) {
                    $getParams = explode(';', $tab['get-params']);
                    foreach ($getParams as $key => $val) {
                        list($curKey, $curVal) = explode('=', $val);
                        $generateParams[$curKey] = $curVal;
                    }
                }

                $a['href'] = $pageUrl->generate($generateParams);
            } else {
                $li['class'] = !empty($li['class']) ? $li['class'] . ' disabled' : 'disabled';
            }
        }

        return $menu->asXML();
    }

}