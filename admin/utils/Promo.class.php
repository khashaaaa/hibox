<?php
class Promo extends GeneralUtil
{
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'promo';
    protected $_template_path = 'promo/';
    /**
     * @var SiteConfigurationRepository
     */
    protected $siteConfigRepository;
    /**
     * @var PromoProvider
     */
    protected $promoProvider;

    protected $sitemapGenerator;


    public function __construct()
    {
        parent::__construct();
        $this->cms->checkTable('site_config');
        $this->cms->checkTable('site_langs');
        $this->siteConfigRepository = new SiteConfigurationRepository($this->cms);
        $this->promoProvider = new PromoProvider($this->getOtapilib());
        $this->sitemapGenerator = new SitemapGenerator();
    }

    /**
     * @param RequestWrapper $request
     */
    public function defaultAction($request)
    {
        $this->_template = 'promo';

        $this->assignConfig();
        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function referralAction($request)
    {
        $this->redirect($this->pageUrl->generate(array('cmd' => 'referral')));
    }

    public function newslettersAction($request)
    {
        $this->redirect($this->pageUrl->generate(array('cmd' => 'newsletters')));
    }

    /**
     * @param RequestWrapper $request
     */
    public function subscribersAction($request)
    {
        $this->_template_path = 'promo/mailing/';
        $this->_template = 'subscribers';

        print $this->fetchTemplate();
    }

    /**
     * @deprecated не используется с 25.10.2018, для
     * рассылок используется модуль Newsletters
     * @param RequestWrapper $request
     */
    public function addMailingAction($request)
    {
        $this->_template_path = 'promo/mailing/';
        $this->_template = 'crud';

        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function configAction($request)
    {
        $this->_template_path = 'promo/mailing/';
        $this->_template = 'config';

        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function socialAction($request)
    {
        $this->_template = 'social';
        $_GET['inputLanguage'] = $this->getActiveLang($request);
        $settings = $this->renderMetaUISettingsByEntity($request, 'SocialNetworkSettings');
        $this->tpl->assign('settings', $settings);
        $this->assignConfig();

        print $this->fetchTemplate();
    }

    private function assignConfig()
    {
        $config = new SiteConfigurationRepository($this->cms);
        $config->SetActiveLang(Session::get('active_lang_promo'));
        $this->tpl->assign('config', $config);
        
    }

    /**
     * @deprecated не используется с 25.10.2018, для
     * рассылок используется модуль Newsletters
     * @param RequestWrapper $request
     */
    public function mailingAction($request)
    {
        $this->_template = 'mailing';

        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    private function onHideSocialSave($request)
    {
        if(!$request)
            return;
        $value = $request->getValue('value');
        $lang = Session::get('active_lang_promo');
        $langSuffix = $lang ? "_".$lang : "";
        $key="hide_social";
        $keySafe = $this->cms->escape($key.$langSuffix);
        if($value != 1){
            $this->siteConfigRepository->Remove($keySafe);
        } else {
            $this->siteConfigRepository->Set($keySafe, "checked");
        }
    }

    /**
     * @param RequestWrapper $request
     * @param string $field
     */
    private function checkHideField($request, $field)
    {
        if(!$field)
            return;
        $value = $this->cms->escape($request->getValue('value'));
        if(!empty($value)){
            $this->siteConfigRepository->Remove($field);
        }
        else{
            $this->siteConfigRepository->Set($field, 'checked');
        }
    }

    /**
     * @param RequestWrapper $request
     */
    public function saveAction($request)
    {
        $this->siteConfigRepository->SetActiveLang(Session::get('active_lang_promo'));
        $this->siteConfigRepository->Set($request->getValue('name'), $request->getValue('value'));

        $name = explode('_', $request->getValue('name'));
        foreach ($name as $i => $value) {
            $name[$i] = ucfirst($value);
        }
        $name = implode('', $name);
        if(method_exists($this, 'on'.$name.'Save')){
            call_user_func(array($this, 'on'.$name.'Save'),$request);
        }

        $this->sendAjaxResponse(array(
            'result' => 'ok',
        ));
    }

    /**
     * @param RequestWrapper $request
     */
    public function generateSiteMapAction($request)
    {
        try {
            $result = $this->sitemapGenerator->generateSiteMap();
            $this->sendAjaxResponse(array(
                'result' => $result,
            ));
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
    }

}
