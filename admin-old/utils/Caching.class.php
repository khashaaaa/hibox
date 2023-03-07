<?php

// Для создания ToDo листа кеширования при сохранении
//Ошибки - не покатит
//include('DoCaching.class.php');

class Caching extends GeneralUtil { 
    
    function defaultAction()
    {
        if (Login::auth())
        {
            global $otapilib;
            $sid = $_SESSION['sid'];
			
			$site_conf = $this->cms->getSiteConfig();
			$site_conf = $site_conf[1];
			
            if ($otapilib->error_message == 'SessionExpired')
            {
                header('Location: index.php?expired');
                die;
            }
            include(TPL_DIR.'caching.php');
        } else {
            include(TPL_DIR.'login.php');
        }
    }
	
	// Сохранение настроек кешинга
    public function saveSettingsAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
		
		$caching = $_POST['caching'];
		
		$site_conf['caching_lvl1'] 			= (isset($caching['lvl1'])) ? 1 : 0;
		$site_conf['caching_lvl2'] 			= (isset($caching['lvl2'])) ? 1 : 0;
		$site_conf['caching_lvl3'] 			= (isset($caching['lvl3'])) ? 1 : 0;
		$site_conf['caching_time_hours'] 	= $caching['time-hours'];
		$site_conf['caching_time_minutes'] 	= $caching['time-minutes'];
		
		$cron_config_path = "cron/config.txt";
		
		if(!$site_conf['caching_lvl1'] && !$site_conf['caching_lvl2'] && $site_conf['caching_lvl3'])
			$cron_config = ''; //If no caching is defined, then empying config file
		else 
			$cron_config =  "{$site_conf['caching_time_minutes']} {$site_conf['caching_time_hours']} * * * ".BASE_PATH."admin-old/cron/cacheme.php";
			//$cron_config =  "{$site_conf['caching_time_minutes']} {$site_conf['caching_time_hours']} * * * ".BASE_PATH."admin-old/index.php?cmd=doCaching";
		
		file_put_contents($cron_config_path, $cron_config);
		$this->cms->saveSiteConfig($site_conf);
		
		// Создаем новый ToDo лист кеширования
		// - Увы такое не прокатит - ошибки при сохранении - создание списка при первом запуске по крону!!!!
		//$caching_class = new DoCaching();
		//$caching_class->createCachingToDo();

        header('Location: index.php?cmd=caching');
    }
    
}