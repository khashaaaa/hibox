<?php

/**
 * Support system of site.
 * Functionality:
 * - view tickets
 * - write answers to tickets
 */
class Support extends GeneralUtil {
	
    public function defaultAction()
    {
        $this->checkAuth();        

        $perpage = 20;
        $from = (isset($_GET['from'])) ? $_GET['from'] : 0;
		
		$perpage_t2 = 20;
        $from_t2 = (isset($_GET['from_t2'])) ? $_GET['from_t2'] : 0;
		
        $tickets = $this->showTickets(0, $from, $perpage);

        chdir(ADMIN_ABSOLUTE_PATH);
        include TPL_DIR.'support.php';
    }

    private function showTickets($userid = 0, $from = 0, $perpage = 20)
    {
		try {
        	global $otapilib;
			$SupportRepository = new SupportRepositoryNew(new CMS());
        	$categories = $otapilib->GetTicketCatogories();
        	$catNames = array();
        	foreach ($categories as $category) {
        	    $catNames[$category['CategoryId']] = $category['Name'];
        	}
        	$arFilter=$this->SetFilter();

        	$result = $SupportRepository->getSearchTicketsQ(@$_SESSION['arSubFilter']['ticket_user_id'], $from, $perpage,$arFilter);
        	$sid = $_SESSION['sid'];        	
        	$orderList = $SupportRepository->getOrderNumbers(@$_SESSION['arSubFilter']['ticket_user_id']);

            $readTickets = array();
            $unreadTickets = array();

        	if(!$result) return array(
        	    'totalcount' => 0,
        	    'filterOrderList' => $orderList,
        	    'content'    => array()
        	);
        	while($t = mysql_fetch_assoc($result)) {
				
                $unreadCount = $SupportRepository->getTicketMessagesCount($t['id'], 'In', 0);
            	if(!$t['read']) $unreadCount++;
            	$orderid = $t['orderid'] ?  $t['orderid'] : '';
            	$ticket = array(
            	    'id' => $t['id'],
            	    'order_id' => $t['orderid'],
            	    'category' => @$catNames[$t['categoryid']],
            	    'subject' => $t['subject'],
            	    'orderid' => $orderid,
            	    'ticketid' => 'Ticket-'.$t['id'],
            	    'createddate' => date('Y-m-d H:i', $t['added']),
            	    'msgcount' => $SupportRepository->getTicketMessagesCount($t['id'])+1,
					'notAnswered' => $t['direction']=='Answer'||($t['direction']=='In'&&$unreadCount>0),
            	    'newmsgcount' => $unreadCount,
                    'user' => $t['userid'],
                    'userLogin' => isset($t['userlogin']) ? $t['userlogin'] : ''
            	);
                if($unreadCount) {
                	$unreadTickets[] = $ticket;
                } else {
                    $readTickets[] = $ticket;
                }				
            }
        	return array(
            	'totalcount' => $SupportRepository->getTicketsCountQ($userid, $from, $perpage),
            	'filterOrderList' => $orderList,
            	'content'    => array_merge($unreadTickets, $readTickets)
        	);
		} catch (DBException $e) {
           	Session::setError($e->getMessage(), 'DBError');                
        } catch(ServiceException $e){
    		Session::setError($e->getMessage(), $e->getErrorCode());
		}

    }

    
    private function getUsers($sid)
    {
		try {
        	global $otapilib;
		
			$SupportRepository = new SupportRepository(new CMS());
		
       
        	if(defined('CFG_MULTI_CURL') && CFG_MULTI_CURL) {
        	    $result = $SupportRepository->getUsers();
        	    $otapilib->InitMulti();
        	    while($t = mysql_fetch_assoc($result)) {
        	        $otapilib->GetUserInfoForOperator($sid, $t['userid']);
        	    }
        	    $otapilib->MultiDo();
        	}

        	$result = $SupportRepository->getUsers();
        	$arResult=array(array('Id'=>0,'Login'=>LangAdmin::get('all')));
        	while($t = mysql_fetch_assoc($result)){
        	    $arResult[$t['userid']] = $otapilib->GetUserInfoForOperator($sid, $t['userid']);
        	}

        	if(defined('CFG_MULTI_CURL') && CFG_MULTI_CURL){
        	    $otapilib->StopMulti();
        	}

        	return $arResult;
		} catch (DBException $e) {
           	Session::setError($e->getMessage(), 'DBError');                
        } catch(ServiceException $e){
    		Session::setError($e->getMessage(), $e->getErrorCode());
		}

    }
	
    private function SetFilter()
    {
        $arFilter = array();
        if (isset($_POST['clearFilter'])) {
            $_SESSION['arSubFilter']['ticket_order_number'] = 0;
            $_SESSION['arSubFilter']['ticket_date_from'] = '';
            $_SESSION['arSubFilter']['ticket_id'] = '';
            $_SESSION['arSubFilter']['ticket_date_to'] = '';
            $_SESSION['arSubFilter']['ticket_user_id'] = false;
            $_SESSION['arSubFilter']['ticket_new'] = '';
        } elseif (isset($_POST['filter'])) {
            $keysForFilter = array(
                'ticket_order_number' => 0,
                'ticket_id' => '',
                'ticket_date_from' => '',
                'ticket_date_to' => '',
                'ticket_user_id' => false,
                'ticket_new' => '',
            );

            foreach ($keysForFilter as $key => $value) {
                if (! empty($_POST[$key])) {
                    $arFilter[$key] = $_POST[$key];
                    $_SESSION['arSubFilter'][$key] = $_POST[$key];
                } else {
                    $_SESSION['arSubFilter'][$key] = $value;
                }
            }
        } else {
            if (isset($_SESSION['arSubFilter'])) {
                foreach ($_SESSION['arSubFilter'] as $key=>$value) {
                    $arFilter[$key] = $value;
                }
            }
        }
        if (!isset($_SESSION['arSubFilter']['ticket_order_number'])) {
            $_SESSION['arSubFilter']['ticket_order_number'] = 0;
        }
        if (!isset($_SESSION['arSubFilter']['ticket_new']) && empty ($arFilter)) {
            $arFilter['ticket_new'] = 1;
        }

        return $arFilter;
    }
}

?>
