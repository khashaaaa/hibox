<?php
class Sb_invoice extends GeneralUtil{
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'sb_invoice';
    protected $_template_path = 'site_config/';
	protected $checkFields=array(
		'name_of_payee',
		'INN_of_payee',
		'account_number_of_payee',
		'bank_name_of_payee',
		'bank_identification_code',
		'correspondent_bank_account',
		'description_of_payment',
	);

    public function __construct(){
		parent::__construct();
    }

    public function defaultAction(){
		if(!$this->checkAuth()||$_SESSION['active_lang_admin']!=='ru'||!$this->cmsStatus){
			header('Location: index.php?cmd=siteConfiguration');
			return false;
		}

		$this->tpl->assign('siteConfig', $this->cms->getSiteConfig());
		$this->tpl->assign('fields', $this->checkFields);

		print $this->fetchTemplate();
    }

    public function saveAction(){
		if ($checkError=CMS::QuittanceDataHasErrors($_POST,$this->checkFields)){
			$this->tpl->assign('errors', $checkError);
			$this->defaultAction();
			return;
		}
        if(!$this->checkAuth()) return false;
        if(!$this->cmsStatus)
            return $this->setErrorAndRedirect(LangAdmin::get('error_connecting_to_database'),
                'index.php?cmd=sb_invoice');

        $this->cms->saveSiteConfig($_POST);
        header('Location: index.php?cmd=sb_invoice');
    }
}
