<?php

class CDEK {
    
    public static function PrintPackage($pid){
        global $otapilib;
		$sid = $_SESSION['sid'];			
		$xml = self::_SetPackageXML($pid);		
		$print = $otapilib->PrintPackageReceipt($sid, $xml);			
		if ($print) {
			$print = base64_decode($print);						
			$nme = "package-".$pid.".pdf";
			$fp = fopen(CFG_APP_ROOT."/userdata/".$nme, "w+"); 
			fwrite($fp, $print); 			
			fclose($fp); 								
			return 'Квитанция готова. <br> <a href="../userdata/'.$nme.'" target="_blank">Показать квитанцию</a>';
			
		}
    }
	
	public static function ExportPackage($pid){
        global $otapilib;
		$sid = $_SESSION['sid'];            	
		$xml = self::_SetExportPackageXML($pid);
		$answer = $otapilib->ExportPackageToDeliveryServiceSystem($sid, $xml);
		if ($answer) $message='Выгрузка выполнена<br>';
		return $message;
    }

 
	private static function _SetPackageXML($pack_id)
    {
        //=========================================================================================================
		$xmlParams = new SimpleXMLElement('<PackagePrintReceiptParameters></PackagePrintReceiptParameters>');
		$xmlParams->addChild('PackageId', $pack_id);				
		return $xmlParams->asXML();	
    }
	private static function _SetExportPackageXML($pack_id)
    {
        //=========================================================================================================
		$xmlParams = new SimpleXMLElement('<PackageExportParameters></PackageExportParameters>');
		$xmlParams->addChild('PackageId', $pack_id);				
		return $xmlParams->asXML();	
    }
    

}

?>
