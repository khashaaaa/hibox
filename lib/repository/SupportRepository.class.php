<?php

class SupportRepository extends Repository
{
    public function createTicket($uid, $orderid, $catid, $subject, $text, $admin = false, $userLogin = false)
    {
        $this->cms->checkTable('site_support');
        $direction = $admin ? 'Out' : 'In';
        $login = $userLogin ? $userLogin : '';
        $sql = "
            INSERT INTO `site_support`
            SET
                `subject` = '" . $this->cms->escape($subject) . "',
                `orderid` = '" . $this->cms->escape($orderid) . "',
                `categoryid` = '" . $this->cms->escape($catid) . "',
                `message` = '" . $this->cms->escape($text) . "',
                `parent` = 0,
                `userid` = '" . $this->cms->escape($uid) . "',
                `userlogin` = '" . $this->cms->escape($login) . "',
                `direction` = '" . $direction . "',
                `read` = 0,
                `added` = '" . time() . "'
        ";
        SupportRepositoryNew::clearSupportCache();
        return $this->cms->query($sql) ? $this->cms->insertedId() : false;
    }

    public function getTicketInfoList($uid, $arFilter = array())
    {
        $this->cms->checkTable('site_support');
        $where = array();
        if (array_key_exists('ticket_pub_order_number', $arFilter) && !empty($arFilter['ticket_pub_order_number']))
            $where[] = "`orderid` like '%" . $arFilter['ticket_pub_order_number'] . "%'";
        if (array_key_exists('ticket_pub_date_from', $arFilter) && !empty($arFilter['ticket_pub_date_from']))
            $where[] = "`added`>" . strtotime($arFilter['ticket_pub_date_from']);
        if (array_key_exists('ticket_pub_date_to', $arFilter) && !empty($arFilter['ticket_pub_date_to']))
            $where[] = "`added`<" . strtotime($arFilter['ticket_pub_date_to']);
        if (array_key_exists('ticket_pub_new', $arFilter) && !empty($arFilter['ticket_pub_new'])) {
            $where[] = "`direction`='Out' AND `read`='0' AND `parent` in (SELECT id FROM `site_support` WHERE `userid`='$uid')";
            if (!empty($where))
                $whereUser = implode(' AND ', $where);
            else
                $whereUser = '';

            $userTicketsQ = "
            SELECT * FROM `site_support`
            WHERE id in (SELECT distinct parent FROM `site_support` WHERE $whereUser)
            ORDER BY `added` DESC
            ";
        } else {
            $where[] = "`userid`='$uid' AND `parent`='0'";
            if (!empty($where))
                $whereUser = implode(' AND ', $where);
            else
                $whereUser = '';

            $userTicketsQ = "
            SELECT * FROM `site_support`
            WHERE $whereUser
            ORDER BY `added` DESC
            ";
        }

        $result = $this->cms->query($userTicketsQ);
        $tickets = array();
        if (!$result)
            return $tickets;

        while ($t = mysqli_fetch_assoc($result)) {
            /*if (($t['direction']=='Out') and ($t['read']=='0')) {
                $count_unread = $this->getTicketMessagesCount($t['id'], 'Out', 0)+1;
            } else {*/
                $count_unread = $this->getTicketMessagesCount($t['id'], 'Out', 0);
            //}
            $tickets[] = array(
            	'id' => $t['id'],
                'ticketid' => $t['id'],
                'status' => $t['direction'],
                'createddate' => date('Y-m-d H:i', $t['added']),
                'msgcount' => $this->getTicketMessagesCount($t['id']),
                'OrderId' => $t['orderid'],
                'CategoryId' => $t['categoryid'],
                'Subject' => $t['subject'],
                'newmsgcount' => $count_unread
            );
        }

        return $tickets;
    }

    public function getTicketMessagesCount($ticketId = false, $direction = false, $read = false, $uid = false)
    {
        $this->cms->checkTable('site_support');
        $where = array();
        $whereString = '';
        if ($read !== false) $where[] = "`read`= '$read'";
        if ($direction !== false) $where[] = "`direction` = '$direction'";
                    
        if ($uid === false) {
            if ($ticketId !== false) $where[] = "(`parent` = '$ticketId' OR `id` = '$ticketId')";
        } else {
            $where[] = "(`userid`='$uid' OR `parent` in (select `id` from `site_support` where parent='0' && `userid`='$uid'))";
        }
        if (!empty($where))
            $whereString = 'WHERE ' . implode(' AND ', $where);
        $sql = "
                SELECT COUNT(*)
                FROM `site_support`
                $whereString
                ";

        $result = $this->cms->querySingleValue($sql);
        return $result !== false ? $result : -1; 
    }

    public function getTicketDetails($uid, $ticketId)
    {
        $this->cms->checkTable('site_support');
        $ticketQ = "
            SELECT * FROM `site_support`
            WHERE `id`=".$ticketId." AND `userid`=".$uid."
            ORDER BY `added` DESC
            ";
        $result = $this->cms->query($ticketQ);

        if (!$result || !mysqli_num_rows($result))
            return false;

        $ticket = mysqli_fetch_array($result);
        return array(
            'Subject' => $ticket['subject'],
            'TicketId' => $ticket['id'],
            'Status' => $ticket['direction'],
            'OrderId' => $ticket['orderid'],
            'CreatedDate' => date('Y-m-d H:i', $ticket['added'])
        );
    }


    public function getTicketMessageList($uid, $ticketId, $orderDesc = false)
    {
        $this->cms->checkTable('site_support');
        $userId = $this->_getUserIdByTicketId($ticketId);
        if ($uid != $userId) return false;

        $ticketsQ = "
            SELECT * FROM `site_support`
            WHERE `id`=".$ticketId." OR `parent`=".$ticketId."
            ORDER BY `added` ".($orderDesc ? 'DESC' : 'ASC')."
            ";
        $ticketsR = $this->cms->query($ticketsQ);
        
        if (!$ticketsR) return false;
        $messages = array();
        while ($t = mysqli_fetch_assoc($ticketsR)) {
            $messages[] = array(
                'CreatedDate' => date('Y-m-d H:i', $t['added']),
                'Direction' => $t['direction'],
                'Text' => $t['message'],
                'userid' => $t['userid'],
                'read' => $t['read'],
            );
        }

        return $messages;
    }

    public function deleteTicketsByUID($uid)
    {
        $this->cms->checkTable('site_support');
        $request = "DELETE FROM `site_support` WHERE `userid` = ".$uid."";
        $this->cms->query($request);
    }

    public function markRead($tid, $dir, $setDir = false){
        $this->cms->checkTable('site_support');
        if ($setDir)
            $setDir = ',`direction`="'.$setDir.'"';
        $this->cms->query('
            UPDATE `site_support`
            SET `read`=1 '.$setDir.'
            WHERE
                `direction`="'.$dir.'"
                AND (`parent`="'.(int)$tid.'"
                OR `id`="'.(int)$tid.'")
            ');
        SupportRepositoryNew::clearSupportCache();
    }

    public function createTicketMessage($uid, $ticketId, $message, $isAdmin)
    {
        $this->cms->checkTable('site_support');
        $dir = $isAdmin ? 'Out' : 'In';
        $userId = $this->_getUserIdByTicketId($ticketId);
        if ($uid != $userId && !$isAdmin)
            return array(false, Lang::get('not_current_user_chat'));

        $message = General::getCms()->escape($message);
        $time = time();

        $res = $this->cms->query("
            INSERT INTO `site_support`
            SET
                `userid` = '$uid',
                `message` = '$message',
                `direction` = '$dir',
                `orderid` = '',
                `subject` = '',
                `categoryid` = '',
                `parent` = '$ticketId',
                `added` = " . $time . ",
                `read` = '0'
            ");
        SupportRepositoryNew::clearSupportCache();
        return array($res, mysqli_error($this->cms->getLink()), $time);
    }



    public function getSearchTicketsQ($userid, $from, $perpage,$arFilter){
        $this->cms->checkTable('site_support');
        $where = array();

        if ($userid) {
            $where[] = "`s`.`userid`='$userid'";
        }
        if (array_key_exists('ticket_id',$arFilter) && !empty($arFilter['ticket_id'])) {
            $where[] = "`s`.`id`=" . $arFilter['ticket_id'];
        }
        if (array_key_exists('ticket_order_number',$arFilter) && !empty($arFilter['ticket_order_number'])) {
            $where[] = "`s`.`orderid` like '%".$arFilter['ticket_order_number']."%'";
        }
        if (array_key_exists('ticket_date_from',$arFilter) && !empty($arFilter['ticket_date_from'])) {
            $where[] = "`s`.`added` >= '".strtotime($arFilter['ticket_date_from'])."'";
        }
        if (array_key_exists('ticket_date_to',$arFilter) && !empty($arFilter['ticket_date_to'])) {
            $timestamp = strtotime($arFilter['ticket_date_to'])+86399;
            $where[] = "`s`.`added` <= '".$timestamp."'";
        }
        if (array_key_exists('ticket_new',$arFilter)&&!empty($arFilter['ticket_new']))
            $where[] = "(`s`.`direction`='Answer' OR `s`.read=0)";

        if (! empty($where)) {
            $whereUser = implode(' AND ',$where).' AND ';
        } else {
            $whereUser='';
        }

        $userTicketsQ = "
            SELECT * FROM `site_support` as s
            WHERE $whereUser `parent`=0
            ORDER BY `added` DESC";
        $result = $this->cms->query($userTicketsQ);
        return $result;
    }


    public function getTicketsCountQ($userid, $from, $perpage){
        $this->cms->checkTable('site_support');
        $whereUser = $userid ? "`userid`='$userid' AND " : '';

        $userTicketsQ = "
            SELECT COUNT(*) FROM `site_support`
            WHERE $whereUser `parent`=0
            ORDER BY `added` DESC
            ";
        $cms = new CMS();
        $cms->Check();
        $result = $this->cms->querySingleValue($userTicketsQ);
        return $result !== false ? $result : 0;
    }

    public function getOrderNumbers($uid=false){
        $this->cms->checkTable('site_support');
        $whereUID = '';
        if ($uid)
            $whereUID = ' AND `userid`='.$uid;
        $userTicketsQ = "
            SELECT distinct `orderid` FROM `site_support`
            WHERE  `parent`=0 $whereUID
            ";

        $result = $this->cms->query($userTicketsQ);
        $arOrderID=array(LangAdmin::get('all'));
        while($t = mysqli_fetch_assoc($result)){
            $t['orderid'] = trim($t['orderid']);
            if (!empty($t['orderid']))
                $arOrderID[] = $t['orderid'];
        }
        return $arOrderID;
    }

    public function getUsers() {
        $this->cms->checkTable('site_support');
        $userTicketsQ = "
            SELECT distinct `userid` FROM `site_support`
            WHERE  `parent`='0'
            ";
        return $this->cms->query($userTicketsQ);
    }

    private function _getUserIdByTicketId($ticketId)
    {
        $userQ = "SELECT `userid` FROM `site_support` WHERE `id`=".$ticketId;
        return $this->cms->querySingleValue($userQ);
    }

    public function closeticket($uid,$ticketId){
        $this->cms->checkTable('site_support');
        if ($uid != '') {
            $userSQL = " AND `userid` = " . $uid;
        } else {
            $userSQL = "";
        }
        $ticketQ = "
            UPDATE `site_support` SET `direction` = 'close'
            WHERE `id`=".$ticketId." " . $userSQL;
        $result = $this->cms->query($ticketQ);
        SupportRepositoryNew::clearSupportCache();
    }
}
