<?php

class Warehouse extends GeneralUtil
{
    protected $_template = 'categories';
    protected $_template_path = 'warehouse/';

    protected $warehouseProvider;

    public function __construct()
    {
        parent::__construct();

        $this->warehouseProvider = new WarehouseProvider($this->getOtapilib());
    }

    function defaultAction($request)
    {
        $sid = Session::get('sid');
        $searchParams = $this->warehouseProvider->generateSearchParams($request);

        $categories = array();
        try {
            $categories = $this->warehouseProvider->SearchWarehouseCategories($sid, $searchParams);
            if (! is_array($categories)) {
                throw new ServiceException(__METHOD__, '', 'Could not load categories list', 1);
            }
            $categories = $categories['Content'];
            $this->checkEmptyCategoryAndDefaultFill($request, $categories);
        } catch (Exception $e) {
            ErrorHandler::registerError($e);
            $categories = array();
        }
        
        $this->tpl->assign('categories', $categories);

        print $this->fetchTemplate();
    }

    private function checkEmptyCategoryAndDefaultFill($request, $categories)
    {
        if (empty($categories)) {
            $this->createDefaultCategory();
            $request->RedirectToReferrer();
        }
    }

    private function createDefaultCategory()
    {
        $name = LangAdmin::get('Default_Warehouse_category_name');
        $parentId = 0;
        $position = 0;
        $newId = $this->warehouseProvider->CreateWarehouseCategoryData(Session::get('sid'), $name, $parentId, $position);
        if (! is_numeric($newId)) {
            throw new ServiceException(__METHOD__, '', 'Could not create new category', 1);
        }
    }

    /**
     * @param RequestWrapper $request
     */
    public function getCategoriesAction($request)
    {
        try {
            $parentId = (int)$request->getValue('parentId');
            $xml = $this->warehouseProvider->SearchWarehouseCategoriesXML($parentId);
            $categories = $this->warehouseProvider->SearchWarehouseCategories(Session::get('sid'), $xml);
            if (! is_array($categories)) {
                throw new ServiceException(__METHOD__, '', 'Could not load categories list', 1);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array(
            'categories' => $categories['Content']
        ));
    }

    /**
     * @param RequestWrapper $request
     */
    public function createCategoryAction($request)
    {
        try {
            $name = $request->getValue('name');
            $parentId = (int)$request->getValue('parentId');
            $position = (int)$request->getValue('position');
            $newId = $this->warehouseProvider->CreateWarehouseCategoryData(Session::get('sid'), $name, $parentId, $position);
            if (! is_numeric($newId)) {
                throw new ServiceException(__METHOD__, '', 'Could not create new category', 1);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array(
            'newId' => (int)$newId
        ));
    }

    /**
     * @param RequestWrapper $request
     */
    public function renameCategoryAction($request)
    {
        try {
            $id = (int)$request->getValue('id');
            $newName = (string)$request->getValue('newName');
            $result = $this->warehouseProvider->UpdateWarehouseCategoryInfo(Session::get('sid'), $id, $newName);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    /**
     * @param RequestWrapper $request
     */
    public function removeCategoryAction($request)
    {
        try {
            $id = (int)$request->getValue('id');
            $result = $this->warehouseProvider->DeleteWarehouseCategory(Session::get('sid'), $id);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    /**
     * @param RequestWrapper $request
     */
    public function moveCategoryAction($request)
    {
        try {
            $id = (int)$request->getValue('id');
            $parentId = (int)$request->getValue('parentId');
            $position = (int)$request->getValue('position');
            $copy = (bool)$request->getValue('copy');
            $result = $this->warehouseProvider->UpdateWarehouseCategoryInfo(Session::get('sid'), $id, null, $parentId);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    /**
     * @param RequestWrapper $request
     */
    public function importAction($request)
    {
        $this->_template = 'import';

        $activitiesTable = '';

        try {
            $types = array('WarehouseItemsImporting');
            $activitiesUtil = new ActivitiesUtil();
            $activitiesTable = $activitiesUtil->getActivitiesListView($types);
        } catch (Exception $e) {
            ErrorHandler::registerError($e);
        }

        $this->tpl->assign('activitiesTable', $activitiesTable);

        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function startImportAction($request)
    {
        $activityId = '';
        $activityType = '';

        try {
            $lang = Session::getActiveAdminLang();
            $sessionId = Session::get('sid');

            $fileIds = $request->getValue('fileId');
            $fileId = isset($fileIds[0]) ? $fileIds[0] : null;

            $answer = $this->warehouseProvider->RunWarehouseImportItems($lang, $sessionId, $fileId);

            if (! $answer) {
                throw new Exception('Service reply is wrong');
            }

            $activityId = $answer->getResult()->GetId()->asString();
            $activityType = $answer->getResult()->GetType();
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array('result' => 'ok', 'activityId' => $activityId, 'activityType' => $activityType), true);
    }

    public function getItemDescriptionAction($request) {
        $providerInfo = $this->otapilib->GetProviderInfo(InstanceProvider::PROVIDER_TYPE_WAREHOUSE);
        $itemDescription = $this->otapilib->GetItemDescription($request->getValue('itemId'), $providerInfo['Language']);

        if (General::getConfigValue('hide_item_external_links_in_description')) {
            $itemDescription = preg_replace('~(href=[\'|"|](http|https)?[^>]*)~is', '', $itemDescription);
        }

        $itemDescription = ProductsHelper::prepareDescription($itemDescription);

        $this->sendAjaxResponse(array('description' => $itemDescription), true);
    }
}
