<?php

class ShopCommentsController extends GeneralContoller
{
    private $ShopReviews;

    public function __construct()
    {
        parent::__construct(true);
        $this->ShopReviews = new ShopReviewsRepository($this->cms);
    }

    public function defaultAction()
    {
        // сохранить новый отзыв
        if ($this->request->isPost()) {
            try {
                $result = ($this->ShopReviews->AddReview($this->request->allPost())) ? 'Ok' : 'Error';
                Cookie::set('ShopReviewsAddedResult', $result, time() + (60 * 60 * 24 * 60));
            } catch (Exception $e) {
                Session::setError($e->getMessage());
            }
            return $this->redirect($this->request->getReferrer());
        }

        // оценить отзыв
        if (RequestWrapper::getValueSafe('calc')) {
            try {
                $this->ShopReviews->SetRatingReview(RequestWrapper::getValueSafe('calc'), RequestWrapper::getValueSafe('to'));
            } catch (Exception $e) {
                Session::setError($e->getMessage());
            }
            return $this->redirect($this->request->getReferrer());
        }
        $alreadyVoted = Session::get('shop_rating', []);
        $comments = array();
        $perpage = General::getConfigValue('shop_reviews_perpage', 10);
        $page = $this->request->getValue('page', 1);
        $from = ($page - 1) * $perpage;

        try {
            $comments = $this->ShopReviews->GetReviews($from, $perpage);
            $comments = $comments['rows'];
            $totalCount = $this->ShopReviews->GetModeratedCount();
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }

        $shopReviewsAddedResult = Cookie::get('ShopReviewsAddedResult', false);
        if ($shopReviewsAddedResult) {
            Cookie::clear('ShopReviewsAddedResult');
        }

        return $this->render('controllers/shop-comments/list', [
            'alreadyVoted' => $alreadyVoted,
            'comments' => $comments,
            'shopReviewsAddedResult' => $shopReviewsAddedResult,
            'paginator' => new Paginator($totalCount, $page, $perpage),
        ]);
    }

    public function renderLastCommentsAction()
    {
        if (! CMS::IsFeatureEnabled('ShopComments')) {
            return '';
        }

        $comments = array();

        try {
            $limit = General::getConfigValue('shopreviews_main', 5);
            $result = $this->ShopReviews->GetReviews(0, $limit);
            $comments = $result['rows'];
            // определяем можно ли голосовать за отзыв
            foreach ($comments as $key => $comment) {
                if (
                    (Session::get('shop_rating') === null || 
                    !is_array(Session::get('shop_rating')) === null || 
                    !in_array($comment['review_id'], Session::get('shop_rating'))) &&
                    $this->getUser()->isAuthenticated()
                ) {
                    $comments[$key]['allowedVote'] = true;
                } else {
                    $comments[$key]['allowedVote'] = false;
                }
            }
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }

        return $this->renderPartial('controllers/shop-comments/last', [
            'comments' => $comments,
        ]);
    }
}
