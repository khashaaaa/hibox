<?php

class ItemSetsNew extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'itemsetsnew'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';

    /**
     * @var PristroyRepository
     */
    private $pristroyRepository;
    
    /**
     * @var SetsRepository
     */
    private $setsRepository;

    public function __construct()
    {
        parent::__construct(true);
        $this->otapilib->setErrorsAsExceptionsOn();

        $this->pristroyRepository = new PristroyRepository($this->cms);
        $this->setsRepository = new SetsRepository($this->cms);
    }

    protected function setVars()
    {
        $this->assignRatingListsFromOtapiToTemplate();

        if (CMS::IsFeatureEnabled('FleaMarket')) {
            $this->tpl->assign('pristroy_items', $this->getPristroyItems());
        }
        $this->tpl->assign('items_with_comments', $this->getItemsWithReviews());
    }

    public function assignRatingListsFromOtapiToTemplate()
    {
        $ratingsList = $this->setsRepository->getRatingsList();

        $parameters = SetsUpdater::getBestItemsParams();
        $this->tpl->assign('best_items', $ratingsList[$parameters['contentType']][$parameters['type']][$parameters['catId']]['items']);

        $parameters = SetsUpdater::getPopularItemsParams();
        $this->tpl->assign('popular_items', $ratingsList[$parameters['contentType']][$parameters['type']][$parameters['catId']]['items']);

        $parameters = SetsUpdater::getLastItemsParams();
        $this->tpl->assign('last_viewed_items', $ratingsList[$parameters['contentType']][$parameters['type']][$parameters['catId']]['items']);

        $parameters = SetsUpdater::getBrandsItemsParams();
        $this->tpl->assign('brands_list', $ratingsList[$parameters['contentType']][$parameters['type']][$parameters['catId']]['items']);

        $parameters = SetsUpdater::getVendorsItemsParams();
        $this->tpl->assign('vendors', $ratingsList[$parameters['contentType']][$parameters['type']][$parameters['catId']]['items']);

        $parameters = SetsUpdater::getWarehouseItemsParams();
        $this->tpl->assign('warehouse_items', $ratingsList[$parameters['contentType']][$parameters['type']][$parameters['catId']]['items']);
        $this->tpl->assign('warehouse_title', Lang::get("Warehouse_goods"));

        $parametersArray = SetsUpdater::getCategoryItemsParams();
        $categories = array();
        foreach ($parametersArray as $parameters) {
            $list = $ratingsList[$parameters['contentType']][$parameters['type']][$parameters['catId']];
            $categories[$parameters['catId']]['name'] = $list['displayName'];
            $categories[$parameters['catId']]['items'] = $list['items'];
        }
        $this->tpl->assign('categories', $categories);
    }

    protected function getPristroyItems()
    {
        $itemsCount = General::getConfigValue('items_with_pristroy', 8);
        $items = $itemsCount ? $this->pristroyRepository->getList(0, $itemsCount) : array();
        return !empty($items['data']) ? $items['data'] : array();
    }

    public function getItemsWithReviews()
    {
        $language = Session::getActiveLang();
        $setsUpdater = SetsUpdater::getInstance();
        $reviewedItems = $setsUpdater->getReviewedItems($language);

        return (!empty($reviewedItems)) ? $reviewedItems['items'] : array();
    }
}
