<?php

class ReviewsProvider
{
    public function getItemReview($itemReviewId, $language, $sid)
    {
        OTAPILib2::GetItemReview($language, $sid, $itemReviewId, $answer);
        OTAPILib2::makeRequests();
        return $answer;
    }

    public function searchItemReviews($language, $sid, $itemId, $source, $frameSize, $framePosition = 0)
    {
        $xmlParams = new SimpleXMLElement('<SearchParameters></SearchParameters>');
        $xmlParams->addChild('ItemId', $itemId);
        $xmlParams->addChild('Source', $source);
        $xmlParamsInternalReviews = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());

        OTAPILib2::SearchItemReviews($language, $sid, $xmlParamsInternalReviews, $framePosition, $frameSize, $answer);
        OTAPILib2::makeRequests();

        return $answer->GetResult();
    }

    public function searchReviewedItems($order, $language, $sid, $frameSize, $framePosition = 0)
    {
        $xmlParams = new SimpleXMLElement('<SearchParameters></SearchParameters>');
        if ($order) {
            $xmlParams->addChild('OrderBy', $order);
        }
        $xmlParams = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());

        OTAPILib2::SearchReviewedItems($language, $sid, $xmlParams, $framePosition, $frameSize, $answer);
        OTAPILib2::makeRequests();
        return $answer;
    }

    public function addItemReview($itemId, $configurationId, $orderId, $orderLineId, $text, $score, $language, $sid, $fileIds = array())
    {
        $xmlParams = new SimpleXMLElement('<ItemReviewData></ItemReviewData>');
        $xmlParams->addChild('ItemId', $itemId);
        $xmlParams->addChild('ConfigurationId', $configurationId);
        $xmlParams->addChild('OrderId', $orderId);
        $xmlParams->addChild('OrderLineId', $orderLineId);
        $xmlParams->addChild('Text', $text);
        $xmlParams->addChild('Language', $language);
        if (!empty($score)) {
            $xmlParams->addChild('Rating', $score);
        }
        if (!empty($fileIds)) {
            $filesXml = $xmlParams->addChild('ImageFileIds');
            foreach ($fileIds as $fileId) {
                $filesXml->addChild('Id', $fileId);
            }
        }
        $xmlParams = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());

        OTAPILib2::AddItemReview($language, $sid, $xmlParams, $answer);
        OTAPILib2::makeRequests();
        return $answer;
    }

    public function addAnswerToItemReview($itemReviewId, $language, $text, $sid, $fileIds = array(), $textLanguage = '')
    {
        $xmlParams = new SimpleXMLElement('<ItemReviewAnswerAddData></ItemReviewAnswerAddData>');
        $xmlParams->addChild('Text', $text);
        $xmlParams->addChild('Language', $textLanguage);
        if (!empty($fileIds)) {
            $filesXml = $xmlParams->addChild('ImageFileIds');
            foreach ($fileIds as $fileId) {
                $filesXml->addChild('Id', $fileId);
            }
        }
        $xmlParams = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());

        OTAPILib2::AddAnswerToItemReview($language, $sid, $itemReviewId, $xmlParams, $answer);
        OTAPILib2::makeRequests();
    }

    public function gradeItemReview($itemReviewId, $isPositive, $language, $sid)
    {
        OTAPILib2::GradeItemReview($language, $sid, $itemReviewId, $isPositive, $answer);
        OTAPILib2::makeRequests();
        return $answer;
    }
}