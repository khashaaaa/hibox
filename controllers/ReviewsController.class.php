<?php

class ReviewsController extends GeneralContoller
{
    private $reviewsProvider;
    public function __construct()
    {
        parent::__construct();
        $this->reviewsProvider = new ReviewsProvider();
    }

    public function getReviewAction()
    {
        try {
            $itemReviewId = $this->request->get('reviewId');
            $language = Session::getActiveLang();
            $sid = User::getObject()->getSid();

            $response = $this->reviewsProvider->getItemReview($itemReviewId, $language, $sid);
            $result = $response->GetResult();

            $reviewLayout = $this->renderPartial('reviews/review-block', [
                'reviews' => $result
            ], ['path' => CFG_VIEW_ROOT]);
            $this->sendAjaxResponse(array('review' => $reviewLayout));

        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }

    public function addItemReviewAction()
    {
        try {
            $itemId = $this->request->post('itemId');
            $configurationId = $this->request->post('configId');
            $orderId = $this->request->post('orderId');
            $orderLineId = $this->request->post('orderLineId');
            $text = $this->escape($this->request->post('text'));
            $score = $this->escape($this->request->post('score'));
            $fileIds = $this->request->post('fileId');
            $language = Session::getActiveLang();
            $sid = User::getObject()->getSid();

            $response = $this->reviewsProvider->addItemReview($itemId, $configurationId, $orderId, $orderLineId, $text, $score, $language, $sid, $fileIds);

            $this->sendAjaxResponse(array('id' => $response->GetResult()->asString()));
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }

    public function gradeItemReviewAction()
    {
        $itemReviewId = $this->request->post('itemreviewid');
        $isPositive = $this->request->post('ispositive');
        $sid = User::getObject()->getSid();

        try {
            $reviews = $this->reviewsProvider->gradeItemReview($itemReviewId, $isPositive, Session::getActiveLang(), $sid);

            $this->sendAjaxResponse(array(
                'positive' => $reviews->GetResult()->GetPositiveGrade(),
                'negative' => $reviews->GetResult()->GetNegativeGrade()
            ));

        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    }

    public function addAnswerToItemReviewAction()
    {
        $itemReviewId = $this->request->post('itemreviewid');
        $text = $this->request->post('reviewanswertext');
        $fileIds = $this->request->post('fileId');
        $sid = User::getObject()->getSid();

        $result = array('status'=>'done', $itemReviewId, $text);
        try {
            $this->reviewsProvider->addAnswerToItemReview($itemReviewId, Session::getActiveLang(), $text, $sid, $fileIds);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse($result);
    }
}