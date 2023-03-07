<?php

class BrandsNew extends GenerateBlock {

    protected $_cache = CFG_CACHED; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'brandlistnew'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/brands/';
    public $_hash = '';
    
    public function __construct() {
        parent::__construct(true);
        if (@CFG_CACHED) {
            $this->tpl->_caching_id = @$_GET['letter'].@$_GET['type'].@$_SESSION['active_lang'];
        }
    }

    protected function setVars() {
        // Запрашивается список брен
        global $otapilib;

        $otapilib->setErrorsAsExceptionsOn();

        if (@$_GET['type']) {
            $brand_list = $otapilib->GetBrandRatingList($_GET['type'], 1000, 0);
        } else {
            $brand_list = $otapilib->GetBrandInfoList();
        }

        if (@$_GET['letter'] && is_array($brand_list)) {
            foreach ($brand_list as $k=>$b) {
                $brandName = (string)$b['Name'];
                if (strtoupper($brandName[0]) != strtoupper($_GET['letter'])) {
                    unset($brand_list[$k]);
                }
            }
        }

        $otapilib->setErrorsAsExceptionsOff();
        if (Lang::get('brand_type'.RequestWrapper::getValueSafe('type')) == 'brand_type'.RequestWrapper::getValueSafe('type')) unset($_GET['type']);

        $this->tpl->assign('brandlist', $brand_list);
    }

}

?>