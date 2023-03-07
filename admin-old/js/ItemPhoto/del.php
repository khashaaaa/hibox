<?php

$nme = $_GET['nme']; //Получаем  имени


unlink('../../../files/ItemCam/thumbs/'.$nme);
unlink('../../../files/ItemCam/'.$nme);



?>