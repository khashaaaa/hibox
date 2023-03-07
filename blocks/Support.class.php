<?php

class Support extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'newmessage'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/users/';

	private   $SupportRepository;
	
	
	
    public function __construct()
    {
        parent::__construct(true);	
		$this->SupportRepository = new SupportRepositoryNew(new CMS());
    }

    
    private function save($fields){
        global $otapilib;
        
        $error = $this->validateFields($fields);
        if($error) return $error;
        
        $sid = Session::getUserSession();
        $user = $otapilib->GetUserInfo($sid);
        if (($user === false) or (int)$user['Id']==0){
            General::sessionExpiredHandle(false);
            return array(false, $otapilib->error_message, '');
        }
        
		try {
        	$reg = $this->SupportRepository->createTicket((int)$user['Id'], $fields['SalesId'], $fields['CategoryId'], $fields['Subject'], $fields['Text'],false, $user['login']);
            
		} catch (DBException $e) {
           	Session::setError($e->getMessage(), 'DBError');                
        }
        if(!$reg){
           return array(false, Lang::get('ticket_add_error'), '');
        }
        
        $this->SupportRepository->setLoginTicket($reg, $user['Login']);

        $data['userid']=(int)$user['id'];
        $data['id']=0;
        $data['login']=$user['login'];
        Notifier::generalNotification('new_ticket',Lang::get('new_ticket'),$data);

        return array(true, Lang::get('data_updated_successfully'), (string)$reg);
    }
    
    private function newMessage(){
        global $otapilib;
        
        if(@$_POST){
            @list($success, $error, $res) = $this->save($_POST);
            if(!$success){
                $this->tpl->assign('error', $error);
            }
            else{
                header('Location: /?p=support&mode=view');
                return true;
            }
        }
        $this->_template = 'newmessage';
        $orders = $otapilib->GetSalesOrdersList(Session::getUserSession(), 0);
        $this->tpl->assign('orders', $orders);
        
        $categories = $otapilib->GetTicketCatogories();
        $this->tpl->assign('cats', $categories);
    }
    
    private function viewMessages(){
        global $otapilib;        
        $this->_template = 'viewmessages'; 
		$ticketlist = array();
		$orderList = array();
			
		$arFilter = $this->SetFilter();
		
        $sid = Session::getUserSession();
        $user = $otapilib->GetUserInfo($sid);
		try {
            $ticketlist = $this->SupportRepository->getTicketInfoList((int)$user['Id'], $arFilter);
			$orderList = $this->SupportRepository->getOrderNumbers($user['Id']);
		} catch (DBException $e) {
           	Session::setError($e->getMessage(), 'DBError');                
        }
		$categories = $otapilib->GetTicketCatogories();
		
		$catNames = array();
		foreach ($categories as $category){
			$catNames[$category['CategoryId']] = $category['Name'];
		}
		$this->tpl->assign('catNames', $catNames);

		$this->tpl->assign('arOrderID', $orderList);
		$this->tpl->assign('ticketlist', $ticketlist);
	}
    
    private function chat(){
        global $otapilib;
        $this->checkChatAdding();
        $this->_template = 'chat';
		$chat = false;
        
		$id = str_replace('Ticket-', '', $_GET['id']);
        $sid = Session::getUserSession();
        $user = $otapilib->GetUserInfo($sid);
		try {
        	$chat['TicketInfo'] = $this->SupportRepository->getTicketDetails((int)$user['Id'], $id);
        	$chat['TicketMessageList'] = $this->SupportRepository->getTicketMessageList((int)$user['Id'], $id);
			$this->SupportRepository->markRead($id, 'Out');
		} catch (DBException $e) {
           	Session::setError($e->getMessage(), 'DBError');                
        }
		if (empty ($user['LastName']))
			$name = $user['RecipientLastName'].' '.$user['RecipientFirstName'];
		else
			$name = $user['LastName'].' '.$user['FirstName'];
		
		      
        
        if(@$_SESSION['chat_success']){
            $this->tpl->assign('success', $_SESSION['chat_success']);
            Session::clear('chat_success');
        }
		$this->tpl->assign('name', $name);
		$this->tpl->assign('chat', $chat);
    }
    
    private function checkChatAdding(){
        if(!empty($_POST)){
            list($success, $error) = $this->addToChat($_POST);
            if(!$success){
                $this->tpl->assign('error', $error);
            }
            else{
                Session::set('chat_success', Lang::get('message_was_added'));
                header('Location: '.$_SERVER['REQUEST_URI']);
                die();
            }
			return '';
        }
		return 'Answer';
    }
    
    private function addToChat($fields){
        global $otapilib;

        if(!$fields['Text']) return array(false, Lang::get('was_not_entered_text_answer'), '');

        $sid = Session::getUserSession();
        $user = $otapilib->GetUserInfo($sid);
        $userid = (int)$user['Id'];

        $id = str_replace('Ticket-', '', $_GET['id']);
        try {
        	$add = $this->SupportRepository->createTicketMessage($userid, $id, $fields['Text'], false);
            $this->SupportRepository->setLoginTicket($id, $user['Login']);
		} catch (DBException $e) {
           	Session::setError($e->getMessage(), 'DBError');                
        }

        $data['userid']=(int)$user['id'];
        $data['id']=$id;
        $data['login']=$user['login'];
        Notifier::generalNotification('new_ticket',Lang::get('new_ticket'),$data);
		
        if($add){			
			return array(true, Lang::get('data_updated_successfully'));
		}
        else
            return array(false, $add[1], '');
    }
    
    protected function setVars()
    {
		if (!Session::getUserData()) {
            RequestWrapper::LocationRedirect(UrlGenerator::generateContentUrl('login'));
        }
        switch (@$_GET['mode']){
            case 'new':
                General::sessionExpiredHandle(false);
                $orderId = @$_POST['SalesId'] ? $_POST['SalesId'] : @$_GET['SalesId'];
                
                $subject = '';
                if(@$_POST['Subject']){
                    $subject = @$_POST['Subject'];
                }
                elseif(@$_GET['type'] == 'moneyOut'){
                    $subject = Lang::get('cashout');
                }
                
                $categoryId = '';
                if(@$_POST['CategoryId']){
                    $categoryId = @$_POST['CategoryId'];
                }
                elseif(@$_GET['type'] == 'moneyOut'){
                    $categoryId = 'Common';
                }
                
                $this->tpl->assign('categoryId', $categoryId);
                $this->tpl->assign('subject', $subject);
                $this->tpl->assign('orderId', $orderId);
                $this->newMessage();
                break;
            case '':
            case 'view':
                global $otapilib;
                $otapilib->GetCountryInfoList();
                General::sessionExpiredHandle(false);
                $this->viewMessages();
                break;
            case 'chat':
                if (Session::getUserData()) {
                    General::sessionExpiredHandle(false);
                    $this->chat();
                }
                break;
			case 'close':
                $this->CloseTicket();
                break;
        }
    }


	private function SetFilter(){
		$arFilter = array();

		if (isset($_POST['clearFilter'])) {
		    $arSubFilter = Session::get('arSubFilter', []);
            $arSubFilter['ticket_pub_order_number'] = 0;
            $arSubFilter['ticket_pub_date_from'] = '';
            $arSubFilter['ticket_pub_date_to'] = '';
            $arSubFilter['ticket_pub_new'] = '';
            Session::set('arSubFilter', $arSubFilter);
		} elseif (isset($_POST['filter'])) {
            $arSubFilter = Session::get('arSubFilter', []);

			if (!empty($_POST['ticket_pub_order_number'])){
				$arFilter['ticket_pub_order_number'] = $_POST['ticket_pub_order_number'];
                $arSubFilter['ticket_pub_order_number'] = $_POST['ticket_pub_order_number'];
			} else {
                $arSubFilter['ticket_pub_order_number']=0;
            }

			if (!empty($_POST['ticket_pub_date_from'])){
				$arFilter['ticket_pub_date_from'] = $_POST['ticket_pub_date_from'];
                $arSubFilter['ticket_pub_date_from'] = $_POST['ticket_pub_date_from'];
			} else {
                $arSubFilter['ticket_pub_date_from']='';
            }

			if (!empty($_POST['ticket_pub_date_to'])){
				$arFilter['ticket_pub_date_to'] = $_POST['ticket_pub_date_to'];
                $arSubFilter['ticket_pub_date_to'] = $_POST['ticket_pub_date_to'];
			} else {
                $arSubFilter['ticket_pub_date_to']='';
            }

			if (isset($_POST['ticket_pub_new'])){
				$arFilter['ticket_pub_new'] = $_POST['ticket_pub_new'];
                $arSubFilter['ticket_pub_new'] = $_POST['ticket_pub_new'];
			} else {
                $arSubFilter['ticket_pub_new']='';
            }

            Session::set('arSubFilter', $arSubFilter);
		} else {
            if (isset($_SESSION['arSubFilter']['ticket_pub_order_number'])) {
                $arFilter['ticket_pub_order_number'] = $_SESSION['arSubFilter']['ticket_pub_order_number'];
            }

			if (isset($_SESSION['arSubFilter']['ticket_pub_date_from'])) {
                $arFilter['ticket_pub_date_from'] = $_SESSION['arSubFilter']['ticket_pub_date_from'];
            }
			if (isset($_SESSION['arSubFilter']['ticket_pub_date_to'])) {
                $arFilter['ticket_pub_date_to'] = $_SESSION['arSubFilter']['ticket_pub_date_to'];
            }

			if (isset($_SESSION['arSubFilter']['ticket_pub_new'])) {
                $arFilter['ticket_pub_new'] = $_SESSION['arSubFilter']['ticket_pub_new'];
            }
		}

		if (!isset($_SESSION['arSubFilter']['ticket_pub_order_number'])) {
            $arSubFilter = Session::get('arSubFilter', []);
            $arSubFilter['ticket_pub_order_number'] = 0;
            Session::set('arSubFilter', $arSubFilter);
		}

		return $arFilter;
	}
	
	private function xmlParams($fields){
        $xml = new SimpleXMLElement('<UserUpdateData></UserUpdateData>');
        $xml->addChild('Email', htmlspecialchars($fields['Email']));
        $xml->addChild('FirstName', htmlspecialchars($fields['FirstName']));
        $xml->addChild('LastName', htmlspecialchars($fields['LastName']));
        $xml->addChild('MiddleName', htmlspecialchars($fields['MiddleName']));
        $xml->addChild('Sex', htmlspecialchars($fields['Sex']));
        
        return $xml->asXML();
    }
	
	private function validateFields($fields){
        if(!$fields['Subject'])
            return array(false, Lang::get('put_message_subject'));
        if(!$fields['Text'])
            return array(false, Lang::get('not_entered_message_text'));
        if(!$fields['CategoryId'])
            return array(false, Lang::get('no_category_selected'));
        
        return false;
    }
}

?>