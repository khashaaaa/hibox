<?php

class Calculator extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'calculator'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/calculator/';

    public function __construct()
    {
        parent::__construct(true);
    }

    protected function setVars()
    {
        global $otapilib;

        $currency_list = InstanceProvider::getObject()->getCurrencyInstanceList();
        $currency_list = $otapilib->GetCurrencyInstanceList($currency_list->asXML());

        $country_list = InstanceProvider::getObject()->getDeliveryCountryInfoList();
        $country_list = $this->otapilib->GetDeliveryCountryInfoList($country_list->asXML());

        $this->tpl->assign('currency_list', $currency_list);
        $this->tpl->assign('country_list', $country_list);


    }

    /**
     * prepare data for js
     * @param array $delivery
     * @param array $countries
     * @return json
     */
    private function _getParamsForJs(array $delivery, array $countries)
    {
        $types = array();
        foreach ($delivery  as $item) {
            $item['country'] = array();
            foreach ($countries as $country) {
                if ($country['delivery_id']==$item['id'] && $country['is_active']) {
                    $item['country'][] = $country;
                }
            }
            $types[$item['id']] = $item;
        }
        return json_encode($types);
    }

    static function getCountryByDelivery()
    {
        global $otapilib;

        $cms = new CMS();
        if (!$status = $cms->Check())
            return;

		$retVal = array();
		try {
			$data = $cms->GetCountriesByDelivery();
			$retVal = array('success'=>true,'data'=>$data);
		} catch (Exception $e) {
			$retVal = array('success'=>false,'message'=>$e->getMessage());
		}
        print json_encode($retVal);
		die;
    }


}
