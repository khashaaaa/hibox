<?php

/**
 * Избранные продавцы
 */
class FavouriteVendors extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'favourite_vendors'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/'; //- путь к шаблону
    
    /**
     * @var UserData
     */
    protected $userData;

    public function __construct(){
        parent::__construct();
        global $otapilib;
        $otapilib->setErrorsAsExceptionsOn();
        $this->userData = new UserData();
    }

    /**
     * Получение списка продавцов
     */
    public function setVars(){
        global $otapilib;
        
        $vendors = array();
        try {
            $sid = Session::getUserOrGuestSession();
            $vendors = $otapilib->GetFavoriteVendors($sid);
        } catch (ServiceException $e) {
            $message = $e->getErrorMessage();
            if (OTBase::isTest() && Session::get('sid')) {
                $message = $e->getErrorCode().': '.$message;
            }
            show_error($message);
        }

        $this->tpl->assign('list', $vendors);
    }

    /**
     * Добавление продавца в избранное
     * @param RequestWrapper $request
     * @throws ServiceException
     */
    public function addAction($request)
    {
        global $otapilib;
        $vendorId = $request->getValue('id');
        $sid = Session::getUserOrGuestSession();
        $vendors = array();
        try {
            $this->otapilib->setErrorsAsExceptionsOn();
            $vendor = $this->otapilib->GetVendorInfo($vendorId);
            $this->otapilib->AddVendorToFavorites($sid, $vendorId, $this->generateAddXml($vendor));
            $vendors = $otapilib->GetFavoriteVendors($sid);
            $this->userData->ClearUserDataCache();
        } catch(Exception $e) {
            if (OTBase::isTest()) {
                $this->throwAjaxError($e);
            } else {
                $e = new Exception(Lang::get('Can_not_add_vender'));
                $this->throwAjaxError($e);
            }
        }
        $this->sendAjaxResponse(array(
            'vendors' => $vendors
        ));
    }
    
    /**
     * Удаление продавца из избранного
     * @param RequestWrapper $request
     * @throws ServiceException
     */
    public function deleteAction($request){
        $sid = Session::getUserOrGuestSession();
        $lang = Session::getActiveLang();
        $isAjax = $request->getValue('$isAjax');
        $ids = $request->getValue('ids');
        try{
            $idsVendors = is_array($ids) ? $ids : array($ids);
            $idsVendors = implode(';', $idsVendors);
            OTAPILib2::RemoveVendorsFromFavorites($lang, $sid, '', $idsVendors, $answer);
            $this->userData->ClearUserDataCache();
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
        }
        if (empty($isAjax)) {
            header('Location: /?p=favourite_vendors');
        } else {
            $this->sendAjaxResponse();
        }
    }

    /**
     * Формирование XML для добавления продавца в избранное
     * @param array $vendor Информация о продавце
     * @return string итоговый XML
     */
    public function generateAddXml($vendor){
        $xml = new SimpleXMLElement('<Fields></Fields>');

        $fields = array('Name','PictureUrl');
        $ratings = array('PositiveFeedbacks','TotalFeedbacks','Score', 'Level');
        foreach($fields as $field){
            $el = $xml->addChild('FieldInfo');
            $el->addAttribute('Name', htmlspecialchars($field));
            $el->addAttribute('Value', htmlspecialchars($vendor[$field]));
        }
        foreach($ratings as $field){
            $el = $xml->addChild('FieldInfo');
            $el->addAttribute('Name', htmlspecialchars($field));
            $el->addAttribute('Value', htmlspecialchars($vendor['Credit'][$field]));
        }

        return $xml->asXML();
    }
}
