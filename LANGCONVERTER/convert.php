<?

include 'simplexlsx.class.php';

$xlsx = new SimpleXLSX('000.xlsx');

 $xml = new SimpleXMLElement('<translations/>');
foreach( $xlsx->rows() as $r ) {    
	$el = $xml->addChild('key', htmlspecialchars($r[2]));
    $el->addAttribute('name', htmlspecialchars($r[0]));	
}
$xml->asXML('000.xml'); 
echo "Скачать <a href=\"000.xml\">Тута</a><br>";
?>   
