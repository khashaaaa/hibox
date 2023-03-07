<?php
/**
 * Класс для управления реферальными пользователями.
 * @link http://wiki.rkdev.ru/display/opentao/ReferralUserManager
**/
class ReferralUserManager
{
    protected $table = 'referral_users';

    /**
    * @var CMS $cms
    */
    protected $cms;

    /**
    * @var SupportRepository $supportRepository
    */
    protected $supportRepository;

    /**
    * @var ReferralCategoryManager $referralCategoryManager
    */
    protected $referralCategoryManager;

    /**
    * @var CMS $cms
    * @var SupportRepository $supportRepository
    */
    public function __construct($cms, $supportRepository = null)
    {
        $this->cms = $cms;
        $this->cms->Check();
        $this->supportRepository = $supportRepository;
        $this->referralCategoryManager = new ReferralCategoryManager($this->cms);
    }

    /**
     * Добавление реферала
     * @param $login
     * @param $user_id
     * @param $parent_id
     * @param int $category
     * @param int $balance
     * @log Логирование в таблицу referral_logs о результатах
     * @return int $newUserId
     */
    public function Add($login, $user_id, $parent_id, $category = 1, $balance = 0)
    {
        if ($category == 1)
            $category = ReferralCategoryManager::DEFAULT_GROUP_ID;

        $insertData = array(
            'login' => $this->cms->escape($login),
            'parent_id' => (int) $parent_id,
            'user_id' => (int) $user_id,
            'category' => (int) $category,
            'balance' => $balance,
            'added' => time(),
        );
        $sql = 'INSERT INTO `' . $this->table . '` (`' . implode('`,`', array_keys($insertData)) . '`)
                VALUES ("'. implode('","', $insertData) .'")';
        $this->cms->query($sql, array($this->table));

        return (int) $this->cms->insertedId();
    }

    /**
     * Сохранение даных реферала
     * @var ReferralUser $User
     * @return bool
     * @throws Exception
     * @log Логирование в таблицу referral_logs о результатах
     */
    public function Save(ReferralUser $user)
    {
        $this->GetById($user->GetId());

        $updateData = array(
            'parent_id' => (int) $user->GetParentId(),
            'category' => (int) $user->GetCategory(),
            'balance' => $user->GetBalance(),
        );
        $sql = 'UPDATE `' . $this->table . '` SET ';
        foreach ($updateData as $field => $value) {
            $sql .= '`' . $field . '` = "' . $value . '",';
        }
        $sql = rtrim($sql, ',');

        $sql .= ' WHERE `user_id` = ' . (int) $user->GetId();

        return (bool) $this->cms->query($sql, array($this->table));
    }

    /**
    * Удаление реферала по id
    * @log Логирование в таблицу referral_logs о результатах
    * @throws DBException
    */
    public function Remove($userId)
    {
        return (bool) $this->cms->query(
            'DELETE FROM `'. $this->table . '` WHERE user_id = ' . (int) $userId,
            array($this->table)
        );
    }

    /**
    * Получение данных о реферале по id
    * @throws NotFoundException
    */
    public function GetById($userId)
    {
        $sql = 'SELECT * FROM `'. $this->table . '` WHERE user_id = ' . (int)$userId;
        $result = $this->cms->query($sql, array($this->table));
        // TODO: а почему бы не пользоваться готовыми open-source обёртками для MySQL функций?
        if (!$result || !mysqli_num_rows($result)) {
            throw new NotFoundException();
        }
        $row = mysqli_fetch_assoc($result);
        $user = new ReferralUser($row['user_id']);

        $user->SetLogin($row['login']);
        $user->SetParentId($row['parent_id']);
        $user->SetBalance($row['balance']);
        $user->SetCategory($row['category']);
        $user->SetDateAdded($row['added']);

        return $user;
    }

    public function GetByLogin($login)
    {
        $sql = 'SELECT * FROM `'. $this->table . '` WHERE login = "' . $this->cms->escape($login) . '"';
        $result = $this->cms->query($sql, array($this->table));
        // TODO: а почему бы не пользоваться готовыми open-source обёртками для MySQL функций?
        if (!$result || !mysqli_num_rows($result)) {
            throw new NotFoundException();
        }
        $row = mysqli_fetch_assoc($result);
        $user = new ReferralUser($row['user_id']);

        $user->SetLogin($row['login']);
        $user->SetParentId($row['parent_id']);
        $user->SetBalance($row['balance']);
        $user->SetCategory($row['category']);
        $user->SetDateAdded($row['added']);

        return $user;
    }

    /**
     * Получение реферера
     * @var ReferralUser $User
     * @return bool|\ReferralUser
     * @throws Exception
     * @returns ReferralUser $Parent
     */
    public function GetParent(ReferralUser $user)
    {
        $sql = 'SELECT * FROM `'. $this->table . '` WHERE user_id = ' . (int) $user->GetParentId();
        $result = $this->cms->query($sql, array($this->table));
        // TODO: а почему бы не пользоваться готовыми open-source обёртками для MySQL функций?
        if (!$result || !mysqli_num_rows($result)) {
            throw new NotFoundException();
        }
        $row = mysqli_fetch_assoc($result);
        $parent = new ReferralUser($row['user_id']);

        $parent->SetLogin($row['login']);
        $parent->SetParentId($row['parent_id']);
        $parent->SetBalance($row['balance']);
        $parent->SetCategory($row['category']);
        $parent->SetDateAdded($row['added']);

        return $parent;
    }

    /**
     * Получение данных по категории
     * @var ReferralUser $User
     * @throws Exception
     * @returns ReferralCategory $Parent
     */
    public function GetCategoryInfo(ReferralUser $user)
    {
        return $this->referralCategoryManager->GetById($user->GetCategory());
    }

    /**
    * Получение  рефералов по id реферера
    * @throws NotFoundException
    */
    public function GetUsersByParentId($parentId)
    {
		$sql = 'SELECT * FROM  `' . $this->table . '` WHERE parent_id = ' . (int)$parentId;
        $result = $this->cms->query($sql, array($this->table));
        // TODO: а почему бы не пользоваться готовыми open-source обёртками для MySQL функций?		
        if (!$result || !mysqli_num_rows($result)) {
            return array();
        }

        $users = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $users[$row['user_id']] = $row;
        }
        return  $users;
    }
    
    /**
     * Обновление данных если установлен параметр жизни рефера
     * @var integer $dueTime
     */    
    public function updateUserByDueTime($dueTime)
    {    
        $currentTime = time();
        $sql = 'UPDATE `'. $this->table . '` SET `parent_id` = 0 WHERE `added` + ' . $dueTime . ' < ' . $currentTime . ' AND  `parent_id` != 0';
        $result = $this->cms->queryMakeArray($sql, array($this->table));        
    }

    /**
    * Отправить сообщение рефереру в личный кабинет
    * @log Логирование в таблицу referral_logs о результатах
    * @throws DBException, Exception
    */
    public function SendMessageToPrivateOffice($userId, $subject, $message)
    {
        return true;
    }
}
