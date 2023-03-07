<?php

class InstanceOptionsInfo
{
    /**
     * @var OTAPILib
     */
    protected $otapilib;

    /**
     * @param OTAPILib $otapilib
     */
    public function __construct($otapilib)
    {
        $this->otapilib = $otapilib;
    }

    /**
     * @param $data
     * @return stdClass
     */
    public function prepareTariff($data)
    {
        $result = new stdClass();
        $data = $data['Tariff'];

        $result->Id = $data['Id'];
        $result->Name = $data['Name'];
        $result->IsEnabled = $data['IsEnabled'];
        $result->CallLimit = $data['CallLimit'];
        $result->CallPrice = round($data['CallPrice'], 2);
        $result->TurnoverPercent = round($data['TurnoverPercent'], 2);

        return $result;
    }

    public function GetTariff($sid)
    {
        return $this->prepareTariff($this->otapilib->GetInstanceOptionsInfo($sid));
    }

    public function GetIsEmailConfirmationUsed()
    {
        $data = $this->otapilib->GetInstanceOptionsInfo(Session::get('sid'));
        if ($data['IsEmailConfirmationUsed'] === 'true') {
            return 'true';
        }

        return 'false';
    }
    
    public function GetCommonInstanceOptionsInfo()
    {
        $data = $this->otapilib->GetCommonInstanceOptionsInfo();
        return $data;
    }

    public function SaveOptions($request)
    {
        $settings = $this->generateSaveOptionsXml($request);
        return $this->otapilib->UpdateInstanceOptions(Session::get('sid'), $settings);
    }

    /**
     * @param RequestWrapper $request
     * @return string
     */
    private function generateSaveOptionsXml($request)
    {
        $settings = new SimpleXMLElement('<InstanceOptionsData></InstanceOptionsData>');

        if ($request->getValue('IsEmailConfirmationUsed')){
            $settings->addChild('IsEmailConfirmationUsed', $request->getValue('IsEmailConfirmationUsed'));
        }
        
        if ($request->getValue('DefaultItemProvider')){
            $settings->addChild('DefaultItemProvider', $request->getValue('DefaultItemProvider'));
        }

        $settingDom = dom_import_simplexml($settings);
        return $settingDom->ownerDocument->saveXML($settingDom->ownerDocument->documentElement);
    }
}
