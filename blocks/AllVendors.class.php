<?php

class AllVendors extends GenerateBlock {

    protected $_cache = true; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'vendors'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/vendors/';
    public $_hash = '';

    public function __construct() {
        parent::__construct(true);
        $this->tpl->_caching_id = $this->request->getValue('letter') . $this->request->getValue('type') . Session::get('active_lang');
    }

    protected function setVars() {
        // Запрашивается список брен
        global $otapilib;

        $pageTitle = Lang::get('all_vendors_page_title');
        if ($pageTitle !== 'all_vendors_page_title') {
            $GLOBALS['pagetitle'] = $pageTitle;
        }

        $seoDescription = Lang::get('all_vendors_seo_description');
        if ($seoDescription !== 'all_vendors_seo_description') {
            $GLOBALS['seo_description'] = $seoDescription;
        }

        $seoKeywords = Lang::get('all_vendors_seo_keywords');
        if ($seoKeywords !== 'all_vendors_seo_keywords') {
            $GLOBALS['seo_keywords'] = $seoKeywords;
        }

        $params = '<RatingListVendorSearchParameters><ItemRatingType>Best</ItemRatingType></RatingListVendorSearchParameters>';
        $vendor_list = array();
        $totalCount = 1000;
        for ($count = 0; $count < $totalCount; $count+=200) {
            $vendors = $otapilib->SearchRatingListVendors($params, $count, 200);
            $vendor_list = array_merge($vendor_list, $vendors['content']);
            if ($count == 0) $totalCount = $vendors['TotalCount'];
        }

        if ($this->request->getValue('letter') && count($vendor_list)) {
            foreach ($vendor_list as $k => $vendor) {
                if (mb_strtoupper(mb_substr($vendor['DisplayName'], 0, 1, 'UTF-8'), 'UTF-8') != mb_strtoupper($this->request->getValue('letter'), 'UTF-8')) {
                    unset($vendor_list[$k]);
                }
            }
        }

        $this->tpl->assign('vendor_list', $vendor_list);
    }
}

?>