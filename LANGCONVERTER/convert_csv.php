<?php

/*
 * Конвертер из файла file.csv в файл lang.xml
 * в файле file.csv в первой колонке key, во второй name
 */

$xml = new SimpleXMLElement('<translations/>');

if (($handle = fopen("file.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $el = $xml->addChild('key', htmlspecialchars($data[1]));
        $el->addAttribute('name', htmlspecialchars($data[0]));
    }
    fclose($handle);
}

$xml->asXML('lang.xml'); 
echo "<a href=\"lang.xml\">Скачать</a><br>";
