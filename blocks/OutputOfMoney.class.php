<?php

class OutputOfMoney extends GenerateBlock
{
    protected   $_template = 'outputofmoney';
    protected   $_template_path = '/privateoffice/';
    private     $_account_info;

    public function __construct()
    {
        parent::__construct(true);
    }

    protected function setVars()
    {
        if (! Session::getUserData()) {
            header('Location: ' . UrlGenerator::getHomeUrl());
        }

        $this->_account_info = $this->_getUserData();
        if ((!$this->_account_info) or ((int)$this->_account_info['id']==0)) {
            header('Location: ' . UrlGenerator::getHomeUrl());
        }

        $availableamount = !empty($this->_account_info['availableamount']) ? (int) $this->_account_info['availableamount'] : 0;
        $this->tpl->assign('amount', $availableamount);
        $this->tpl->assign('comment', $this->request->getValue('comment'));
        $this->tpl->assign('success', $this->request->valueExists('success') ? Lang::get('data_updated_successfully') : '');
        $this->tpl->assign('error', $this->request->valueExists('error') ? Lang::get('error_occured') : '');

        if ($this->request->getMethod() == 'POST') {
            Notifier::notifyAdminWithdrawMoney(
                $this->_account_info,
                $availableamount,
                $this->request->getValue('amount'),
                $this->request->getValue('comment')
            );

            // Создается тикет с категорией Вывод стредств
            $supportRepository = new SupportRepository(new CMS());
            $categoryId = null;
            $orderId = null;
			try {
				$SupportRepository = new SupportRepository(new CMS());
            	$ticketId = $supportRepository->createTicket((int)$this->_account_info['id'], $orderId, $categoryId, Lang::get('Withdrawals'), Lang::get('sum') . ': ' . $this->request->getValue('amount') . "\n" . $this->request->getValue('comment'),false, $this->_account_info['login']);
			} catch (DBException $e) {
           		Session::setError($e->getMessage(), 'DBError');                
        	}
            if ($ticketId) {
                // перенаправить клиента на созданный тикет в службе поддержки
                header('Location: ' . UrlGenerator::generateSupportUrl(array('mode' => 'chat', 'id' => $ticketId)));
                die();
            }

            header('Location: /?p=outputofmoney&success');
        }
    }

    private function _getUserData()
    {
        $sid = Session::getUserDataSid();
        $data = array();
        $user_info = $this->otapilib->GetUserInfo($sid);
        $accountinfo = $this->otapilib->GetAccountInfo($sid);
        if ($accountinfo) {
            $data = array_merge($data, $accountinfo);
        }
        if ($user_info) {
            $data = array_merge($data, $user_info);
        }
        return $data;
    }
}
