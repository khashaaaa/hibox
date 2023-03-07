<?

class ReferralLogManager {
    /**
    * @var CMS $cms
    */
    protected $cms;
	
    protected $table = 'referral_logs';
 
    /**
    * @var CMS $cms
    */
    public function __construct($cms) {
        $this->cms = $cms;
        $this->cms->Check();
    }
 
    /**
    * @var ReferralCategory $Category
    * @log Логирование в таблицу referral_logs о результатах
    * @throws DBException, Exception
    * @return int $newCategoryId
    */
    public function Add($code, $action, $date = '') {
        $date = ($date) ? $date : date('Y-m-d h-i-s');
        $insertData = array(
            'code' => $this->cms->escape($code),
            'action' => $this->cms->escape($action),
            'date' => $date
        );
        $sql = 'INSERT INTO `' . $this->table . '` (`' . implode('`,`', array_keys($insertData)) . '`)
                VALUES ("' . implode('","', $insertData) . '")';
        $this->cms->query($sql, array($this->table));

        return (int) $this->cms->insertedId();
    }
}
