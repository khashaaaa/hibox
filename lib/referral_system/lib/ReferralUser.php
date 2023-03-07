<?php
/**
 * Класс для представляения информации по реферале. Служит для однотипного представления данных о пользователе в реферальной системе.
 * @link http://wiki.rkdev.ru/display/opentao/ReferralUser
**/
class ReferralUser
{
    private $login = '';     //Логин пользователя (чтобы не дергать из сервисов или сессий постоянно)
    private $userId = 0;     //Id пользователя
    private $balance = 0.0;  //Бонусный счет
    private $date_added = null;   //Дата вступления в программу
    private $category = 0;   //Статус пользователя в реферальной программе
    private $parentId = 0;   //Рефёрер

    public function __construct ($userId)
    {
        $this->userId = $userId;
    }

    public function GetLogin() {
       return $this->login;
    }

    public function SetLogin($login) {
        $this->login = $login;
    }

    public function GetParentId() {
       return $this->parentId;
    }

    public function SetParentId($parentId) {
        $this->parentId = $parentId;
    }

    public function GetId() {
        return $this->userId;
    }

    public function GetBalance() {
        return $this->balance ? $this->balance : 0.0;
    }

    public function SetBalance($balance) {
        $this->balance = $balance;
    }

    public function GetDateAdded() {
        return $this->date_added ? date("Y-m-d", $this->date_added) : '';
    }

    public function SetDateAdded($date) {
        $this->date_added = $date;
    }

    public function GetCategory() {
        return $this->category;
    }

	//передается id
    public function SetCategory($category) {
        $this->category = $category;
    }
}
