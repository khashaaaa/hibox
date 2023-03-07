<?php

class DiscountProvider 
{
    /**
     * @var OTAPIlib
     */
    private $otapilib;
    
    const TYPEAHEAD_USERS_LIMIT = 5;

    public function __construct($otapilib)
    {
        $this->otapilib = $otapilib;
    }

    public function getDiscountGroupList()
    {
        $result = $this->otapilib->GetDiscountGroupList(Session::get('sid'));
        return $result;
    }

    public function getUsersOfDiscountGroup($id, $from, $perpage)
    {
        $result = $this->otapilib->GetUsersOfDiscountGroup(Session::get('sid'), $id, $from, $perpage);
        return $result;
    }

    public function searchUsersOfDiscountGroup($request, $perpage, $page)
    {
        $id = $request->get('groupId');
        $from = ($page > 1) ? ($page - 1) * $perpage : 0;

        $xmlUserFilters = $this->generateFindUsersXml($request);
        $result = $this->otapilib->SearchUsersOfDiscountGroup(Session::get('sid'), $id, $from, $perpage, $xmlUserFilters);
        return $result;
    }

    public function saveDiscountGroup($params)
    {
		$xml = $this->generateSaveDiscountGroupXml($params);
		if (! empty($params['Id'])) {
			$result = $this->otapilib->UpdateDiscountGroup(Session::get('sid'), $params['Id'], $xml);            
		} else {
		    $result = $this->otapilib->AddDiscountGroupToInstance(Session::get('sid'), $xml);
		}
        return $result;
    }	
	
	public function removeDiscountGroup($id)
    {		
        $result = $this->otapilib->RemoveDiscountGroupFromInstance(Session::get('sid'), $id);
        return $result;
    }
	
	public function removeUserFromDiscount($groupId, $userId)
    {		
        $result = $this->otapilib->RemoveUserFromDiscountGroup(Session::get('sid'), $groupId, $userId);
        return $result;
    }
	
	public function addUserToDiscount($groupId, $userId)
    {		
        $result = $this->otapilib->AddUserToDiscountGroup(Session::get('sid'), $groupId, $userId);
        return $result;
    }
	
	public function findBaseUserInfoList($request)
    {
		$xml = $this->generateFindUsersXml($request);
        $result = $this->otapilib->FindBaseUserInfoListFrame(Session::get('sid'), $xml, 0, self::TYPEAHEAD_USERS_LIMIT);
        return $result;
    }

    public function getUserDiscountGroupForOperator($userId)
    {
        $result = $this->otapilib->GetUserDiscountGroupForOperator(Session::get('sid'), $userId);
        return $result;
    }

    private function generateSaveDiscountGroupXml($params)
    {				
		if (! empty($params['Id'])) {
			$xmlParams = new SimpleXMLElement('<DiscountGroupUpdateData></DiscountGroupUpdateData>');           
		} else {
		    $xmlParams = new SimpleXMLElement('<DiscountGroupAddData></DiscountGroupAddData>');
		}		
		if (! empty($params['Name'])) {
            $xmlParams->addChild('Name', $params['Name']);
        }
		if (! empty($params['Description'])) {
            $xmlParams->addChild('Description', $params['Description']);
        } else {		
            $xmlParams->addChild('Description', '');
        }
        if (isset($params['Discount'])) {
			$el = $xmlParams->addChild('Discount');
            $el->addChild('Percent', $params['Discount']);
		}
        if (isset($params['IsDefault']) && $params['IsDefault']) {
            $xmlParams->addChild('IsDefault', 'true');
        } else {
            $xmlParams->addChild('IsDefault', 'false');
        }

		$el = $xmlParams->addChild('DiscountIdentificationParametr');

        $el->addChild('PurchaseVolume', $params['DiscountIdentificationParametr']['PurchaseVolume']);

        $elProvidersContent = $el->addChild('Providers')->addChild('Content');
        if (! empty($params['DiscountIdentificationParametr']['Providers'])) {
            foreach ($params['DiscountIdentificationParametr']['Providers'] as $provider) {
                $elProvidersContent->addChild('Item', $provider);
            }
        }

        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }
	
	private function generateFindUsersXml($request)
    {		
		$param = $request->getValue('username');
		$xmlParams = new SimpleXMLElement('<UserFilterParameters></UserFilterParameters>');
		if (! empty($param)) {
            $xmlParams->addChild('Login', htmlspecialchars($param));
        }
		return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }
	
}