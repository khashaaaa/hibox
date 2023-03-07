<?php

class Cacher {
    
    public static $countFiles = 0;
    
            
    public static function rRmDir($dir, $removeDir = true)
    {
        if (self::$countFiles < 1000) {
            if (is_dir($dir)) {
               $objects = scandir($dir);
               foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (filetype($dir . "/" . $object) == "dir") {
                            self::rRmDir($dir . "/" . $object); 
                        } else {
                            unlink($dir . "/" . $object);
                            self::$countFiles++;
                        }
                    }
                }
                reset($objects);
                if ($removeDir)
                    rmdir($dir);
            }        
        } else {
            self::$countFiles = 0;
            throw new Exception(LangAdmin::get('Please_reset_cache'), 101);
        }
    }

}