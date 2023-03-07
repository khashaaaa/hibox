<?php

class SupportRepositoryNew extends SupportRepository
{
    public function searchTicketsNew($from, $perpage, $arFilter, $isOrders)
    {
        $this->cms->checkTable('site_support');
        $whereSQL = $this->getFilter($arFilter, $isOrders);
        $userTicketsQ = "
            SELECT DISTINCT `s1`.* FROM `site_support` as `s1`
            $whereSQL `s1`.`parent`=0
            ORDER BY `added` DESC LIMIT $from, $perpage";


        $result = $this->cms->queryMakeArray($userTicketsQ);
        return $result;
    }

    public function getOrderTicketsNew($from, $perpage, $arFilter, $isOrders)
    {
        $this->cms->checkTable('site_support');
        $whereSQL = $this->getFilter($arFilter, $isOrders);
        $userTicketsQ = "
            SELECT DISTINCT `s1`.`orderid`, `s1`.`added` FROM `site_support` as `s1`
            $whereSQL `s1`.`parent` = 0
            ORDER BY `s1`.`added` DESC LIMIT $from, $perpage";
        $isOrders = $this->cms->queryMakeArray($userTicketsQ);
        if (count($isOrders) == 0) {
            return false;
        }
        $whereSQL = $this->getFilter($arFilter, $isOrders);
        $userTicketsQ = "
            SELECT DISTINCT `s1`.* FROM `site_support` as `s1`
            $whereSQL `s1`.`parent`= 0
            ORDER BY `added` DESC";
        $result = $this->cms->queryMakeArray($userTicketsQ);
        return $result;
    }

    public function getTicketsCount($arFilter, $isOrders)
    {
        $this->cms->checkTable('site_support');
        if ($isOrders) {
            $whereCountSQL = "DISTINCT `s1`.`orderid`";
        } else {
            $whereCountSQL = "`s1`.`id`";
        }
        $whereSQL = $this->getFilter($arFilter, $isOrders);
        $userTicketsQ = "
            SELECT $whereCountSQL FROM `site_support` as `s1`
            $whereSQL `s1`.`parent`=0";

        $result = $this->cms->queryMakeArray($userTicketsQ);
        return count($result);

    }

    public static function getTotalCountNotAnswered($isOrders)
    {
        $cms = new CMS();
        $cms->checkTable('site_support');
        if ($isOrders) {
            $whereSQL = "`s1`.`orderid` <> '' ";
        } else {
            $whereSQL = "`s1`.`orderid` = '' ";
        }
        $userTicketsQ = "
            SELECT COUNT(*) FROM `site_support` as `s1`
            LEFT OUTER JOIN `site_support` `s2` ON `s1`.`id` = `s2`.`parent`
            WHERE
                (((`s2`.`direction`='Answer' AND `s2`.`read`= 1) OR (`s1`.`direction`='Answer' AND `s1`.`read`= 1)) OR
                ((`s2`.`direction`='In' AND `s2`.`read`= 0) OR (`s1`.`direction`='In' AND `s1`.`read`= 0))) AND
                $whereSQL AND (`s1`.`direction` <> 'close') AND 
                `s1`.`parent` =0
            ";
        $result = $cms->querySingleValue($userTicketsQ);
        return $result !== false ? $result : 0;    }

    public static function getTotalCountNew($isOrders)
    {
        $cms = new CMS();
        $cms->checkTable('site_support');
        if ($isOrders) {
            $whereSQL = "`s1`.`orderid` <> '' ";
        } else {
            $whereSQL = "`s1`.`orderid` = '' ";
        }
        $userTicketsQ = "
            SELECT COUNT(*) FROM `site_support` as `s1`
            LEFT OUTER JOIN `site_support` `s2` ON `s1`.`id` = `s2`.`parent`
            WHERE
                ((`s2`.`direction`='In' AND `s2`.`read`= 0) OR (`s1`.`direction`='In' AND `s1`.`read`= 0)) AND
                $whereSQL AND
                `s1`.`parent` =0
            ";
        $result = $cms->querySingleValue($userTicketsQ);
        return $result !== false ? $result : 0;
    }

    public function setLoginTicket($id, $login)
    {
        $this->cms->checkTable('site_support');
        $this->cms->query("
            UPDATE `site_support`
            SET `userlogin` = '" . $this->cms->escape($login) . "'
            WHERE `id` = $id
        ");

    }

    private function getFilter($arFilter, $isOrder = false)
    {
        $where = '';
        $join = false;
        if (! empty($arFilter['ticket_id'])) {
            $where .= "`s1`.`id`= '" . $this->cms->escape($arFilter['ticket_id']) . "' AND ";
        }
        if (! empty($arFilter['ticket_user'])) {
            $where .= "`s1`.`userlogin` like '%" . $this->cms->escape($arFilter['ticket_user']) . "%' AND ";
        }
        if ($isOrder) {
            if (! empty($arFilter['ticket_order_number'])) {
                $where .= "`s1`.`orderid` like '%" . $this->cms->escape($arFilter['ticket_order_number']) . "%' AND ";
            } else {
                if (is_array($isOrder)) {
                    $where .= "(";
                    foreach ($isOrder as $val) {
                        $where .= "(`s1`.`orderid` = '{$val['orderid']}') OR";
                    }
                    $where = substr($where, 0, -2);
                    $where .= ") AND ";
                } else {
                    $where .= "`s1`.`orderid` <> '' AND ";
                }
            }
        } else {
            $where .= "`s1`.`orderid` = '' AND ";
        }
        if (! empty($arFilter['ticket_date_from'])) {
            if (! $join) {
                $join = " LEFT OUTER JOIN `site_support` `s2` ON `s1`.`id` = `s2`.`parent` ";
            }
            $where .= "(`s2`.`added` >= '" . strtotime($arFilter['ticket_date_from']) . "' OR `s1`.`added` >= '" . strtotime($arFilter['ticket_date_from']) . "') AND ";
        }
        if (! empty($arFilter['ticket_date_to'])) {
            if (! $join) {
                $join = " LEFT OUTER JOIN `site_support` `s2` ON `s1`.`id` = `s2`.`parent` ";
            }
            $timestamp = strtotime($arFilter['ticket_date_to']) + 86399;
            $where .= "(`s2`.`added` <= '" . $timestamp . "' OR `s1`.`added` <= '" . $timestamp . "') AND ";

        }
        if (! empty($arFilter['ticket_new'])) {
            if (! $join) {
                $join = "LEFT OUTER JOIN `site_support` `s2` ON `s1`.`id` = `s2`.`parent` ";
            }
            $where .= "((`s2`.`direction` = 'In' AND `s2`.`read`= 0) OR (`s1`.`direction` = 'In' AND `s1`.`read`= 0) ";
            if (empty($arFilter['ticket_notanswer'])) {
                $where .= ") AND ";
            } else {
                $where .= " OR ";
            }
        }
        if (! empty($arFilter['ticket_notanswer'])) {
            if (! $join) {
                $join = "LEFT OUTER JOIN `site_support` `s2` ON `s1`.`id` = `s2`.`parent` ";
            }
            if (empty($arFilter['ticket_new'])) {
                $where .= "(";
            }
            $where .= " (`s1`.`direction`='Answer') OR (`s2`.`direction`='Answer')";
            $where .= " OR (`s1`.`direction`='In') OR (`s2`.`direction`='In')";
            $where .= " ) AND (`s1`.`direction` <> 'close') AND ";
        }

        if ($join) {
           $return = $join . ' WHERE ' . $where;
        } else {
           $return = ' WHERE ' . $where;
        }
        return $return;
    }

    public static function clearSupportCache()
    {        
        $fileMysqlMemoryCache = new FileAndMysqlMemoryCache(new CMS());
        $cacheKey = array(
            0 => md5('new_isOrder'),
            1 => md5('new_none'),
            2 => md5('none_isOrder'),
            3 => md5('none_none')
        );
        foreach ($cacheKey as $key) {
            $fileMysqlMemoryCache->DelCacheEl($key);
        }      
    }

}
