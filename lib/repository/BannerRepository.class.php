<?php

class BannerRepository extends Repository
{
    public function GetBanners($lang = false)
    {
			$array = array();
        	$this->cms->checkTable('banners');
			$where = $lang ? 'WHERE `lang`="'.$lang.'"' : '';
        	$sql = 'SELECT * FROM `banners` '.$where.' ORDER BY `sort` ASC';
			$r = $this->cms->query($sql);
			if ($r && mysqli_num_rows($r)) {
				while ($row = mysqli_fetch_assoc($r)) {
					$array[] = $row;
				}
			}
        	return $array;
		
    }
    
    public function GetOneBanner($id)
    {
			$array = array();
        	$this->cms->checkTable('banners');			
        	$sql = 'SELECT * FROM `banners` WHERE `id`="'.$id.'" ORDER BY `sort` ASC';
			$r = $this->cms->query($sql);
			if ($r && mysqli_num_rows($r)) {
                return mysqli_fetch_assoc($r);				
			}
        	return false;
		
    }
	
	public function AddBanner($data,$source)
    {
			$this->cms->checkTable('banners');			
        	$sql = "INSERT INTO `banners`
                    SET
                        `source` = '".$this->cms->escape($source)."',
                        `name` = '".$this->cms->escape($data['desc'])."',
                        `url` = '".$this->cms->escape($data['link'])."',
                        `lang` = '".$this->cms->escape($data['language'])."',
                        `content` = '".$this->cms->escape($data['content'])."'";
			$this->cms->query($sql);
		
    }
	
	public function EditBanner($data,$source)
    {
			$this->cms->checkTable('banners');			
        	$sql = "UPDATE `banners`
                    SET
                        `source` = '".$this->cms->escape($source)."',
                        `name` = '".$this->cms->escape($data['desc'])."',
                        `url` = '".$this->cms->escape($data['link'])."',
                        `lang` = '".$this->cms->escape($data['language'])."',
                        `content` = '".$this->cms->escape($data['content'])."'
                    WHERE `id`= '".$data['id']."'";
			$this->cms->query($sql);
		
    }
	
	public function UpdateBanner($id,$sort)
    {
			$this->cms->checkTable('banners');			
        	$sql = "UPDATE `banners`
            SET
                `sort` = '".$sort."' WHERE `id`= '".$id."'
        	";
			$this->cms->query($sql);
		
    }
	
	public function DelBanner($id)
    {
			$this->cms->checkTable('banners');			
        	$sql = "DELETE FROM `banners` WHERE `id`='".$id."'";
			$this->cms->query($sql);
		
    }
	
	
	
	
}
