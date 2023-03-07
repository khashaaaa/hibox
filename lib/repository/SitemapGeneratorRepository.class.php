<?php

class SitemapGeneratorRepository extends Repository {

	public function __construct($cms){
		parent::__construct($cms);
	}

	public function Check(){
		return $this->cms->Check();
	}

	public function getBlock($type){
		return $this->cms->getBlock($type);
	}

	public function GetPageByID($id){
		$cRep = new ContentRepository($this->cms);
		return $cRep->GetPageByID($id);
	}
	
	public function checkTable($tableName){
		return $this->cms->checkTable($tableName);
	}

	public function getSitePagesParents($id){
		return $this->cms->queryMakeArray("SELECT * FROM `site_pages_parents` WHERE `parent_id` = $id  ORDER BY `menu_order`");
	}
	



}