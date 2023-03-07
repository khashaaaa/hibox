<?php

class GetBankAccountController extends FooterController
{
    public function renderBankAccountAction()
    {
        return General::viewFetch('view/GetBankAccount', array(
            'path' => dirname(dirname(__FILE__)),
            'vars' => array(
                'result' => $this->getBankAccount()
            )));
    }
    
    public function renderOrderInfoDangerAction()
    {
	   	$cRep = new ContentRepository($this->cms);
        $page = $cRep->GetPageByAlias('order-info-danger');
        $result = ($page) ? $page['text'] : Lang::get('empty_page_msg');
        return General::viewFetch('view/OrderInfoDanger', array(
            'path' => dirname(dirname(__FILE__)),
            'vars' => array(
                'result' => $result
            )));
    }
    
    public function renderOrderInfoInformAction()
    {
	   	$cRep = new ContentRepository($this->cms);
        $page = $cRep->GetPageByAlias('order-info-inform');
        $result = ($page) ? $page['text'] : Lang::get('empty_page_msg');
        return General::viewFetch('view/OrderInfoInform', array(
            'path' => dirname(dirname(__FILE__)),
            'vars' => array(
                'result' => $result
            )));
    }

    /**
     * @param $parent
     * @return array
     */
    public function getBankAccount()
    {
	    $cRep = new ContentRepository($this->cms);
        $page = $cRep->GetPageByAlias('bank-account-number');
        $result = ($page) ? $page['text'] : Lang::get('empty_page_msg');
        
		return $result;
    }
}