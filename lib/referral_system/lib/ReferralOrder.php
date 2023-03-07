<?

class ReferralOrder {
    private $orderId = 0;
    private $userId = 0;
    private $parentId = 0;
    private $amount = 0.0;
    private $profitPercent = 0.0;
    private $date = '';

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function GetId() {
        return $this->orderId;
    }

    public function GetUserId() {
        return $this->userId;
    }

    public function SetUserId($userId) {
        $this->userId = $userId;
    }

    public function GetParentId() {
        return $this->parentId;
    }

    public function SetParentId($parentId) {
        $this->parentId = $parentId;
    }

    public function GetAmount() {
        return $this->amount;
    }

    public function SetAmount($amount) {
        $this->amount = $amount;
    }

    public function GetProfitPercent() {
        return $this->profitPercent;
    }

    public function SetProfitPercent($profitPercent) {
        $this->profitPercent = $profitPercent;
    }

    public function GetDate() {
        return $this->date;
    }

    public function SetDate($date) {
        $this->date = $date;
    }

}
?>
