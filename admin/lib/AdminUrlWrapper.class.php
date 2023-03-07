<?php
class AdminUrlWrapper extends UrlWrapper
{
    public function GetAction($cmd = null){
        if ($this->GetKey('do')) {
            $do = $this->GetKey('do');
        } elseif (!is_null($cmd)) {
            $path = RightsManager::defaultPath($cmd);
            $do = $path['do'];
        } else {
            $do = 'default';
        }
        return $do;
    }

    public function GetCmd(){
        return $this->GetKey('cmd') ? $this->GetKey('cmd') : 'Orders';
    }

    public function AssignDo($doAction, $cleanUrl = true)
    {
        if ($cleanUrl) {
            $url = $this->generate(array('do' => $doAction));
        } else {
            $url = $this->SetValueReadOnly('do', $doAction);
        }
        return $url;
    }

    public function AssignCmd($cmdAction){
        return $this->SetValueReadOnly('cmd', $cmdAction);
    }

    public function generate(array $params = array(), $cleanUrl = true)
    {
        $tempUrl = new UrlWrapper();
        $tempUrl->Set($this->Get());
        $current = array_filter(array(
            'cmd' => $tempUrl->GetKey('cmd') ? $tempUrl->GetKey('cmd') : 'Orders',
            //'do' => $tempUrl->GetKey('do'),
        ));
        if ($tempUrl->GetKey('debug')) {
            $current['debug'] = 1;
        }
        if (! empty($params)) {
            foreach ($params as $key => $value) {
                $tempUrl->SetValue($key, $value);
            }
        }
        if ($cleanUrl) {
            $newCleanUrl = '/' . RequestWrapper::getUriPart(0) . '/?' . http_build_query(array_merge($current, $params));
            $tempUrl->Set($newCleanUrl);

        }
        return $tempUrl->Get();
    }

    public function getWarehouseProductUrl($item)
    {
        $category = str_replace('wh-', '', $item['categoryid']);
        $itemid = str_replace('wh-', '', $item['itemid']);
        return $this->generate(array(
            'cmd'=>'WarehouseProducts',
            'do'=>'editProduct',
            'category'=> $category,
            'id' => $itemid,
        ));
    }

    public function AssignCmdAndDo($cmdAction, $doAction, $cleanUrl = true)
    {
        if ($cleanUrl) {
            $url = $this->generate(array(
                'cmd' => $cmdAction,
                'do' => $doAction,
            ));
        } else {
            $tempUrl = new UrlWrapper();
            $url = $tempUrl->Set($this->Get())
                ->Add('do', $doAction)
                ->Add('cmd', $cmdAction)
                ->Get();
        }
        return $url;
    }

    public function AssignClearCmd($cmdAction)
    {
        return $this->generate(array(
            'cmd' => $cmdAction,
        ));
    }

    public function SetPageLangUrl($lang)
    {
        $tempUrl = new UrlWrapper();
        return $tempUrl->Set($this->Get())
            ->Add('do', 'setPageLang')
            ->Add('cmd', 'AdminLanguage')
            ->Add('lang', $lang)
            ->Add('retpath', $this->Get())
            ->Get();
    }

    public function SetAdminLangUrl($lang)
    {
        $tempUrl = new UrlWrapper();
        return $tempUrl->Set($this->Get())
            ->Add('do', 'setAdminLang')
            ->Add('cmd', 'AdminLanguage')
            ->Add('lang', $lang)
            ->Add('retpath', $this->Get())
            ->Get();
    }

    public function SetStatusAdminLangUrl($lang)
    {
        $tempUrl = new UrlWrapper();
        return $tempUrl->Set($this->Get())
            ->Add('do', 'setStatusAdminLang')
            ->Add('cmd', 'AdminLanguage')
            ->Add('lang', $lang)
            ->Add('retpath', $this->Get())
            ->Get();
    }

    public function SetSiteLangUrl($lang)
    {
        $tempUrl = new UrlWrapper();
        return $tempUrl->Set($this->Get())
            ->Add('do', 'setSiteLang')
            ->Add('cmd', 'AdminLanguage')
            ->Add('lang', $lang)
            ->Add('retpath', $this->Get())
            ->Get();
    }


    public function getTranslationsUrl()
    {
        $tempUrl = new UrlWrapper();
        $cmd = $this->getCmd();

        $tempUrl->Set(UrlGenerator::getProtocol() . '://'.$_SERVER['HTTP_HOST'].'/admin/')
            ->Add('do', 'getTranslations')
            ->Add('cmd', 'AdminLanguage')
            ->Add('ver', CFG_ADMIN_VERSION);

        if($cmd != 'AdminLanguage') {
            $tempUrl->Add('section', $cmd);
        }

        return $tempUrl->Get();
    }
}
