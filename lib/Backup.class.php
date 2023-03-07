<?php

class Backup
{
    // Не добавляем в бэкап следующие папки
    private $noBackupPath = array(
        '.', '..', 'backup', 'cache',
        'install', 'updates', 'uploaded', 'new-admin-proto',
        '_updates', 'cgi-bin', 'www.', '_html_layouts', 'public_html',
        'public_html_real',
    );

    private function isBackupable($file)
    {
        if (in_array($file, $this->noBackupPath) || (strripos($file, 'www.') !== false)) {
            return false;
        }
        if (strpos($file, '.git') === 0) {
            return false;
        }
        return true;
    }

    public function CreateBackup($path, $name)
    {
        $tmp = scandir($path . '/');
        foreach ($tmp as $file) {
            if (! $this->isBackupable($file)) {
                continue;
            }
            if (file_exists($path . '/' . $file)) {
                $files[] = $path . '/' . $file;
            }
        }
        if (file_exists($path . '/updates/version.xml')) {
            $files[] = $path . '/updates/version.xml';
        }
        if (! is_dir($path . '/' . 'backup')) {
            mkdir($path . '/' . 'backup');
        }

        $archive = new PclZip($path.'/'.'backup/'.$name.''.date('Y-m-d_h-i-s').'.zip');
        $v_list = $archive->create($files, PCLZIP_OPT_REMOVE_PATH, $path);
        if ($v_list == 0) {
            die("Error : " . $archive->errorInfo(true));
        }

        return Lang::get('Site_backup_successfull');
    }

    public function dbBackup($path, $host, $user, $pass, $name)
    {
        $link = mysqli_connect($host, $user, $pass);
        mysqli_select_db($link, $name);

        $excludeTables = array('cache', 'memory_cache');

        $tables = array();
        $result = mysqli_query($link, 'SHOW TABLES');
        while ($row = mysqli_fetch_row($result)) {
            if (! in_array($row[0], $excludeTables)) {
                $tables[] = $row[0];
            }
        }

        $dumpSQL = '';
        foreach ($tables as $table) {
            $result = mysqli_query($link, 'SELECT * FROM '.$table);
            $num_fields = mysqli_num_fields($result);

            $dumpSQL .= 'DROP TABLE `'.$table.'`;';
            $row2 = mysqli_fetch_row(mysqli_query($link, 'SHOW CREATE TABLE `'.$table.'`'));
            $dumpSQL .= "\n".$row2[1].";\n";

            for ($i = 0; $i < $num_fields; $i++) {
                while ($row = mysqli_fetch_row($result)) {
                    $dumpSQL .= 'INSERT INTO `'.$table.'` VALUES(';
                    for ($j=0; $j<$num_fields; $j++) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = str_replace("\n" , "\\n", $row[$j]);
                        if (isset($row[$j])) {
                            $dumpSQL .= '"'.$row[$j].'"' ;
                        } else {
                            $dumpSQL .= '""';
                        }
                        if ($j < ($num_fields-1)) {
                            $dumpSQL .= ',';
                        }
                    }
                    $dumpSQL .= ");\n";
                }
            }
            $dumpSQL .= "\n\n";
        }

        file_put_contents($path . '/backup/db-backup-'.date('Y-m-d_h-i-s').'.sql', $dumpSQL);
    }
}