<?php

class ReviewsProvider extends Repository
{
    public function __construct($cms)
    {
        parent::__construct($cms);
    }

    public function getProductsReviewsForOperator($language, $sid, $searchParameters, $frameSize, $framePosition = 0)
    {
        $xmlParams = new SimpleXMLElement('<SearchParameters></SearchParameters>');
        if (! empty($searchParameters['userId'])) {
            $xmlParams->addChild('UserId', $searchParameters['userId']);
        }
        if (! empty($searchParameters['itemId'])) {
            $xmlParams->addChild('ItemId', $searchParameters['itemId']);
        }
        if (! empty($searchParameters['orderId'])) {
            $xmlParams->addChild('OrderId', OrdersProxy::normalizeOrderIdForOtapi($searchParameters['orderId']));
        }
        if (! empty($searchParameters['reviewIds'])) {
            $ids = explode(';', $searchParameters['reviewIds']);
            $xmlParamsTmp = $xmlParams->addChild('ReviewIds');
            foreach ($ids as $id) {
                $xmlParamsTmp->addChild('Id', $id);
            }
        }
        if (! empty($searchParameters['isApproved'])) {
            $xmlParams->addChild('IsApproved', 'false');
        }

        $xmlParams = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());

        OTAPILib2::SearchItemReviewsForOperator($language, $sid, $xmlParams, $framePosition, $frameSize, $answer);
        OTAPILib2::makeRequests();

        return array('count' => $answer->GetResult()->GetTotalCount(), 'rows' => $answer->GetResult()->GetContent());
    }

    public function acceptReview($language, $sid, $itemReviewIds)
    {
        OTAPILib2::ApproveItemReviews($language, $sid, $itemReviewIds, $answer);
        OTAPILib2::makeRequests();
    }

    public function deleteReview($language, $sid, $itemReviewIds)
    {
        OTAPILib2::DeleteItemReviews($language, $sid, $itemReviewIds, $answer);
        OTAPILib2::makeRequests();
    }

    public function addAnswerToItemReviewForOperator($language, $languageAnswer, $sid, $reviewId, $message, $fileIds = array())
    {
        if (General::IsFeatureEnabled('ItemReviews')) {
            $xmlAddData = new SimpleXMLElement('<ItemReviewAnswerAddData></ItemReviewAnswerAddData>');
            $xmlAddData->addChild('Text', $message);
            $xmlAddData->addChild('Language', $languageAnswer);
            $xmlAddData->addChild('Language', $languageAnswer);
            if (!empty($fileIds)) {
                $filesXml = $xmlAddData->addChild('ImageFileIds');
                foreach ($fileIds as $fileId) {
                    $filesXml->addChild('Id', $fileId);
                }
            }
            $xmlAddData = str_replace('<?xml version="1.0"?>', '', $xmlAddData->asXML());

            OTAPILib2::AddAnswerToItemReviewForOperator($language, $sid, $reviewId, $xmlAddData, $answer);
            OTAPILib2::makeRequests();
        }
    }

    public function rewardItemReview($language, $sessionId, $itemReviewId, $amount, $comment)
    {
        OTAPILib2::rewardItemReview($language, $sessionId, $itemReviewId, $amount, $comment, $answer);
        OTAPILib2::makeRequests();
        return $answer;
    }
}
