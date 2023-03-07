<?php

class WebUISettings {
    /**
     * @var OTAPIlib
     */
    private $otapilib;

    public function __construct($otapilib)
    {
        $this->otapilib = $otapilib;
    }

    /**
     * @param RequestWrapper $request
     */
    public function SetCategoryMode($mode){
        $currentWebUISettings = $this->otapilib->GetWebUISettings(Session::get('sid'));
        $currentWebUISettings->Settings->SelectedCategoryStructureType = $mode;        
        $this->otapilib->SetWebUISettings(Session::get('sid'), $currentWebUISettings->Settings->asXML());
    }

    public function GetWebUISettings(){
        return $this->otapilib->GetWebUISettings(Session::get('sid'));
    }
    
    public function UpdateStatisticsSettings($mode){
        $xml = "<StatisticsSettingsUpdateData><IsNeedCollect>" . $mode . "</IsNeedCollect></StatisticsSettingsUpdateData>";        
		return $this->otapilib->UpdateStatisticsSettings(Session::get('sid'),$xml);	        
    }
    
    
}