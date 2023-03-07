<?php

OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');

class Support extends GeneralUtil
{
    protected $_template = 'orders';
    protected $_template_path = 'support/';

    /**
     * @var supportRepositoryNew
     */
    protected $supportRepository;

    /**
     * @var supportProvider
     */
    protected $supportProvider;

    /**
     * @var usersProvider
     */
    protected $usersProvider;



    public function __construct()
    {
        parent::__construct();
        $this->supportRepository = new SupportRepositoryNew($this->cms);
        $this->supportProvider = new SupportProvider($this->getOtapilib());
        $this->usersProvider = new UsersProvider($this->getOtapilib());
    }

    public function defaultAction($request)
    {
        $this->setTicketSearch($request, true);
        print $this->fetchTemplate();
    }

    public function otherAction($request)
    {
        $this->_template = 'other';
        $this->setTicketSearch($request, false);
        print $this->fetchTemplate();
    }

    public function addTicketMessageAction($request)
    {
        $sid = Session::get('sid');
        $orderId        = $request->getValue('orderId');
        $customerId     = $request->getValue('customerId');
        $customerLogin  = $request->getValue('customerLogin', '');
        $ticketId       = $request->getValue('ticketId');
        $comment        = $request->getValue('comment');
        $isNewTicket    = $request->getValue('isNewTicket');
        if (empty($comment)) {
            $this->respondAjaxError(LangAdmin::get('Value_must_not_be_empty'));
        }
        $response = array();
        try {
            if ($isNewTicket === 'true') {
                $result = $this->supportRepository->createTicket(
                    $customerId,
                    $orderId,
                    'Common',
                    LangAdmin::get('in_order'),
                    $comment,
                    true,
                    $customerLogin
                );
                $newTicketId = (int)$result;
            } else {
                $result = $this->supportRepository->createTicketMessage('-100', $ticketId, $comment, true);
                $this->supportRepository->markRead($ticketId, 'Answer', 'Answered');
            }
            if (! empty($result)) {
                if (RightsManager::hasRight(RightsManager::RIGHT_VIEWUSERS)) {
                    $userdata = $this->getOtapilib()->GetUserInfoForOperator($sid, $customerId);
                    $userdata['ticket_id'] = ! empty($newTicketId) ? $newTicketId : $ticketId;
                    $userdata['txt_message'] = $comment;
                    Notifier::notifyUserOnTicketAnswer($userdata);
                }

                $response['comment'] = array(
                    'username'  => LangAdmin::get('Operator'),
                    'time'      => date('d.m.Y, H:i'),
                    'text'   => TextHelper::parseTextWithUrl(TextHelper::escape($comment)),
                );
                if (! empty($newTicketId)) {
                    $response['newTicketId'] = $newTicketId;
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse($response);
    }

    public function markTicketMessagesReadAction($request)
    {
        $ticketId   = $request->getValue('ticketId');

        $response = array();
        try {
            $this->supportRepository->markRead($ticketId, 'In', 'Answer');
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse($response);
    }

    /**
     * @param RequestWrapper $request
     */
    public function getChatAction($request)
    {
        $chatData = array();
        try {
            $chatData['TicketInfo'] = $this->supportRepository->getTicketDetails($request->post('userId'), $request->post('ticketId'));
            $chatData['TicketInfo']['CustomOrderId'] = OrdersProxy::normalizeOrderId($chatData['TicketInfo']['OrderId']);
            $chatData['TicketMessageList'] = $this->supportRepository->getTicketMessageList($request->post('userId'), $request->post('ticketId'));
            $chatData['TicketMessageList'] = array_reverse($chatData['TicketMessageList']);
            foreach ($chatData['TicketMessageList'] as &$message) {
                $message['CreatedDate'] = date('d.m.Y H:i', strtotime($message['CreatedDate']));
                $message['Text'] = TextHelper::parseTextWithUrl($this->escape($message['Text']));
            }
            $this->supportRepository->markRead($request->post('ticketId'), 'In', 'Answer');
            $chatData['userData'] = $this->usersProvider->GetUserInfoForOperator(Session::get('sid'), $request->post('userId'));
            if (! empty($chatData['userData'])) {
                $this->supportRepository->setLoginTicket($request->post('ticketId'), $chatData['userData']['Login']);
            }

        } catch (ServiceException $e) {
            if ($e->getCode() != 'NotFound') {
                $this->respondAjaxError($e->getMessage());
            }
        } catch (DBException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse($chatData);
    }

    /**
     * @param RequestWrapper $request
     */
    public function sendMessageAction($request)
    {
        $id = str_replace('Ticket-', '', $request->post('ticketId'));
        $response = array();
        try {
            $validator = new Validator(array(
                'message' => $request->post('message')
            ));
            $validator->addRule(new NotEmpty(), 'message', LangAdmin::get('Message_can_not_be_empty'));
            if (! $validator->validate()) {
                $errors = $validator->getLastError();
                throw new Exception((string)$errors);
            }
            $result = $this->supportRepository->createTicketMessage(-100, $id, $request->post('message'), true);
            $this->supportRepository->markRead($id, 'Answer','Answered');

            $userData['email'] = trim($request->post('userMail'));
            $userData['ticket_id'] = $id;
            $userData['txt_message'] = $request->getRequestValueSafe('message');

            $response['message'] = TextHelper::parseTextWithUrl($request->getRequestValueSafe('message'));
            $response['time'] = $result[2] * 1000; // need return milliseconds for JS Date()
            Notifier::notifyUserOnTicketAnswer($userData);
        } catch (DBException $e) {
            $this->respondAjaxError($e->getMessage());
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse($response);
    }

    /**
     * @param RequestWrapper $request
     */
    public function closeTicketAction($request)
    {
        $id = str_replace('Ticket-', '', $request->post('ticketId'));
        try {
            $this->supportRepository->closeticket('', $id);
        } catch (DBException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    /**
     * @param RequestWrapper $request
     * @param $isOrders
     */
    private function setTicketSearch($request, $isOrders)
    {
        try {
            $arFilter = $this->setFilter($request);
            $categories = $this->supportProvider->GetTicketCatogories();
            $supportTickets = $this->getSearchTickets($request, $categories, $arFilter, $isOrders);
        } catch (ServiceException $e) {
            $arFilter = array();
            $supportTickets = array();
            Session::setError($e->getMessage(), $e->getErrorCode());
        } catch (DBException $e) {
            $arFilter = array();
            $supportTickets = array();
            Session::setError($e->getMessage(), 'DBError');
        }
        $page = $request->valueExists('page') ? $request->getValueSafe('page') : 1;
        $perPage = $request->valueExists('perpage') ? $request->getValueSafe('perpage') : 10;
        $this->tpl->assign('supportTickets', $supportTickets);
        $this->tpl->assign('filter', $arFilter);
        $this->tpl->assign('paginator', new Paginator($supportTickets['totalcount'], $page, $perPage));
    }

    /**
     * @param RequestWrapper $request
     * @param $categories
     * @param $arFilter
     * @param $isOrders
     * @return array
     */
    private function getSearchTickets($request, $categories, $arFilter, $isOrders)
    {
        $page = $request->valueExists('page') ? $request->getValueSafe('page') : 1;
        $perpage = $request->valueExists('perpage') ? $request->getValueSafe('perpage') : 10;
        $from = ($page - 1) * $perpage;

        $catNames = array();
        foreach ($categories as $category) {
            $catNames[$category['CategoryId']] = $category['Name'];
        }
        if ($isOrders) {
            $result = $this->supportRepository->getOrderTicketsNew($from, $perpage, $arFilter, $isOrders);
        } else {
            $result = $this->supportRepository->searchTicketsNew($from, $perpage, $arFilter, $isOrders);
        }

        $readTickets = array();
        $unreadTickets = array();

        if (! $result) return array(
            'totalcount' => 0,
            'totalCountNotAnswered' => SupportRepositoryNew::getTotalCountNotAnswered($isOrders),
            'totalCountNew' => SupportRepositoryNew::getTotalCountNew($isOrders),
            'orderList' => array(),
            'content'    => array()
        );

        foreach ($result as $t) {
            $unreadCount = $this->supportRepository->getTicketMessagesCount($t['id'], 'In', 0);
            $notAnswered = $this->supportRepository->getTicketMessagesCount($t['id'], 'Answer', 1);

            $ticket = array(
                'id' => $t['id'],
                'category' => isset($catNames[$t['categoryid']]) ? $catNames[$t['categoryid']] : '',
                'subject' => $t['subject'],
                'orderid' => $t['orderid'],
                'ticketid' =>  $t['id'],
                'createddate' => $t['added'],
                'msgcount' => $this->supportRepository->getTicketMessagesCount($t['id']),
                'notAnswered' => ($notAnswered > 0) || ($unreadCount > 0),
                'newmsgcount' => $unreadCount,
                'user' => $t['userid'],
                'userLogin' => isset($t['userlogin']) ? $t['userlogin'] : '',
                'status' => $t['direction']
            );
            if ($unreadCount) {
                $unreadTickets[] = $ticket;
            } else {
                $readTickets[] = $ticket;
            }
        }

        $tickets = array_merge($unreadTickets, $readTickets);

        $orderList = array();
        if ($isOrders) {
            $orderList = $this->getOrderList($tickets);
        }

        return array(
            'totalcount' => $this->supportRepository->getTicketsCount($arFilter, $isOrders),
            'totalCountNotAnswered' => SupportRepositoryNew::getTotalCountNotAnswered($isOrders),
            'totalCountNew' => SupportRepositoryNew::getTotalCountNew($isOrders),
            'orderList' => $orderList,
            'content'    => $tickets
        );
    }

    /**
     * @param RequestWrapper $request
     * @return array
     */
    private function setFilter($request)
    {
        $arFilter = array();
        $keysForFilter = array(
            'ticket_order_number' => '',
            'ticket_id' => '',
            'ticket_date_from' => '',
            'ticket_date_to' => '',
            'ticket_user' => '',
            'ticket_new' => '',
            'ticket_notanswer' => '',
        );

        foreach ($keysForFilter as $key => $value) {
            if ($request->valueExists($key)) {
                $arFilter[$key] = $request->get($key);
            }
        }
        return $arFilter;
    }

    private function getOrderList($tickets)
    {
        $orderList = array();
        $orderListTmp = array();
        foreach ($tickets as $ticket) {
            $orderListTmp[] = $ticket['orderid'];
        }
        $orderListTmp = array_unique($orderListTmp);
        foreach ($orderListTmp as $orderNumber) {
            $temp['id'] = $orderNumber;
            $temp['notAnswered'] = 0;
            $messageCount = 0;
            foreach ($tickets as $message) {
                if ($orderNumber == $message['orderid'] && $message['status'] != 'close') {
                    $messageCount++;
                    if ($message['notAnswered']) {
                        $temp['notAnswered'] = 1;
                    }
                }
            }
            $temp['messageCount'] = $messageCount;
            $orderList[] = $temp;
        }

        return $orderList;
    }
}
