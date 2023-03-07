<?

class ReferralCategoryManager {

    const DEFAULT_GROUP_ID = 1;
    /**
    * @var CMS $cms
    */
    protected $cms;
	
    protected $table = 'referral_category';
 
    /**
    * @var CMS $cms
    */
    public function __construct($cms) {
        $this->cms = $cms;
        $this->cms->Check();
        $this->CheckBaseCategoryExists();
    }
 
    /**
    * @var ReferralCategory $Category
    * @log Логирование в таблицу referral_logs о результатах
    * @throws DBException, Exception
    * @return int $newCategoryId
    */
    public function Add($Category) {
        //
        $insertData = array(
            'groupName' => '"' . $Category->GetGroupName() . '"',
            'catId' => ($Category->GetId() === null) ? 'NULL' : '"' . $Category->GetId() . '"',
            'minOverallPayment' => '"' . $Category->GetMinOverallPayment() . '"',
            'profitPercent' => '"' . $Category->GetProfitPercent(). '"'
        );
        $sql = 'INSERT INTO `' . $this->table . '` (`' . implode('`,`', array_keys($insertData)) . '`)
                VALUES (' . implode(',', $insertData) . ')';
        $this->cms->query($sql, array($this->table));

        return (int) $this->cms->insertedId();
    }

    /**
    * @throws DBException
    * @log Логирование в таблицу referral_logs о результатах
    */
    public function Remove($categoryId){
        //
        $sql = "DELETE FROM `" . $this->table . "` WHERE catId = '$categoryId'";
        $query = $this->cms->query($sql, array(1 => $this->table));
        return $query;
    }

    /**
    * @throws NotFoundException
    */
    public function GetById($categoryId) {
        $sql = "SELECT * FROM  `" . $this->table . "` WHERE catId = '$categoryId'";
        $query = $this->cms->query($sql, array(1 => $this->table));
        if (!$query || !mysqli_num_rows($query)) {
            throw new NotFoundException();
        }
        $row = mysqli_fetch_assoc($query);
        $CatData = new ReferralCategory();

        $CatData->SetGroupName($row['groupName']);
        $CatData->SetId($row['catId']);
        $CatData->SetMinOverallPayment($row['minOverallPayment']);
        $CatData->SetProfitPercent($row['profitPercent']);
        return $CatData;
    }

    /**
     * @param $categoryId
     * @return array(ReferralUser) $users
     */
    public function GetUsersByCategory($categoryId) {
        $sql = "SELECT * FROM  `referral_users` WHERE category = '" . $categoryId . "' ORDER BY `added` DESC";
        $query = $this->cms->query($sql, array(1 => 'referral_users'));
        $ReturnArray = array();
        while ($row = mysqli_fetch_assoc($query)) {
            $tmp = new ReferralUser($row['user_id']);
            $tmp->SetLogin($row['login']);
            $tmp->SetBalance($row['balance']);
            $tmp->SetCategory($row['category']);
            $tmp->SetDateAdded($row['added']);
            $ReturnArray[] = $tmp;
        }
        return $ReturnArray;
    }

    /**
     * @param $amount
     * @throws NotFoundException
     * @return int $categoryId
     */
    public function PickUpCategoryIdByAmount($amount) {
        $amount = $this->cms->escape($amount);
        $sql = "SELECT `catId` FROM `" . $this->table . "`"
            . " WHERE `minOverallPayment` <= '" . $amount ."'"
            . " ORDER BY `minOverallPayment` DESC LIMIT 1";
        $query = $this->cms->query($sql, array($this->table));
        if (!$query || !mysqli_num_rows($query)) {
            throw new NotFoundException();
        }
        $row = mysqli_fetch_assoc($query);

        return $row['catId'];
    }

    public function GetGroupName(){
        /**
         * @TODO Заполнить категорию
         */
        return 'Need to fill';
    }

    public function GetAllCategories(){
        $sql = "SELECT * FROM `" . $this->table . "` ORDER BY `minOverallPayment` ASC";
        return $this->cms->queryMakeArray($sql, array($this->table));
    }

    private function CheckBaseCategoryExists(){
        $sql = "SELECT * FROM `" . $this->table . "` WHERE catId = " . self::DEFAULT_GROUP_ID;
        $data = $this->cms->queryMakeArray($sql, array($this->table));
        if (count($data) == 0) {
            $C = new ReferralCategory();
            $C->SetId(self::DEFAULT_GROUP_ID);
            $C->SetGroupName(''.Lang::get('Participant').'');
            $C->SetMinOverallPayment(0);
            $C->SetProfitPercent(0);
            $this->Add($C);
        }
    }
}
