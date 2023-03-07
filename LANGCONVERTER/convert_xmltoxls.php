<?

include 'simplexlsx.class.php';

$xml = simplexml_load_file('lang.xml');


require_once '../lib/PhpExcel/PHPExcel.php';
$pExcel = new PHPExcel();

require_once '../lib/PhpExcel/PHPExcel/Writer/Excel5.php';
$objWriter = new PHPExcel_Writer_Excel5($pExcel);

require_once '../lib/PhpExcel/PHPExcel/Style/Color.php';

$pExcel->setActiveSheetIndex(0);
$aSheet = $pExcel->getActiveSheet();


$aSheet->setCellValue('A1', 'Key');
$aSheet->setCellValue('B1', 'Translation');
$number = 0;

foreach( $xml as $lang ) {
    $aSheet->setCellValue('A' . ($number+2), $lang->attributes()->name);
    $aSheet->setCellValue('B' . ($number+2), (string)$lang);
    $number++;
}
$objWriter->save('langs.xls');
echo "Скачать <a href=\"langs.xls\">Тута</a><br>";
