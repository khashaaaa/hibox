<?php
class AddBonusInfo {
    private $userId;
    private $amount;
    private $comment;

    public function __construct($userId, $amount, $comment){
        $this->userId = $userId;
        $this->amount = $amount;
        $this->comment = $comment;
    }

    public function toXML(){
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $addBonusInfo = $dom->createElement('AddBonusInfo');

        $element = $dom->createElement('UserId', htmlspecialchars($this->userId));
        $addBonusInfo->appendChild($element);

        $element = $dom->createElement('Amount', htmlspecialchars($this->amount));
        $addBonusInfo->appendChild($element);

        $element = $dom->createElement('Comment', htmlspecialchars($this->comment));
        $addBonusInfo->appendChild($element);

        $dom->appendChild($addBonusInfo);

        return $dom->saveXML($addBonusInfo);
    }
}