<?php

class UserHistoryRepository extends Repository
{
	/**
	 * Извлечение данных о просмотренных товарах
	 */
    public function getHistoryInfo()
    {
        $userLogin = $this->cms->escape(Session::getUserData('username'));
        $userHistory = $this->cms->queryMakeArray('SELECT * FROM `userhistory`
            WHERE `user` = "' . $userLogin . '" AND `id` != "" ORDER BY `tme` DESC');

        return $userHistory;
    }

    /**
     * Проверка на наличие записи о товаре
     * @param string $usrlogin      Логин пользователя
     * @param string $id            Идентификатор товара
     */
    public function isExistUserHistoryItem($usrlogin, $id)
    {
        $res = $this->cms->query('select count(name) from `userhistory`
            WHERE `user` = "' . $usrlogin . '" AND `id` = "' . $id . '"');
        $row  = mysqli_fetch_array($res);
        return $row[0];
    }

    /**
     * Сохранение данных в локальную базу о просмотре
     * @param string $userLogin      Логин пользователя
     * @param string $id            Идентификатор товара
     * @param string $nme           Наименование товара
     * @param string $price         Цена
     * @param string $promo_price   Цена со скидкой
     * @param string $pic           url изображения товара
     */
    public function saveUserHistoryItem($userLogin, $id, $nme, $price, $promo_price, $pic)
    {
        if (!(float)$promo_price) { 
            $promo_price = NULL;
        }

        $this->cms->query("INSERT INTO `userhistory` (`user`, `id` , `name` , `price` ,
            `promo_price` ,`pic`, `tme`) 
            VALUES ('" . $userLogin . "','" . $id . "','" . $this->cms->escape($nme) . "','" .
            $price . "','" . $promo_price . "','" . $this->cms->escape($pic) . "','" .
            time() . "');");
    }


    /**
     * Обновление записи опросмотре  в локальной базе
     * @param string $userLogin      Логин пользователя
     * @param string $id            Идентификатор товара
     * @param string $promo_price   Цена со скидкой
     */
    public function updateUserHistoryItem($userLogin, $id, $promo_price)
    {
        $sql = "UPDATE `userhistory` SET `tme` = '" . time() . "' ";
        if ((float)$promo_price) {
            $sql .= ", `promo_price` = '" . $promo_price . "' ";
        }
        $sql .= "WHERE `user` = '" . $userLogin . "'
                AND `id` = '" . $id . "'";
        $this->cms->query($sql);
    }

	/**
	 * Добавление данных в локальную базу о просмотре
	 * @param string $id 			Идентификатор товара
	 * @param string $nme 			Наименование товара
	 * @param string $price      	Цена
	 * @param string $promo_price  	Цена со скидкой
	 * @param string $pic      		url изображения товара
     * @return bool
     */
    public function addUserHistoryItem($id, $nme, $price, $promo_price, $pic)
    {
        if (empty($id)) return;
        if (empty($nme)) return;
        if (empty($price)) return;
        $this->cms->checkTable('userhistory');
        $userLogin = Session::getUserData('username');
        $history_count = count($this->getHistoryInfo());

        $view = $this->isExistUserHistoryItem($userLogin, $id);

        if ($view == 1) {
            $this->updateUserHistoryItem($userLogin, $id, $promo_price);
            return true;
        }
        if ($history_count > 9) {
            $this->deleteLastUserItemVisit($userLogin);
        }
        $this->saveUserHistoryItem($userLogin, $id, $nme, $price, $promo_price, $pic);
        return true;
    }

    public function deleteLastUserItemVisit($userLogin){
        $this->cms->query('DELETE FROM `userhistory` WHERE `user`="'.$this->cms->escape($userLogin).'" ORDER BY `tme` ASC LIMIT 1');
    }
}
