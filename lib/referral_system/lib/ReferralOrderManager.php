<?php

class ReferralOrderManager {
    protected $table = 'referral_orders';

    /**
    * @var CMS $cms
    */
    protected $cms;

    /**
    * @var CMS $cms
    * @var SupportRepository $supportRepository
    */
    public function __construct($cms)
    {
        $this->cms = $cms;
        $this->cms->Check();
    }

    /**
     * Добавление заказа
     * @param $order_id
     * @param $user_id
     * @param $parent_id
     * @param $amount
     * @param $date
     * @param $profit_percent
     * @log Логирование в таблицу referral_logs о результатах
     * @return int $newOrderId
     */
    public function Add($order_id, $user_id, $parent_id, $amount, $profit_percent, $date = 0)
    {
        $date = ($date) ? $date : date('Y-m-d h-i-s');
        $insertData = array(
            'order_id' => $this->cms->escape($order_id),
            'user_id' => (int) $user_id,
            'parent_id' => (int) $parent_id,
            'amount' => (float) $amount,
            'profit_percent' => (float) $profit_percent,
            'date' => $date
        );
        $sql = 'INSERT INTO `' . $this->table . '` (`' . implode('`,`', array_keys($insertData)) . '`)
                VALUES ("'. implode('","', $insertData) .'")';
        $this->cms->query($sql, array($this->table));

        return (int) $this->cms->insertedId();
    }

    public function Delete($order_id){
        $sql = 'DELETE FROM `' . $this->table . '` WHERE `order_id` = "' . $this->cms->escape($order_id) . '"';
        $this->cms->query($sql, array($this->table));
    }

    /**
    * Получение заказа по id заказа из БЛ
    * @throws NotFoundException
    */
    public function GetById($orderId)
    {
        $sql = "SELECT * FROM `" . $this->table . "` WHERE `order_id` = '" . $this->cms->escape($orderId) . "' LIMIT 1";
        $query = $this->cms->query($sql, array($this->table));
        if (mysqli_num_rows($query) <= 0) {
            return false;
        }
        $row = mysqli_fetch_assoc($query);
        $OrderData = new ReferralOrder($row['order_id']);

        $OrderData->SetUserId($row['user_id']);
        $OrderData->SetParentId($row['parent_id']);
        $OrderData->SetAmount($row['amount']);
        $OrderData->SetProfitPercent($row['profit_percent']);
        $OrderData->SetDate($row['date']);
        return $OrderData;
    }

    /**
     * Получение суммы всех заказов для реферера
     *
     * @param $parent_id
     * @throws NotFoundException
     * @return float
     */
    public function CalculateAmountUser($parent_id)
    {
        $sql = 'SELECT SUM(`amount`) as `sum` FROM `' . $this->table . '` WHERE `parent_id` = "' . (int) $parent_id . '"';
        $query = $this->cms->query($sql, array($this->table));
        if (!$query || !mysqli_num_rows($query)) {
            throw new NotFoundException();
        }
        $row = mysqli_fetch_assoc($query);

        return $row['sum'];

    }

    /**
     * Получение  заказов рефералов по id реферера
     * @throws NotFoundException
     */
    public function GetOrdersByUserId($userId)
    {
        $sql = 'SELECT * FROM `' . $this->table . '` WHERE user_id = ' . (int)$userId . ' ORDER BY  `date` DESC';
        $result = $this->cms->query($sql, array($this->table));
        // TODO: а почему бы не пользоваться готовыми open-source обёртками для MySQL функций?
        if (!$result || !mysqli_num_rows($result)) {
            return false;
        }
        $orders = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        return $orders;
    }

}
