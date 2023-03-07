<?php

require_once dirname(__FILE__) . '/AjaxController.php';

OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');
OTBase::import('system.uploader.php.UploadHandler');

class Pristroy extends AjaxController
{
    public function addProductAction($request)
    {
        try {
            $user = $this->otapilib->GetUserInfo(Session::getUserDataSid());
            if (empty($user['id'])) {
                $this->respondError(Lang::get('Authorization_error'));
            }

            $item = json_decode($request->getValue('item'));

            $validator = new Validator(array(
                'title'         => trim($request->getValue('pristroy_title')),
                'description'   => trim($request->getValue('pristroy_desc')),
                'price'         => $request->getValue('pristroy_price'),
                'quantity'      => $request->getValue('pristroy_quantity'),
            ));
            $validator->addRule(new NotEmptyString(),  'title',         Lang::get('Title_cannot_be_empty'));
            $validator->addRule(new NotEmptyString(),  'description',   Lang::get('Description_cannot_be_empty'));
            $validator->addRule(new NotEmptyNumber(1), 'price',         Lang::get('Price_must_be_positive'));
            $validator->addRule(new Range(1, $item->Qty), 'quantity',   Lang::get('Incorrect_quantity'));

            if (! $validator->validate()) {
                $this->respondError($validator->getErrors());
            }
            $data = $validator->getData();

            $uploadResult = json_decode($this->uploadImage());
            $uploadedImage = '';
            if (isset($uploadResult->pristroy_image[0]->url)) {
                $uploadedImage = $uploadResult->pristroy_image[0]->url;
            }

            $images = json_encode(array_filter(array(
                $request->getValue('default_image'),
                $uploadedImage,
            )));

            try {
                $fulliteminfo = $this->otapilib->BatchGetItemFullInfo(User::getObject()->getSid(), $item->ItemId, 'RootPath');
            } catch (ServiceException $e) {
                // если не вышло, пишем что отдал сервис - ошибку
                $fulliteminfo = $e->getCode(). ':' . $e->getMessage();
            }

            $pristroy = new PristroyRepository(new CMS());

            $newId = $pristroy->addProduct(
                $data['title'],
                $data['description'],
                $images,
                $data['price'],
                $request->getValue('currency'),
                $data['quantity'],
                $user['id'],
                $user['login'],
                $item->id,
                $item->ItemId,
                $item->ConfigId,
                $item->ConfigText,
                json_encode($fulliteminfo)
            );

            $product = $pristroy->getProduct($newId);

            $this->sendResponse(array(
                'product' => $product,
            ), true);
        }
        catch (Exception $e) {
            $this->respondError($e->getMessage());
        }
    }

    public function saveProductAction($request)
    {
        try {
            $item = json_decode($request->getValue('item'));
            if (! isset($item->pristroy->id)) {
                $this->respondError('Could not find the product ID. Probably, you are trying to update an unexisting item.');
            }

            $validator = new Validator(array(
                'title'         => trim($request->getValue('pristroy_title')),
                'description'   => trim($request->getValue('pristroy_desc')),
                'price'         => $request->getValue('pristroy_price'),
                'quantity'      => $request->getValue('pristroy_quantity'),
            ));
            $validator->addRule(new NotEmptyString(),  'title',         Lang::get('Title_cannot_be_empty'));
            $validator->addRule(new NotEmptyString(),  'description',   Lang::get('Description_cannot_be_empty'));
            $validator->addRule(new NotEmptyNumber(1), 'price',         Lang::get('Price_must_be_positive'));
            $validator->addRule(new Range(1, $item->Qty), 'quantity',   Lang::get('Incorrect_quantity'));

            if (! $validator->validate()) {
                $this->respondError($validator->getErrors());
            }
            $data = $validator->getData();

            $uploadResult = json_decode($this->uploadImage());
            $uploadedImage = $request->getValue('uploaded_image');
            if (isset($uploadResult->pristroy_image[0]->url)) {
                $uploadedImage = $uploadResult->pristroy_image[0]->url;
            }

            $images = json_encode(array_filter(array(
                $request->getValue('default_image'),
                $uploadedImage,
            )));

            $status = PristroyRepository::STATUS_ON_MODERATION;

            $pristroy = new PristroyRepository(new CMS());
            $pristroy->updateProduct(
                $item->pristroy->id,
                $data['title'],
                $data['description'],
                $data['price'],
                $data['quantity'],
                $status,
                $images
            );

            $this->sendResponse(array(
                'product' => $pristroy->getProduct($item->pristroy->id)
            ));
        } catch (Exception $e) {
            $this->respondError($e->getMessage());
        }
    }

    public function updateStatusAction($request)
    {
        try {
            $pristroy = new PristroyRepository(new CMS());
            $statuses = $pristroy->getStatuses();

            $validator = new Validator(array(
                'id'        => $request->getValue('id'),
                'status'    => $request->getValue('status'),
            ));
            $validator->addRule(new NotEmptyNumber(), 'id', 'ID cannot be empty');
            $validator->addRule(new Choice(array_keys($statuses)), 'status', 'Invalid pristroy item status given');

            if (! $validator->validate()) {
                $this->respondError($validator->getErrors());
            }
            $data = $validator->getData();

            $pristroy->updateStatusByItemId($data['id'], $statuses[$data['status']]);

            $this->sendResponse(array(
                'status' => $statuses[$data['status']]
            ));
        } catch (Exception $e) {
            $this->respondError($e->getMessage());
        }
    }

    private function uploadImage()
    {
        ob_start();
        new UploadHandler(array(
            'param_name' => 'pristroy_image',
            'image_versions' => array(
                'thumbnail_100_100' => array(
                    'crop' => true,
                    'max_width' => 100,
                    'max_height' => 100,
                    'jpeg_quality' => 90
                ),
                'thumbnail_160_160' => array(
                    'crop' => true,
                    'max_width' => 160,
                    'max_height' => 160,
                    'jpeg_quality' => 90
                ),
                'thumbnail_310_310' => array(
                    'crop' => true,
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
}

new Pristroy();
