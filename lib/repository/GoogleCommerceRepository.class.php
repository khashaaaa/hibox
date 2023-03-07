<?php

class GoogleCommerceRepository extends Repository
{
	
	protected $table = 'site_payed_orders';
	
    private function setTransferedOrder($id,$amount)
    {		
        	$this->cms->checkTable($this->table);
        	$q = "INSERT INTO `".$this->table."` (`id`, `amount`) VALUES ('".$id."', '".$amount."')";
			$r = $this->cms->query($q);        	
		
    }
	
	
	private function updateTransferedOrder($id,$amount)
    {		
        	$this->cms->checkTable($this->table);
        	$q = "UPDATE `".$this->table."` SET `id`='".$id."', `amount`='".$amount."' WHERE `id`='".$id."'";
			$r = $this->cms->query($q);      
        			
    }
	
	public function CheckOrder($id,$amount)
    {
			$id = $this->cms->escape($id);
			$amount = $this->cms->escape($amount);
        	$this->cms->checkTable($this->table);
        	$q = 'SELECT * FROM `'.$this->table.'` WHERE `id`="'.$id.'"';
			$data = $this->cms->queryMakeArray($q);			
        	if (! count($data) or ($data[0]['amount'] <> $amount)) {				
				return false;
			} else {			
				return true;
				
			}		
    }
	
	public function checkAndSaveOrders($orders)
    {				
        $this->cms->checkTable($this->table);
		foreach ($orders as $order) {
            $q = 'SELECT * FROM `'.$this->table.'` WHERE `id`="'.$order['id'].'"';
		    $data = $this->cms->queryMakeArray($q);			
            if (! count($data)) {
			    $this->setTransferedOrder($order['id'],$order['amount']);				
		    } else {				
			    $this->updateTransferedOrder($order['id'],$order['amount']);
		    }
		}			
    }	
	
}
