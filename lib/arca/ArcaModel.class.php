<?php
class ArcaModel
{
    const PAYMENT_BY_VIRTUAL_CARD = 'arca.virttual';
    const PAYMENT_BY_REAL_CARD = 'arca.emv';

    /**
     * @var CMS
     */
    protected $cms;

    public function __construct(){
        $this->cms = new CMS();
        if(!CMS::CheckStatic()){
            throw new DBException('Connection error: ' . mysqli_error(General::getCms()->getLink()), DBException::CONNECTION_ERROR);
        }
    }

    public function savePaymentForm($formParameters){
        $exists = $this->cms->checkTable('site_arca_payments');
        if(!$exists) throw new DBException('Can\'t create table', DBException::CANNOT_CREATE_TABLE);

        $formParametersSql = array_map(array($this, 'createAssignment'), $formParameters);
        $sql = "INSERT INTO `site_arca_payments` SET " . implode(', ', $formParametersSql);
        $insertResult = General::getCms()->query($sql);

        if(!$insertResult)
            throw new DBException('Can\'t save payment parameters: '.mysqli_error(General::getCms()->getLink()));

        return General::getCms()->insertedId();
    }

    public function getPayment($paymentId){
        $paymentId = intval($paymentId);

        $sql = "SELECT * FROM `site_arca_payments` WHERE `id` = $paymentId";
        $getResult = General::getCms()->query($sql);

        if(!$getResult)
            throw new DBException('Can\'t get payment', DBException::QUERY_ERROR);

        return mysqli_fetch_assoc($getResult);
    }

    public function getPaymentByOrderId($orderId){
        $orderId = General::getCms()->escape($orderId);

        $sql = "SELECT * FROM `site_arca_payments` WHERE `orderID` = $orderId";
        $getResult = General::getCms()->query($sql);

        if(!$getResult)
            throw new DBException('Can\'t get payment', DBException::QUERY_ERROR);

        return mysqli_fetch_assoc($getResult);
    }

    public function getPaidPayments($limit = 1, $orderBy = 'id', $orderDir = 'ASC'){
        $limit = intval($limit);
        $orderBy = General::getCms()->escape($orderBy);
        $orderDir = General::getCms()->escape($orderDir);
        $paymentType = self::PAYMENT_BY_VIRTUAL_CARD;

        $sql = "SELECT * FROM `site_arca_payments` WHERE `user_paid` = 1 AND `confirmed` IS NULL
                AND `payment_type` = '$paymentType'
                ORDER BY $orderBy $orderDir LIMIT $limit";
        $getResult = General::getCms()->query($sql);
        if(!$getResult)
            throw new DBException('Can\'t get payments', DBException::QUERY_ERROR);

        $payments = array();
        while($row = mysqli_fetch_assoc($getResult)){
            $payments[] = $row;
        }

        return $payments;
    }

    public function getConfirmedPayments($limit = 1, $orderBy = 'id', $orderDir = 'ASC'){
        $limit = intval($limit);
        $orderBy = General::getCms()->escape($orderBy);
        $orderDir = General::getCms()->escape($orderDir);
        $paymentRealCardType = self::PAYMENT_BY_REAL_CARD;

        $sql = "SELECT * FROM `site_arca_payments`
                WHERE
                  (`confirmed` = 1 AND `notified` IS NULL)
                  OR
                  (`user_paid` = 1 AND `notified` IS NULL AND `payment_type` = '$paymentRealCardType')
                ORDER BY $orderBy $orderDir LIMIT $limit";
        $getResult = General::getCms()->query($sql);
        if(!$getResult)
            throw new DBException('Can\'t get payments', DBException::QUERY_ERROR);

        $payments = array();
        while($row = mysqli_fetch_assoc($getResult)){
            $payments[] = $row;
        }

        return $payments;
    }

    public function deletePayment($paymentId){
        $paymentId = intval($paymentId);

        $sql = "DELETE FROM `site_arca_payments` WHERE `id` = $paymentId";
        $deleteResult = General::getCms()->query($sql);

        if(!$deleteResult)
            throw new DBException('Can\'t delete payment', DBException::QUERY_ERROR);

        return true;
    }

    public function markPaymentPaid($paymentId, $rrn){
        $paymentId = intval($paymentId);
        $rrn = General::getCms()->escape($rrn);
        $sql = "UPDATE `site_arca_payments` SET `user_paid` = 1, `rrn` = '$rrn' WHERE `id` = $paymentId";

        $updateResult = General::getCms()->query($sql);
        if(!$updateResult)
            throw new DBException($sql.'; '.mysqli_error(General::getCms()->getLink()), DBException::QUERY_ERROR);

        return true;
    }

    public function markPaymentConfirmed($paymentId){
        $paymentId = intval($paymentId);
        $sql = "UPDATE `site_arca_payments` SET `confirmed` = 1 WHERE `id` = $paymentId";

        $updateResult = General::getCms()->query($sql);
        if(!$updateResult)
            throw new DBException($sql.'; '.mysqli_error(General::getCms()->getLink()), DBException::QUERY_ERROR);

        return true;
    }

    public function markPaymentNotified($paymentId){
        $paymentId = intval($paymentId);
        $sql = "UPDATE `site_arca_payments` SET `notified` = 1 WHERE `id` = $paymentId";

        $updateResult = General::getCms()->query($sql);
        if(!$updateResult)
            throw new DBException($sql.'; '.mysqli_error(General::getCms()->getLink()), DBException::QUERY_ERROR);

        return true;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function createAssignment($p){
        $name = General::getCms()->escape($p['Name']);
        $value = General::getCms()->escape($p['Value']);
        return "`$name` = '$value'";
    }
}
