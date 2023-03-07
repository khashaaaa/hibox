<?php

/*
	Данный код сканирует папку с изображениями и возвращает объект JSON 
	с именами файлов. Он используется в jQuery для вывода изображений 
	на главной странице:
*/

// Стандартный заголовок данных JSON:
header('Content-type: application/json');

$perPage = 24;

// Сканируем папку миниатюр: E:\WORK\home\test1.ru\www\files\ItemCam\thumbs
// ищем именно по шаблону номер заказа, номер товара (к прмиеру)
$nme = $_GET['nme']; //Получаем шаблон имени
$g = glob('../../../files/ItemCam/thumbs/'.$nme.'*.jpg');

if(!$g){
	$g = array();
}

$names = array();
$modified = array();

// Цикл по всем именам файлов, возвращенных функцией glob.
// Второй файл заполняется временем снимка.

for($i=0,$z=count($g);$i<$z;$i++){
	$path = explode('/',$g[$i]);
	$names[$i] = array_pop($path);
	
	$modified[$i] = filemtime($g[$i]);
}

// Сортировка массива с именами
// в соответствии с временем снимка, сохраненным в $modified:

array_multisort($modified,SORT_DESC,$names);

$start = 0;

// browse.php также генерирует страницы результатов с опциональным параметром
// GET с указанием имени файла, с которого начинается список страницы:

if(isset($_GET['start']) && strlen($_GET['start'])>1){
	$start = array_search($_GET['start'],$names);
	
	if($start === false){
		// Такого изображения нет
		$start = 0;
	}
}

// nextStart возвращает имя следующего файла.
// Таким образом, скрипт может передать их как параметр $_GET['start'],
// если нажата кнопка "Загрузить еще"

$nextStart = '';

if (count($names)>$start+$perPage){

	if($names[$start+$perPage]){
		$nextStart = $names[$start+$perPage];
	}

	$names = array_slice($names,$start,$perPage);
}

// Форматируем и возвращаем объект JSON:

echo json_encode(array(
	'files' => $names,
	'nextStart'	=> $nextStart
));

?>