<?

if (PHP_SAPI !== 'cli') {
    die('cli only');
}

try {
    $file = !empty($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'log.txt';
    $fh = fopen($file, "rt") or die('Can\'t open file "' . $file . '"!');
    $log = fread($fh, filesize($file));
    fclose($fh);
    unlink($file);
    echo $log;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
