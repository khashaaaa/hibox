<?php

class Reviews extends GeneralUtil
{
    protected $_template = 'reviews';
    protected $_template_path = 'reviews/';
    protected $reviewsProvider;

    public function __construct()
    {
        parent::__construct();
        $this->reviewsProvider = new ReviewsProvider($this->cms);
    }

    public function defaultAction($request)
    {
        $page = $this->getPageDisplayParams($request);
        $reviews = array('count' => 0, 'rows' => array());

        try {
            $language = Session::getActiveAdminLang();
            $sid = Session::get('sid');

            $searchParameters = array(
                'userId' => $request->get('user'),
                'orderId' => $request->get('order'),
                'itemId' => $request->get('item'),
                'isApproved' => $request->get('new')
            );

            $reviews = $this->reviewsProvider->getProductsReviewsForOperator($language, $sid, $searchParameters, $page['limit'], $page['offset']);
        } catch (Exception $e) {
            ErrorHandler::registerError($e);
        }
        
        $this->tpl->assign('reviews', $reviews['rows']);
        $this->tpl->assign('paginator', new Paginator($reviews['count'], $page['number'], $page['limit']));
        print $this->fetchTemplate();
    }

    /**
     * @param $reviews OtapiArrayOfItemReviewInfo
     */
    private function reviewsInfoToArray($reviews)
    {
        $reviewItem = array();
        foreach ($reviews->GetItem() as $reviewItemTmp) {
            $answers = array();
            foreach ($reviewItemTmp->GetAnswers()->GetAnswer() as $answer) {
                $answerImageUrls = $answer->getImageUrls()->GetImageUrl();
                $answerImagePreviewUrls = $answer->getImagePreviewUrls()->GetImageUrl();
                $answers[] = array(
                    'AuthorName' => $answer->GetAuthorName(),
                    'Text' => $answer->GetText(),
                    'Language' => $answer->GetLanguage(),
                    'OriginalText' => $answer->GetOriginalText(),
                    'OriginalLanguage' => $answer->GetOriginalLanguage(),
                    'OperatorId' => $answer->GetOperatorId(),
                    'IsOperator' => $answer->IsOperator(),
                    'ImageUrls' => (!empty($answerImageUrls)) ? $answerImageUrls->toArray() : array(),
                    'ImagePreviewUrls' => (!empty($answerImagePreviewUrls)) ? $answerImagePreviewUrls->toArray() : array()
                );
            }
            $reviewImageUrls = $reviewItemTmp->getImageUrls()->GetImageUrl();
            $reviewImagePreviewUrls = $reviewItemTmp->getImagePreviewUrls()->GetImageUrl();
            $reviewItem = array(
                'Id' => $reviewItemTmp->GetId()->asString(),
                'UserId' => $reviewItemTmp->GetUserId()->asString(),
                'ItemId' => $reviewItemTmp->GetItemId(),
                'ConfigurationId' => $reviewItemTmp->GetConfigurationId(),
                'OrderId' => OrdersProxy::normalizeOrderId($reviewItemTmp->GetOrderId()->asString()),
                'OrderLineId' => $reviewItemTmp->GetOrderLineId()->asString(),
                'CreatedTime' => date('d.m.Y H:i', strtotime($reviewItemTmp->GetCreatedTime())),
                'Text' => $reviewItemTmp->GetText(),
                'Language' => $reviewItemTmp->GetLanguage(),
                'OriginalLanguage' => $reviewItemTmp->GetOriginalLanguage(),
                'IsApproved' => $reviewItemTmp->IsApproved(),
                'UserName' => $reviewItemTmp->GetUserName(),
                'Rating' => $reviewItemTmp->GetRating(),
                'PositiveGrade' => $reviewItemTmp->GetPositiveGrade(),
                'NegativeGrade' => $reviewItemTmp->GetNegativeGrade(),
                'IsRewarded' => $reviewItemTmp->IsRewarded(),
                'Answers' => $answers,
                'ImageUrls' => (!empty($reviewImageUrls)) ? $reviewImageUrls->toArray() : array(),
                'ImagePreviewUrls' => (!empty($reviewImagePreviewUrls)) ? $reviewImagePreviewUrls->toArray() : array()
            );
        }
        return $reviewItem;
    }

    public function getReviewForOperatorAction($request)
    {
        $this->_template = 'review-info';

        $review = array();
        $availableLanguages = array();
        $canBeAnswered = General::IsFeatureEnabled('ItemReviews');

        try {
            $language = Session::getActiveAdminLang();
            $sid = Session::get('sid');

            $searchParameters = array(
                'reviewIds' => $request->post('reviewId')
            );
            $review = $this->reviewsProvider->getProductsReviewsForOperator($language, $sid, $searchParameters, 1);
            $review = $this->reviewsInfoToArray($review['rows']);

            $availableLanguages = $this->languagesProvider->GetActiveLanguages();
            $this->tpl->assign('review', $review);
            $this->tpl->assign('availableLanguages', $availableLanguages);
            $this->tpl->assign('canBeAnswered', $canBeAnswered);

            $content = $this->fetchTemplateWithoutHeaderAndFooter();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse(array(
            'content' => $content,
            'review' => $review,
            'availableLanguages' => $availableLanguages,
            'canBeAnswered' => $canBeAnswered
        ));
    }
    
    public function acceptReviewAction($request)
    {
        try {
            $language = Session::getActiveAdminLang();
            $sid = Session::get('sid');
            $ids = $request->getValue('ids');

            $this->reviewsProvider->acceptReview($language, $sid, $ids);

        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();        
    }

    public function deleteReviewAction($request)
    {
        try {
            $language = Session::getActiveAdminLang();
            $sid = Session::get('sid');
            $ids = $request->getValue('ids');
    
            $this->reviewsProvider->deleteReview($language, $sid, $ids);

        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function settingsAction($request)
    {
        $this->_template = 'settings';

        try {
            $lang = Session::getActiveAdminLang();
            $sid = Session::get('sid');
            $settings = array();
            OTAPILib2::GetItemReviewSettings($lang, $sid, 'true', $settings);
            OTAPILib2::makeRequests();

            $settings = $settings->GetResult()->GetRawData();

            $this->tpl->assign('settings', $settings);
            $this->tpl->assign('updateUrl', '?cmd=reviews&do=updateSettings');
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }
        print $this->fetchTemplate();
    }

    public function updateSettingsAction($request)
    {
        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->post('type');

        try {
            $params = explode(MetaUI::NODES_SEPARATOR, $name);
            if (is_array($params) && count($params) > 0) {
                $xmlParameters = MetaUI::generateSingleParamXml('UpdateData', $params, $value, $type);
                $answer = false;
                OTAPILib2::UpdateItemReviewSettings(Session::getActiveAdminLang(), Session::get('sid'), $xmlParameters, $answer);
                OTAPILib2::makeRequests();
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
    }

    public function addAnswerToItemReviewAction($request)
    {
        $reviewId = $request->post('reviewId');
        $language = $request->post('language');
        $message = $request->post('message');
        $fileIds = $request->post('fileId');
        $sid = Session::get('sid');

        try {
            $this->reviewsProvider->addAnswerToItemReviewForOperator($language, $language, $sid, $reviewId, $message, $fileIds);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse();
    }

    public function rewardItemReviewAction($request)
    {
        $language = Session::getActiveAdminLang();
        $sessionId = Session::get('sid');
        $itemReviewId = $request->post('reviewId');
        $amount = $request->post('amount');
        $comment = $request->post('comment');

        try {
            $this->reviewsProvider->RewardItemReview($language, $sessionId, $itemReviewId, $amount, $comment);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
    }
}
