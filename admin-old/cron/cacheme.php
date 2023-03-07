<?php

	$controller_url = 'http://'.$_SERVER['SERVER_NAME'].'/admin-old/index.php?cmd=doCaching';
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_URL, $controller_url);
	$data = curl_exec($curl);
	echo $data;
	curl_close($curl);
	
?>