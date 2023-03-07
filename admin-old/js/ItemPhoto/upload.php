<?php

/*
	Данный скрипт получает снимок JPEG
	из webcam.swf как запрос POST.
*/

// Нам нужно обрабатывать только запрос POST:
if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
	exit;
}

$nme = $_GET['nme']; //Получаем шаблон имени

$folder = '../../../files/';

//Содаем имя файла в зависисмоти от номера заказа и номера товара (пока не делал!!!)
//Было бы оч удобно отслеживать фотографии по номеру заказа и номеру товара как в админ, так и пользовтеля
$filename = $nme.''.md5(rand()).'.jpg';

$original = $folder.$filename;

// Снимок JPEG отправляется в необработанном виде:
$input = file_get_contents('php://input');
/*
if(md5($input) == '7d4df9cc423720b7f1f3d672b89362be'){
	// Черное изображение. Нам оно без надобности.
	exit;
}
*/
$result = file_put_contents($original, $input);
if (!$result) {
	echo '{
		"error"		: 1,
		"message"	: "Ошибка сохранения изображения. Убедитесь, что папка uploads и ее вложенные каталоги имеет chmod 777."
	}';
	exit;
}

$info = getimagesize($original);
if($info['mime'] != 'image/jpeg'){
	unlink($original);
	exit;
}

// Перемещаем временный файл в оригинальную папку:
rename($original,'../../../files/ItemCam/'.$filename);
$original = '../../../files/ItemCam/'.$filename;

// Используем библиотеку GD для изменения размера
// для миниатюры:

$origImage	= imagecreatefromjpeg($original);
$newImage	= imagecreatetruecolor(154,110);
imagecopyresampled($newImage,$origImage,0,0,0,0,154,110,520,370); 

imagejpeg($newImage,'../../../files/ItemCam/thumbs/'.$filename);

echo '{"status":1,"message":"Success!","filename":"'.$filename.'"}';
?>
