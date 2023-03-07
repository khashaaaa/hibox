<?php

class SearchOldController extends GeneralContoller
{
    public function __construct()
    {
        parent::__construct();

        $alias = General::prepareRequest();
        General::prepareAliases($alias);
    }

    public function renderContent($content)
    {
        $style = '<style>
            form.search .field { width: 320px; }
            form.search input[type="submit"] { height: 30px !important; }
        </style>';
        return parent::renderContent($style . $content);
    }

    public function searchAction()
    {
        $CFG_CREATE_BLOCKS = array ('ItemListNew', 'CrumbsNew');
        define('CFG_PAGE_TEMPLATE', 'searchnew');

        $content = General::runBlocks(CFG_PAGE_TEMPLATE, $CFG_CREATE_BLOCKS);

        return $this->renderContent($content);
    }

    public function vendorAction()
    {
        $CFG_CREATE_BLOCKS = array ('ItemListNew', 'CrumbsNew');
        define('CFG_PAGE_TEMPLATE', 'vendorinfonew');

        $content = General::runBlocks(CFG_PAGE_TEMPLATE, $CFG_CREATE_BLOCKS);

        return $this->renderContent($content);
    }

    public function categoryAction()
    {
        $CFG_CREATE_BLOCKS = array ('ItemListNew', 'CrumbsNew');
        define('CFG_PAGE_TEMPLATE', 'categorynew');

        $content = General::runBlocks(CFG_PAGE_TEMPLATE, $CFG_CREATE_BLOCKS);

        return $this->renderContent($content);
    }

    public function subcategoryAction()
    {
        if ((RequestWrapper::get('root') && substr(RequestWrapper::get('cid'),0,3) == 'CID') || RequestWrapper::getParamExists('virt')) {
            // Показ подкатегорий без товаров, в рутовых категориях с ручным переводом нет товаров
            $CFG_CREATE_BLOCKS = array ('SubCategoryNew', 'CrumbsNew');
            define('CFG_PAGE_TEMPLATE', 'subcategorynew');
        } else {
            // Показ подкатегорий c товарами и фильтром
            $CFG_CREATE_BLOCKS = array ('ItemListNew', 'CrumbsNew');
            define('CFG_PAGE_TEMPLATE', 'categorynew');
        }

        $content = General::runBlocks(CFG_PAGE_TEMPLATE, $CFG_CREATE_BLOCKS);

        return $this->renderContent($content);
    }
}
