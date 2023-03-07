<?php

OTBase::import('system.lib.GeneralPlugin');

class SimilarProductsPlugin extends GeneralPlugin
{
	private $cms;

	public function __construct()
	{
		parent::__construct();
		LangAdmin::getTranslations(dirname(__FILE__) . '/langs/');

		$this->cms = new CMS();
	}

	public function renderPluginPage($request)
	{
		$pageUrl = new AdminUrlWrapper();
		$pageUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

		return General::viewFetch('/view/renderPluginPage', array(
			'path' => dirname(__FILE__),
			'vars' => array()
		));
	}

	/**
	 * @param RequestWrapper $request
	 */
	public function request($request)
	{
		$route = $_REQUEST['action'];
		switch ($route) {
			case 'getSimilarProducts':
				$this->SimilarProducts($request);
				break;
		}
	}

	public function respondAjaxError($error_message)
	{
		echo (json_encode([
			'error' => true,
			'message' => $error_message
		]));
		exit;
	}

	public function SimilarProducts($request)
	{
		global $otapilib;
		try {
			$sid = Session::get('sid');
			$xml = '<SearchItemsParameters><IsClearItemTitles>false</IsClearItemTitles><ImageUrl>' . $request->getValue('image_url') . '</ImageUrl><CategoryMode>External</CategoryMode><Provider>' . $request->getValue('provider') . '</Provider><SearchMethod>Image</SearchMethod></SearchItemsParameters>';

			$response = $otapilib->BatchSearchItemsFrame($sid, $xml, 0, 20, 'SubCategories,Vendor,RootPath,SearchProperties');

			echo json_encode($response['Items']['Items']['data']);
		} catch (ServiceException $e) {
			$this->respondAjaxError($e->getMessage());
		}
	}
}