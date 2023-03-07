<?php

OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');
OTBase::import('system.uploader.php.UploadHandler');

class Pristroy extends GeneralUtil
{
    protected $_template = 'index';
    protected $_template_path = 'pristroy/';
    /**
     * @var PristroyRepository
     */
    protected $pristroy;

    public function __construct()
    {
        parent::__construct();

        $this->pristroy = new PristroyRepository($this->cms);
    }

    /**
     * @param RequestWrapper $request
     * @return array
     */
    private function generateFilter($request)
    {
        $filter = array();
        if ($request->getValue('user') && strlen($request->getValue('user'))>0) {
            $login = $request->getValue('user');
            $filter['user'] = $login;

            try {
                $userId = 0;
                $xmlParams = new SimpleXMLElement('<UserFilterParameters></UserFilterParameters>');
                if ($login) {
                    $xmlParams->addChild('Login', htmlspecialchars($login));
                    $users = $this->otapilib->FindBaseUserInfoListFrame(Session::get('sid'), $xmlParams->asXML(), 0, 100);

                    foreach ($users['Content'] as $userData) {
                        if ($userData['Login'] == $login) {
                            $userId = $userData['Id'];
                            break;
                        }
                    }
                }
                $userId = $userId ? $userId : 0;
                $filter['userId'] = $userId;
            }
            catch (ServiceException $e) {
                Session::setError($e->getMessage(), $e->getErrorCode());
            }
        }
        if ($request->getValue('on_moderation')) {
            $filter['status'][] = PristroyRepository::STATUS_ON_MODERATION;
        }
        if($request->getValue('approved')) {
            $filter['status'][] = PristroyRepository::STATUS_APPROVED;
        }
        if($request->getValue('rejected')) {
            $filter['status'][] = PristroyRepository::STATUS_REJECTED;
        }
        return $filter;
    }

    public function defaultAction($request)
    {
        $filter = $this->generateFilter($request);

        $page = $this->getPageDisplayParams($request);
        
        $perpage = $page['limit'];
        $page = $page['number'];
        $from = $page;

        $count = 0;
        try {
            $list = $this->pristroy->getListForAdmin($from, $perpage, true, $filter);
            $count = $list['rows'];
            $this->tpl->assign('list', $list['data']);
        } catch (ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
        }

        $this->tpl->assign('filter', $filter);
        $this->tpl->assign('paginator', new Paginator($count, $page, $perpage));

        print $this->fetchTemplate();
    }

    public function editAction($request)
    {
        $id = RequestWrapper::get('id');

        try {
            $product = $this->pristroy->getProduct($id);
            
            if (empty($product['id'])) {
                throw new ServiceException(__METHOD__, '', 'Product not found', 1);
            }
            $this->tpl->assign('product', $product);
        } catch (ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
        }

        $this->tpl->assign('actionTitle', LangAdmin::get('editing'));

        $this->_template = 'form';
        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function saveAction($request)
    {
        $id = $request->getValue('id');

        try {
            if ($id) {
                $product = $this->pristroy->getProduct($id);
                if (empty($product['id'])) {
                    $this->respondAjaxError('Product not found');
                }

                $status = (int) $request->getValue('status');
                $validator = new Validator(array(
                    'title'             => trim($request->getValue('title')),
                    'description'       => trim($request->getValue('description')),
                    'price'             => $request->getValue('price'),
                    'quantity'          => $request->getValue('quantity'),
                    'status'            => $status,
                    'default_image'     => $request->getValue('default_image'),
                    'uploaded_image'    => $request->getValue('uploaded_image'),
                    'reject_reason'     => trim($request->getValue('reject_reason')),
                ));
                $validator->addRule(new NotEmptyString(), 'title',       LangAdmin::get('Title_cannot_be_empty'));
                $validator->addRule(new NotEmptyString(), 'description', LangAdmin::get('Description_cannot_be_empty'));
                $validator->addRule(new NotEmptyNumber(0.001), 'price',      LangAdmin::get('Price_must_be_positive'));
                $validator->addRule(new NotEmptyNumber(1), 'quantity',   LangAdmin::get('Quantity_must_be_positive'));
                if ($status == PristroyRepository::STATUS_REJECTED) {
                    $validator->addRule(new NotEmptyString(), 'reject_reason', LangAdmin::get('Enter_reject_reason'));
                }

                if (! $validator->validate()) {
                    $this->respondAjaxError($validator->getErrors());
                }
                $data = $validator->getData();

                $defImageUploadResult = json_decode($this->uploadImage('new_default_image'));
                if (isset($defImageUploadResult->new_default_image[0]->url)) {
                    $data['default_image'] = $defImageUploadResult->new_default_image[0]->url;
                }
                
                $uploadResult = json_decode($this->uploadImage('new_uploaded_image'));
                if (isset($uploadResult->new_uploaded_image[0]->url)) {
                    $data['uploaded_image'] = $uploadResult->new_uploaded_image[0]->url;
                }
                $images = json_encode(array_filter(array(
                    $data['default_image'],
                    $data['uploaded_image'],
                )));

                $this->pristroy->updateProduct(
                        $id,
                        $data['title'],
                        $data['description'],
                        $data['price'],
                        $data['quantity'],
                        $data['status'],
                        $images,
                        $data['reject_reason']
                );

                $this->clearCacheOnIndexPage();
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array(), true);
    }

    public function rejectAction($request)
    {
        //$id = $request->getValue('id');
        $params = $request->getValue('params');
        try {
            $result = $this->pristroy->rejectProduct($params);
            $this->clearCacheOnIndexPage();
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function approveAction($request)
    {
        $id = $request->getValue('id');
        try {
            $result = $this->pristroy->approveProduct($id);
            $this->clearCacheOnIndexPage();
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function removeAction($request)
    {
        $id = $request->getValue('id');
        try {
            $result = $this->pristroy->updateProductStatus($id, PristroyRepository::STATUS_REMOVED);
            $this->clearCacheOnIndexPage();
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function approveBulkAction($request)
    {
        $this->approveAction($request);
    }

    public function rejectBulkAction($request)
    {
        $this->rejectAction($request);
    }

    public function removeBulkAction($request)
    {
        $this->removeAction($request);
    }

    // TODO: Убрать дублирование с WarehouseProducts::uploadImage
    private function uploadImage($param_name)
    {
        ob_start();
        new UploadHandler(array(
            'param_name' => $param_name,
            'image_versions' => array(
                'thumbnail_100_100' => array(
                    'max_width' => 100,
                    'max_height' => 100,
                    'jpeg_quality' => 90
                ),
                'thumbnail_160_160' => array(
                    'max_width' => 160,
                    'max_height' => 160,
                    'jpeg_quality' => 90
                ),
                'thumbnail_310_310' => array(
                    'max_width' => 310,
                    'max_height' => 310,
                    'jpeg_quality' => 90
                ),
            ),
        ), true, null, '/uploaded/pristroy/');
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    private function clearCacheOnIndexPage()
    {
        // TODO: Очищать кеш нужно именно для подборки пристроя, а не вообще кеш всех подборок
        Session::set('clear_cache_itemsetsnew', true);
    }
}
