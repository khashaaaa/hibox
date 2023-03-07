<?php

class CalculatorController extends GeneralContoller
{
    public $layout = 'twoColumn';

    public function defaultAction()
    {
        $currencyList = array();
        $deliveryCountryList = array();
        $deliveriesTable = '';

        try {
            $currencyList = InstanceProvider::getObject()->getCurrencyInstanceList();
            $currencyList = ($currencyList) ? $currencyList->GetDisplayedMoneys()->GetCurrencyInfo() : array();

            $deliveryCountryList = InstanceProvider::getObject()->getDeliveryCountryInfoList()->GetItem();

            if (
                $this->request->valueExists('price') ||
                $this->request->valueExists('currency') ||
                $this->request->valueExists('country') ||
                $this->request->valueExists('weight')
            ) {
                $deliveriesTable = $this->generateDeliveriesTable();
            }
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }
		
		$info = $this->getCalculatorInfo();
		
        return $this->render('controllers/calculator/index', [
            'priceItems' => $this->request->get('price', '0.00'),
            'currency' => $this->request->get('currency', User::getObject()->getUserPreferences()->GetCurrencyCode()),
            'country' => $this->request->get('country', User::getObject()->getUserPreferences()->GetCountryCode()),
            'weight' => $this->request->get('weight', '0.00'),
            'currencyList' => $currencyList,
            'deliveryCountryList' => $deliveryCountryList,
            'deliveriesTable' => $deliveriesTable,
            'info' => $info
        ]);
    }

    public function getDeliveriesAction()
    {
        $deliveriesTable = '';

        try {
            $deliveriesTable = $this->generateDeliveriesTable();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse(array(
            'html' => $deliveriesTable,
        ));
    }

    private function generateDeliveriesTable()
    {
        $deliveries = array();

        $countryId = $this->request->getValue('country');
        $weight = $this->request->getValue('weight');
        $currency = $this->request->getValue('currency');
        $priceItems = $this->request->getValue('price');

        $deliveryModeList = null;
        OTAPILib2::GetDeliveryModesWithPrice(Session::getActiveLang(), $countryId, $weight, $deliveryModeList);
        OTAPILib2::makeRequests();

        if ($deliveryModeList && $deliveryModeList->GetResult()) {
            $deliveries = $this->prepareDeliveries($deliveryModeList->GetResult(), $currency, $priceItems);
        }

        return $this->renderPartial('controllers/calculator/deliveries-table', [
            'deliveries' => $deliveries,
        ]);
    }

    // получаем обработанный массив с информацией о доставках
    private function prepareDeliveries($deliveryModeList, $currency, $priceItems)
    {
        $deliveries = array();

        $i = 0;
        foreach ($deliveryModeList->GetDeliveryMode() as $deliverie) {
            $price = null;
            foreach ($deliverie->GetFullPrice()->GetConvertedPriceList()->GetDisplayedMoneys()->GetMoney() as $money) {
                if ($money->GetCodeAttribute() == $currency) {
                    $price = $money;
                    break;
                }
            }
            if (!$price) {
                continue;
            }
            $deliveries[$i]['name'] = $deliverie->GetName();
            $deliveries[$i]['deliveriePrice'] = TextHelper::formatPrice($price->asString(), $price->GetSignAttribute());
            $deliveries[$i]['totalPrice'] = TextHelper::formatPrice($price->asString() + $priceItems, $price->GetSignAttribute());

            $i++;
        }

        return $deliveries;
    }
    
    public function getCalculatorInfo()
    {
	   $cRep = new ContentRepository($this->cms);
       $page = $cRep->GetPageByAlias('calculator-info');
       
       $result = ($page) ? $page['text'] : Lang::get('empty_page_msg');
       
       return $result;
    }
}
