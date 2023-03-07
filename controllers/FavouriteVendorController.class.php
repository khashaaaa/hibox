<?php

class FavouriteVendorController extends GeneralContoller
{
    /**
     * @var UserData
     */
    protected $userData;

    public function __construct()
    {
        parent::__construct();
        $this->userData = new UserData();
    }

    public function defaultAction()
    {
        $this->setCrumbs();

        $lang = Session::getActiveLang();
        $user = User::getObject();
        $sid = $user->getSid();

        $page = $this->request->getValue('page', 1);
        $perPage = $this->request->getValue('perPage', 20);

        $frameSize = $perPage;
        $framePosition = ($page - 1) * $frameSize;

        $totalCount = 0;
        $vendors = [];

        try {
            $xmlParameters = $this->getXmlParameters();

            OTAPILib2::SearchSimplifiedFavoriteVendors($lang, $sid, $xmlParameters, $framePosition, $frameSize, $answer);
            OTAPILib2::makeRequests();

            $result = $answer->GetResult();
            $totalCount = $result->GetTotalCount();

            foreach ($result->GetContent()->GetItem() as $vendor) {
                $vendors[] = Vendor::getObject($vendor->GetId(), $vendor);
            }
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }

        $paginator = new Paginator($totalCount, $page, $perPage);

        return $this->render('controllers/favourite-vendors/list', [
            'paginator' => $paginator,
            'vendors' => $vendors
        ]);
    }

    public function addAction()
    {
        /* TODO: метод еще нигде не используется */

        $id = $this->request->getValue('id');
        $user = User::getObject();
        $sid = $user->getSid();

        try {
            $xmlParameters = $this->getXmlParametersFields();

            OTAPILib2::AddVendorToFavorites($sid, $id, $xmlParameters, $answer);
            OTAPILib2::makeRequests();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse();
    }

    public function deleteAction()
    {
        $sid = User::getObject()->getSid();
        $lang = Session::getActiveLang();
        $ids = $this->request->getValue('ids');
        try {
            $idsVendors = is_array($ids) ? $ids : array($ids);
            $idsVendors = implode(';', $idsVendors);
            OTAPILib2::RemoveVendorsFromFavorites($lang, $sid, '', $idsVendors, $answer);
            OTAPILib2::makeRequests();
            $this->userData->ClearUserDataCache();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse();
    }

    /**
     * @return void
     */
    private function setCrumbs()
    {
        CrumbsController::setCrumbs([
            ['title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()],
            ['title' => Lang::get('favourite_vendors'), 'url' => UrlGenerator::getUrl('favourite_vendors')]
        ]);
    }

    /**
     * @return mixed
     */
    private function getXmlParameters()
    {
        $xml = new SimpleXMLElement('<SimplifiedVendorSearchParameters></SimplifiedVendorSearchParameters>');
        return $xml->asXML();
    }

    /**
     * @return mixed
     */
    private function getXmlParametersFields()
    {
        $xml = new SimpleXMLElement('<Fields></Fields>');
        return $xml->asXML();
    }
}
