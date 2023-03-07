<?php

class Banners extends GenerateBlock {

    protected $_cache = CFG_CACHED; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'bannersnew'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';

    public function __construct()
    {
        parent::__construct(true);
    }

    protected function setVars()
    {
        try {
            $BannersRepository = new BannerRepository(new CMS());
            $banners = $BannersRepository->GetBanners($_SESSION['active_lang']);
        } catch (DBException $e) {
            Session::setError($e->getMessage(), 'DBError');
        }

        $data = (isset($data[0])) ? $data : array();
        $newBanners = Plugins::invokeEvent('onRenderSiteBanners', array('data' => @$data));
        $banners = $newBanners === false ? $banners : $newBanners;

        $this->tpl->assign('banners', $banners);
    }

}
