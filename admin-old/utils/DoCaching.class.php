<?php
// Данный класс делает кеширование, вызывается запросом admin-old/index.php?cmd=doCaching
require BASE_ADMIN_PATH.'cron/cronHelper/cron.helper.php';

class DoCaching extends GeneralUtil {

	public $site_conf;
	
	public function defaultAction() 
    {	
		$this->site_conf = $this->cms->getSiteConfig();
		$this->site_conf = $this->site_conf[1];

		if(!$this->site_conf['caching_lvl1'] && !$this->site_conf['caching_lvl2'] && !$this->site_conf['caching_lvl3'])
			exit('Кеширование отключено');
			
		//	Проверить, если данный скрипт уже не выполняется CRON-ом, продолжать дальше...
		if(($pid = cronHelper::lock()) !== FALSE) 
		{			
			$last_update_time = $this->getLastUpdateTime();
			if($last_update_time){
				$now = time();
				$last_update_since_epoch = strtotime($last_update_time); //this converts the string to the seconds since epoch
				$seconds_passed = $now - $last_update_since_epoch;
				$hours_passed = $seconds_passed/3600;
			}
			
			// Если спустя последнего обновления таблицы прошло больше 3-х часов, или задан параметер 
			// $_GET['recreate'], генерируем новую таблицу ссылок для кеширования
			if( $hours_passed>3 || isset($_GET['recreate']) || !$last_update_time) $this->createCachingToDo();
			
			echo "$seconds_passed секунд прошло после создания ToDo листа кешиирования страниц</br></br>";
			
			$urls = $this->getCachingToDo();
			if(count($urls)<=1) exit('Все страницы закешированы</br></br>');
			
			//Проходимся по страницам в ToDo, чтобы закешировать их
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			foreach($urls as $url)
			{
				// Не обрабатываем штамп времени
				if($url!='stamp')
				{
					curl_setopt($curl, CURLOPT_URL, $url);
					$data = curl_exec($curl);
					$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
					
					// Если страница обработанна правильна, удаляем ее из ToDo
					if($http_status == 200)
					{
						$this->removeUrl($url);
						echo "Страница $url успешано закеширована и удалена из ToDo <br/>";
					}
					else 
					{
						echo "Ощибка при кешировании страницы $url <br/>";
					}
				}
			}
			curl_close($curl);
			
		}
		
    }
	
	// Данный метод генерирует лист ссылок которые нужно прокешировать и помещает их БД
    public function createCachingToDo()
	{	
		global $otapilib;
		//define('TS_HOST_NAME', preg_replace( '~:[0-9]+$~', '', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '' ));
		//define('CFG_BASE_HREF', 'http://'.TS_HOST_NAME);	
		
		//Это условие случается при сохранении данных в CMD админке, т.к. defaultAction не вызывается
		if(!isset($this->site_conf))
		{
		 	$this->site_conf = $this->cms->getSiteConfig();
			$this->site_conf = $this->site_conf[1];
		}
				
		// Получение ссылок с главной страницы
		if($this->site_conf['caching_lvl1'])
		{
			$recommend = $otapilib->GetItemRatingList('Best',20,0);
            $popular = $otapilib->GetItemRatingList('Popular',20,0);
			foreach($recommend as $item)
			{
				$id=$item['id'];
				//$url = CFG_BASE_HREF.'/index.php?p=item&id='.$id;
				$url = 'http://'.$_SERVER['SERVER_NAME'].'/index.php?p=item&id='.$id;
				$first_level_urls[] = $url;
			}
			foreach($popular as $item)
			{
				$id=$item['id'];
				//$url = CFG_BASE_HREF.'/index.php?p=item&id='.$id;
				$url = 'http://'.$_SERVER['SERVER_NAME'].'/index.php?p=item&id='.$id;
				$first_level_urls[] = $url;
			}
		}
	
		// Получение второго и третьего уровней ссылок меню
		// Если включена функция кеширования хотя бы одного из уровней меню
		if($this->site_conf['caching_lvl2'] || $this->site_conf['caching_lvl3'])
		{
			//Получаем все категории первого уровня
			$root_cats = $otapilib->GetRootCategoryInfoList();
			
			//Работаем с 10 категорияму, которые выводятся в боковом менью
			for($i=0;$i<10;$i++)
			{	
				$id		= $root_cats[$i]['id'];
					if(!$id) break;	// Если категорий меньше чем 10, выходим из цикла
				//$name 	= $root_cats[$i]['name'];			
				//$cat_type = ($root_cats[$i]['IsParent']=='true')?'subcategory':'category'; ???
				$cat_type = 'subcategory';
				
				// Заполняем массив кеширования второго уровня
				//$url = CFG_BASE_HREF.'/index.php?p='.$cat_type.'&cid='.$id;
				$url = 'http://'.$_SERVER['SERVER_NAME'].'/index.php?p='.$cat_type.'&cid='.$id;
				$second_level_urls[] = $url;
				
				// Если включено кеширование третьего уровня то получаем ссылки страниц, если же нет, напрасно не 
				// делаем запросы к DB, чтобы не понижать производительность			
				if($this->site_conf['caching_lvl2'])
				{	
					//Для каждой категории выводится 4 субкатегории
					$sub_cats = $otapilib->GetCategorySubcategoryInfoList($root_cats[$i]['id']);
					for($j=0;$j<4;$j++)
					{
						$sub_id = $sub_cats[$j]['id'];
							if(!$sub_id) break;	// Если категорий меньше чем 4, выходим из цикла
						//$sub_name = $sub_cats[$j]['name'];
						$sub_cat_type = ( $sub_cats[$j]['IsParent']=='true' )?'subcategory':'category';
		
						// Заполняем массив кеширования третьего уровня
						//$sub_url = CFG_BASE_HREF.'/index.php?p='.$sub_cat_type.'&cid='.$sub_id;
						$sub_url = 'http://'.$_SERVER['SERVER_NAME'].'/index.php?p='.$sub_cat_type.'&cid='.$sub_id;
						$third_level_urls[] = $sub_url;
					}
				}
			}
		}
		
		// Для отладки использовать echo '<pre>'.print_r($array_name,1).'</pre>';
		
		$cache_urls = array();
		
		if($this->site_conf['caching_lvl1']==1)
		{
			$cache_urls =  array_merge($cache_urls, $first_level_urls);
		}
		if($this->site_conf['caching_lvl2']==1)
		{
			$cache_urls =  array_merge($cache_urls, $second_level_urls);
		}
		if($this->site_conf['caching_lvl3']==1)
		{
			$cache_urls = array_merge($cache_urls, $third_level_urls);
		}
		
		$this->saveCachingToDo($cache_urls);

	}
	// Сохраняет сгенерируемые ссылки в БД 
	public function saveCachingToDo($urls)
	{
		// Проверяем, если таблица site_caching_todo не существует...
		$table = mysql_query("SHOW TABLES LIKE 'site_caching_todo'");
		$tableExists = mysql_num_rows($table) > 0;
		// ...создаем ее
		if(!$tableExists) 
		{	
			mysql_query("CREATE TABLE `site_caching_todo` (
						  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
						  `url` varchar(255) NOT NULL,
						  `timestamp` timestamp NOT NULL
						)");
		}
		else // Если же таблица существует
		{ 
			//Очищаем предыдущий ToDO лист
			mysql_query("TRUNCATE TABLE `site_caching_todo` ");
		}
		
		// Записываем туда ссылки страниц которые нужно прокешировать
		foreach($urls as $url)
		{
			// ВНИМАНИЕ данный метод защиты от SQL иньекций УСТАРЕЛ согласно документации
			// http://php.net/manual/en/function.mysql-real-escape-string.php
			$url = mysql_real_escape_string($url);
			$q = mysql_query("INSERT INTO `site_caching_todo`(`url`) VALUES ('$url')");
		}
		// Создаем виртуальный URL Для мониторинга времени создания таблицы
		$q = mysql_query("INSERT INTO `site_caching_todo`(`url`) VALUES ('stamp')");
	}
	
	private function removeUrl($url)
	{
		$url = mysql_real_escape_string($url);
		$q = mysql_query("DELETE FROM `site_caching_todo` WHERE `url` = '$url' ");
	}
	// Возвращает последнее время обновления таблицы ссылок кеширования
	private function getLastUpdateTime()
	{
		$q = mysql_query("SELECT `timestamp` FROM `site_caching_todo` ORDER BY `timestamp` DESC LIMIT 1");

		if(mysql_num_rows($q)>0)
		{
			$result = mysql_fetch_array($q);
			return $result[0];
		}
		else
		{
			return false;
		}
	}
	// Получаем ссылки для кеширования из ДБ
	private function getCachingToDo()
	{
		$q = mysql_query("SELECT `url` FROM `site_caching_todo`");
		
		$urls = array();
		while($row = mysql_fetch_assoc($q))
		{
            $urls[] = $row['url'];
        }
        return $urls;
	}
}