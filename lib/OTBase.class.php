<?php

if (! defined('CFG_APP_ROOT')) {
    define('CFG_APP_ROOT', dirname(dirname(__FILE__)));
}

class OTBase
{
    private static $_aliases = array('system' => CFG_APP_ROOT); // alias => path

    private static $_imports = array();

    private static $_includePaths = array();

    private static $_classMap = array();

    public static function isAdmin() {
    	return Session::get('sid');
    }

    public static function isTest()
    {
        if (RequestWrapper::get('debug')) {
            return true;
        }
        if (defined('DEVELOP')) {
            return (bool) DEVELOP;
        }
        return !defined('NO_DEBUG');
    }

    public static function isLimitedFunctional($config)
    {
        $limitedFile = CFG_APP_ROOT . '/config/is_limited_functional.php';
        if (file_exists($limitedFile)) {
            $limited = require($limitedFile);
        } else {
            $limited = array();
        }

        return in_array($config, $limited);
    }

    public static function onFullDebug()
    {
        if (defined('DEVELOP') && (bool)DEVELOP) {
            return true;
        }
        return RequestWrapper::get('debug') == 'full';
    }

    public static function isMultiCurlEnabled()
    {
        return defined('CFG_MULTI_CURL') && CFG_MULTI_CURL;
    }

    /**
     * http://www.yiiframework.com/doc/api/1.1/YiiBase#import-detail
    **/
    public static function import($alias, $forceInclude = false)
    {
        if(isset(self::$_imports[$alias]))  // previously imported
            return self::$_imports[$alias];

        if(class_exists($alias,false) || interface_exists($alias,false))
            return self::$_imports[$alias]=$alias;

        if(($pos=strrpos($alias,'.'))===false)  // a simple class name
        {
            if($forceInclude && self::autoload($alias))
                self::$_imports[$alias]=$alias;
            return $alias;
        }

        $className=(string)substr($alias,$pos+1);
        $isClass=$className!=='*';

        if($isClass && (class_exists($className,false) || interface_exists($className,false)))
            return self::$_imports[$alias]=$className;

        if(($path=self::getPathOfAlias($alias))!==false)
        {
            if($isClass)
            {
                if($forceInclude)
                {
                    if(is_file($path.'.php')) {
                        require_once($path.'.php');
                        self::$_imports[$alias]=$className;
                    }
                }
                else {
                    $file = $path.'.php';
                    $file = is_file($file) ? $file : $path.'.class.php';
                    self::$_classMap[$className] = $file;
                }
                return $className;
            }
            else  // a directory
            {
                array_unshift(self::$_includePaths,$path);

                return self::$_imports[$alias]=$path;
            }
        }
        else
            throw new Exception('Alias "'.$alias.'" is invalid. Make sure it points to an existing directory or file.');
    }

    /**
     * http://www.yiiframework.com/doc/api/1.1/YiiBase#autoload-detail
    **/
    public static function autoload($className)
    {
        // use include so that the error PHP file may appear
        if(isset(self::$_classMap[$className]))
            include(self::$_classMap[$className]);
        else
        {
            // include class file relying on include_path
            if(strpos($className,'\\')===false)  // class without namespace
            {
                if (is_file($className.'.php')) {
                    include($className.'.php');
                } else {
                    foreach(self::$_includePaths as $path)
                    {
                        $classFile=$path.DIRECTORY_SEPARATOR.$className.'.php';
                        $classSuffixFile=$path.DIRECTORY_SEPARATOR.$className.'.class.php';
                        if (is_file($classFile)) {
                            include($classFile);
                            break;
                        } else if (is_file($classSuffixFile)) {
                            include($classSuffixFile);
                            break;
                        }
                    }
                }
            }
            return class_exists($className,false) || interface_exists($className,false);
        }
        return true;
    }

    /**
     * http://www.yiiframework.com/doc/api/1.1/YiiBase#getPathOfAlias-detail
     *
     * Translates an alias into a file path.
     * Note, this method does not ensure the existence of the resulting file path.
     * It only checks if the root alias is valid or not.
     * @param string $alias alias (e.g. system.web.CController)
     * @return mixed file path corresponding to the alias, false if the alias is invalid.
     */
    public static function getPathOfAlias($alias)
    {
        if(isset(self::$_aliases[$alias]))
            return self::$_aliases[$alias];
        else if(($pos=strpos($alias,'.'))!==false)
        {
            $rootAlias=substr($alias,0,$pos);
            if(isset(self::$_aliases[$rootAlias]))
                return self::$_aliases[$alias]=rtrim(self::$_aliases[$rootAlias].DIRECTORY_SEPARATOR.str_replace('.',DIRECTORY_SEPARATOR,substr($alias,$pos+1)),'*'.DIRECTORY_SEPARATOR);
        }
        return false;
    }

    public static function getVersion()
    {
        if (self::isTest()) {
            return time();
        }

        $fileVersion = CFG_APP_ROOT . '/updates/version.xml';
        if (file_exists($fileVersion)) {
           $v = simplexml_load_file($fileVersion);
           return $v->Version[0]->Number;
        } else {
           return date('y-m-d');
        }
    }

    /**
     * Parses external path and create local version files
     * @return true on success, or false otherwise
     */
    public static function setVersion()
    {
        global $otapilib;

        $fileVersion = CFG_APP_ROOT . '/updates/version.xml';

        $info = simplexml_load_file(CFG_TOOLS_URL . '/update_rep/info/info.php?phpVersion=' . phpversion());
        if ($info === false) {
            return false;
        }
        $info->Version[0]->addChild('LibVersion', $otapilib->getCurrentVersion());
        file_put_contents($fileVersion, $info->asXML());
        file_put_contents(CFG_APP_ROOT . '/userdata/finish', 'yes');
        file_put_contents(CFG_APP_ROOT . '/admin/version.txt', (string)$info->Version[0]->Number);

        if (!file_exists($fileVersion)) {
            return false;
        }

        return true;
    }
}
