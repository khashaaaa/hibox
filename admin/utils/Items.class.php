<?php
OTBase::import('system.uploader.php.UploadHandler');

class Items extends GeneralUtil
{
    protected $_template = 'items';
    protected $_template_path = 'items/';

    protected $setsProvider;

    public function __construct()
    {
        parent::__construct();
        $this->setsProvider = new SetsProvider($this->cms, $this->getOtapilib());
    }

    public function defaultAction($request)
    {
        $this->_template = 'index';
        print $this->fetchTemplate();
    }

    public function editItemAction($request)
    {
        try {
            $id = $type = $request->getValue('item-id');
            if ($this->setsProvider->GetItemFullInfo($id, $this->getActiveLang($request))) {
                $this->sendAjaxResponse(array('result' => 'ok', 'id' => $id));
            } else {
                $this->respondAjaxError(LangAdmin::get('Item_not_found'));
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
    }

    public function editAction($request)
    {
        $this->_template = 'crud';

        try {
            $id = $request->getValue('id');
            $cid = $request->getValue('cid');
            $sid = Session::get('sid');
            $settings = array();

            OTAPILib2::GetRatingListsByElement($this->getActiveLang($request), $sid, 'Item', $id, $request);
            OTAPILib2::GetAdditionalItemInfo(Session::getActiveAdminLang(), $sid, $this->getActiveLang($request), $id, 'true', $settings);
            OTAPILib2::makeRequests();

            $settings = $settings->GetResult()->GetRawData();

            $sets = array();

            foreach($request->GetResult()->GetContent()->GetItem() as $i) {
                $set = array (
                    'ContentTypeName' => $i->GetContentTypeName(),
                    'ItemRatingTypeName' => $i->GetItemRatingTypeName(),
                    'CategoryId' => $i->GetCategoryId(),
                    'Count' => $i->GetCount()
                );

                $ratingTypeName = $i->GetItemRatingTypeName();
                $categoryId = $i->GetCategoryId();

                $set['url'] = '/admin/?cmd=sets&do=items&type=' . $i->GetItemRatingTypeName()
                    . ($i->GetCategoryId() ? '&cid=' . $i->GetCategoryId() : '');

                switch ($ratingTypeName) {
                    case 'Category':
                        $category = $this->otapilib->GetCategoryInfo($i->GetCategoryId());
                        $set['title'] = LangAdmin::get('Category') . ': ' . $category['name'];
                        break;
                    case 'Last':
                        $set['title'] = LangAdmin::get('items_with_last_label');
                        break;
                    case 'Best':
                        $set['title'] = LangAdmin::get('items_with_best_label');
                        if ($categoryId == 'Warehouse') {
                            $set['title'] = LangAdmin::get($categoryId);
                            $set['url'] = $set['url'] = '/admin/?cmd=sets&do=items&type=' . $categoryId;
                        }
                        break;
                    // В спецификации тип Recommend есть, но в админке не предусмотрен
                    case 'Recommend':
                        $set['title'] = LangAdmin::get('items_with_best_label');
                        break;
                    case 'Popular':
                        $set['title'] = LangAdmin::get('items_with_popular_label');
                        break;
                }

                $sets[] = $set;
            }

            $itemInfo = $this->setsProvider->GetItemFullInfo($id, $this->getActiveLang($request));
            $itemDescription = $this->setsProvider->GetItemDescription($id, $this->getActiveLang($request));
            $provider = InstanceProvider::getObject()->GetProviderInfo($this->getActiveLang($request), $itemInfo['ProviderType']);

            if ($itemInfo['ProviderType'] == InstanceProvider::PROVIDER_TYPE_WAREHOUSE) {
                $id = trim($itemInfo['id'], 'wh-');
                $category = $itemInfo['ExternalCategoryId'];
                $url = 'index.php?cmd=WarehouseProducts&do=editProduct&category='.$category.'&id='.$id;
                $this->redirect($url);
            }

            $itemBrandName = $itemInfo['BrandName'];
            $itemBrandId = $itemInfo['BrandId'];

            $setsRepository = new SetsRepository($this->cms);

            $items = $setsRepository->addCutomImageForItems(array(0 => $itemInfo), $this->getActiveLang($request));

            $itemInfo = $items[0];

            $this->tpl->assign('id', $id);
            $this->tpl->assign('sets', $sets);
            $this->tpl->assign('itemInfo', $itemInfo);
            $this->tpl->assign('itemBrandName', $itemBrandName);
            $this->tpl->assign('itemBrandId', $itemBrandId);
            $this->tpl->assign('provider', $provider);
            $this->tpl->assign('itemDescription', $itemDescription);
            $this->tpl->assign('currentLang', $this->getActiveLang($request));
            $this->tpl->assign('settings', $settings);
            $this->tpl->assign('updateUrl', '?cmd=items&do=updateSettings&id=' . $id);
            if ($cid) {
                $this->tpl->assign('cid', $cid);
            }
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }
        print $this->fetchTemplate();

    }

    public function updateSettingsAction($request)
    {
        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->get('type');
        $itemId = $request->get('id');

        try {
            $params = explode(MetaUI::NODES_SEPARATOR, $name);
            if (is_array($params) && count($params) > 0) {
                $xmlParameters = MetaUI::generateSingleParamXml('AdditionalItemInfoUpdateData', $params, $value, $type);
                $answer = false;
                OTAPILib2::UpdateAdditionalItemInfo(Session::getActiveAdminLang(), Session::get('sid'), $this->getActiveLang($request), $itemId, $xmlParameters, $answer);
                OTAPILib2::makeRequests();
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
    }

    public function saveFilterAction($request)
    {
        try {
            $sid = Session::get('sid');
            $xml = "<TranslationUpdateData><Text>" . $request->getValue('value') . "</Text></TranslationUpdateData>";
            switch ($request->getValue('name')) {
                case 'ItemPropertyName': 
                    $translatableContent = 'taobao:ItemProperty:Name';
                    break;
                case 'ItemPropertyValueName': 
                    $translatableContent = 'taobao:ItemPropertyValue:Name';
                     break;
            }
            OTAPILib2::UpdateTranslation(
                    $this->getActiveLang($request),
                    Session::get('sid'),
                    $this->getActiveLang($request),
                    $translatableContent,
                    $request->get('filterId'),
                    $xml,
                    $answer
            );
            OTAPILib2::makeRequests();
        } catch(ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array('result' => 'ok'));
    }

    public function saveItemAction($request)
    {
        try {
            $sid = Session::get('sid');
            $id = $request->getValue('id');
            $title = $request->getValue('title');
            $description = $request->getValue('description');
            $field = $request->getValue('field');

            if ($field == 'title') {
                $key = "taobao:Item:Title";
                $result = $this->setsProvider->EditTranslateByKey($sid, $this->getActiveLang($request), $title, $key, $id);
            } else if ($field == 'image') {
                $oldImage = $request->getValue('existingImage');
                $newImage = $this->getNameSetUploadImage();

                if ($newImage) {
                    $this->setsProvider->SetItemCustomPictures('', $id, $newImage, $this->getActiveLang($request));
                } elseif ($oldImage == 'del') {
                    $this->setsProvider->DelItemCustomPictures('', $id, $this->getActiveLang($request));
                }
            } else if ($field == 'description') {
                $key = "taobao:Item:Description";
                $result = $this->setsProvider->EditTranslateByKey($sid, $this->getActiveLang($request), $description, $key, $id);
            }
        } catch(ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array('result' => 'ok'));
    }

    public function resetItemAction($request)
    {
        try {
            $sid = Session::get('sid');
            $id = $request->getValue('id');
            $field = $request->getValue('field');
            $inputLanguage = $this->getActiveLang($request);

            $language = $this->getActiveLang($request);

            $translatableContent = $request->getValue('translatableContent');
            $translationId = $request->getValue('translationId');

            $key = false;
            if ($field == 'title') {
                $key = 'taobao:Item:Title';
            } else if ($field == 'description') {
                $key = 'taobao:Item:Description';
            }

            if ($key) {
                $answer = null;
                $updateData = new SimpleXMLElement('<TranslationUpdateData></TranslationUpdateData>');
                $updateData->addChild('ResetTranslation', 'true');
                $updateData = str_replace('<?xml version="1.0"?>', '', $updateData->asXML());

                OTAPILib2::UpdateTranslation($language, Session::get('sid'), $inputLanguage, $key, $id, $updateData, $answer);
                OTAPILib2::makeRequests();
            }

            $resetValue = '';
            if ($field == 'title') {
                $itemInfo = $this->setsProvider->GetItemFullInfo($id, $this->getActiveLang($request));
                $resetValue = $itemInfo['Title'];
            } else if ($field == 'description') {
                $itemDescription = $this->setsProvider->GetItemDescription($id, $this->getActiveLang($request));
                $resetValue = $itemDescription;
            }
        } catch(ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'ok', 'resetValue' => $resetValue));
    }

    private function getNameSetUploadImage()
    {
        if (! empty($_FILES['newImage']['tmp_name'])) {
            $uploadResult = $this->uploadSetImage();
            if (isset($uploadResult['newImage'][0])) {
                if (isset($uploadResult['newImage'][0]->url)) {
                    $logoUrl = $uploadResult['newImage'][0]->url;
                } else if (isset($uploadResult['newImage'][0]->error)) {
                    $this->respondAjaxError($uploadResult['newImage'][0]->error);
                }
            } else {
                $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
            }
        } else {
            $logoUrl = '';
        }
        return $logoUrl;
    }

    private function uploadSetImage()
    {
        $uploader = new UploadHandler(array(
            'param_name' => 'newImage',
            'image_versions' => array(
                '' => array(
                    'max_width' => 800,
                    'max_height' => 600,
                    'jpeg_quality' => 95
                ),
            ),
        ), false, null, '/uploaded/sets/');
        return $uploader->post(false);
    }
}
