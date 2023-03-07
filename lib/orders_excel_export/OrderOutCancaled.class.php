<?php
class OrderOutCancaled
{
    public static function ExportOrderCut($mass){	   
	   $tmp = array();
	   foreach($mass['SalesLinesList'] as $number => $line){
       		if (((int)$line['StatusId']!=12) and ((int)$line['StatusId']!=13)) { 
				$tmp[$number] = $line;
			}
	   }
	   $mass['SalesLinesList'] = array();
	   $mass['SalesLinesList'] = $tmp;	     
	   return $mass;
    }

    
}
