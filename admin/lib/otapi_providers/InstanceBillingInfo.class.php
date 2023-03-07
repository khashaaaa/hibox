<?php

class InstanceBillingInfo
{
    /**
     * @var OtapiBillInfoAnswer
     */
    private $bill = null;

    /**
     * @return OtapiBillInfoAnswer
     */
    public function getBill()
    {
        return $this->bill;
    }

    /**
     * @var OtapiBillInfoListAnswer
     */
    private $bills = null;

    /**
     * @return OtapiBillInfoListAnswer
     */
    public function getBills()
    {
        return $this->bills;
    }
    
    /**
     * @var OtapiBillInfoListAnswer
     */
    private $paidBills = null;

    /**
     * @return OtapiBillInfoListAnswer
     */
    public function getPaidBills()
    {
        return $this->paidBills;
    }

    /**
     * @var OtapiBillInfoListAnswer
     */
    private $unpaidBills = null;

    /**
     * @return OtapiBillInfoListAnswer
     */
    public function getUnpaidBills()
    {
        return $this->unpaidBills;
    }

    /**
     * @return OtapiInstanceOptionsInfoAnswer
     */
    public function getInstanceOptions()
    {
        return $this->instanceOptions;
    }

    /**
     * @return OtapiTariffChangeHistoryAnswer
     */
    public function getRateHistory()
    {
        return $this->rateHistory;
    }

    /**
     * @var OtapiTariffChangeHistoryAnswer
     */
    private $rateHistory = null;

    /**
     * @var OtapiInstanceOptionsInfoAnswer
     */
    private $instanceOptions = null;

    /**
     * @var OtapiCalculateRentResponse
     */
    private $calculateRent = null;
    
    /**
     * @var OtapiCallArchiveAnswer
     */
    private $callArchives = null;
    
    /**
     * @return OtapiCallArchiveAnswer
     */
    public function getCallArchives()
    {
        return $this->callArchives;
    }
    
    /**
     * @return OtapiCalculateRentResponse
     */
    public function getCalculateRent()
    {
        return $this->calculateRent;
    }
    
    /**
    * @var $answers OtapiBillInfoAnswer
    */
    public function initGetBill($language, $sid, $billId, $includeDetails = 'false')
    {
        OTAPILib2::GetBill($language, $sid, $billId, $includeDetails, $this->bill);
    }

    /**
    * @var $answers OtapiBillInfoListAnswer
    */
    public function initSearchBills($language, $sid)
    {
        OTAPILib2::SearchBills($language, $sid, '<BillSearchParameters></BillSearchParameters>', '0', '100', $this->bills);
    }

    /**
    * @var $answers OtapiBillInfoListAnswer
    */
    public function initSearchPaidBills($language, $sid, $offset = 0, $limit = 100)
    {
        OTAPILib2::SearchBills($language, $sid, '<BillSearchParameters><IsPaid>true</IsPaid></BillSearchParameters>', $offset, $limit, $this->paidBills);
    }

    /**
    * @var $answers OtapiBillInfoListAnswer
    */
    public function initSearchUnpaidBills($language, $sid)
    {
        OTAPILib2::SearchBills($language, $sid, '<BillSearchParameters><IsPaid>false</IsPaid></BillSearchParameters>', '0', '100', $this->unpaidBills);
    }

    /**
    * @var $answers OtapiCalculateRentResponse
    */
    public function initCalculateRent($sid)
    {
        $dates = new DateTime('NOW');
        $fromDate = date('c', strtotime($dates->format('Y-m') .'-01'));
        $toDate = date('c', strtotime($dates->format('Y-m-d').' 23:59:59'));        
        OTAPILib2::CalculateRent($sid, $fromDate, $toDate, 'false', $this->calculateRent);
    }
    
    /**
    * @var $answers OtapiCallArchiveAnswer
    */
    public function initGetCallArchives($xmlSearchSettings)
    {               
        OTAPILib2::GetCallArchives($xmlSearchSettings, $this->callArchives);
    }
    
    /**
    * @var $answers OtapiCalculateRentResponse
    */
    public function initCalculateRentToBill($sid, $fromDate, $toDate)
    {               
        OTAPILib2::CalculateRent($sid, $fromDate, $toDate, 'true', $this->calculateRent);
    }
    
    /**
    * @var $answers OtapiTariffChangeHistoryAnswer
    */
    public function initRateHistory($language, $sid)
    {
        OTAPILib2::GetTariffChangeHistory($language, $sid, $this->rateHistory);
    }
    
    /**
    * @var $answers OtapiInstanceOptionsInfoAnswer
    */
    public function initInstanceOptions($language, $sid)
    {
        OTAPILib2::GetInstanceOptionsInfo($language, $sid, $this->instanceOptions);
    }
    
    public function doRequests()
    {
        OTAPILib2::makeRequests();
    }
}