<?

class ReferralCategory {
    private $groupName = '';
    private $catId = 0;
    private $minOverallPayment = 0.0;
    private $profitPercent = 0.0;

    public function GetGroupName() {
		return $this->groupName;
    }

    public function SetGroupName($groupName) {
        $this->groupName = $groupName;
    }

    public function GetId() {
        return $this->catId;
    }

    public function SetId($catId) {
        $this->catId = $catId;
    }

    public function GetMinOverallPayment() {
        return $this->minOverallPayment;
    }

    public function SetMinOverallPayment($payment){
        $this->minOverallPayment = $payment;
    }

    public function GetProfitPercent() {
        return $this->profitPercent;
    }

    public function SetProfitPercent($profitPercent){
        $this->profitPercent = $profitPercent;
    }

}
?>
