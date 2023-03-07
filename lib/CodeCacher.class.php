<?php

require_once 'vendor/JSMin.class.php';
require_once 'vendor/CssMin.class.php';
                
class CodeCacher {
    
    public static function getJSCode($scripts, $useCache, $section, $ver) 
    {
        if ($useCache) {
            $cacheFileName = dirname(dirname(__FILE__)) . '/cache/admin/' . $section .'-js-' . $ver .'.js';
            
            if (! file_exists($cacheFileName)) {
                $jsCode = self::generateJSCode($scripts);
                $jsCode = JSMin::minify($jsCode);                
                $createDirResult = !file_exists( dirname(dirname(dirname(__FILE__))) . '/cache/admin' ) ? mkdir(dirname(dirname(dirname(__FILE__))) . '/cache/admin', 0777, true) : true;
                file_put_contents($cacheFileName, $jsCode);
            } else {
                $jsCode .= file_get_contents($cacheFileName);
            }
        } else {
            $jsCode = self::generateJSCode($scripts);
        }
        return $jsCode;
    }
    
    private static function generateJSCode($scripts) {
        $jsCode = '';
        foreach ($scripts as $script) {
            if (! empty($script) && $script[0] == '/') {
                // load from site root
                $scriptPath = dirname(dirname(__FILE__)) . $script;
        
            } else if (! empty($script) && $script[0] != '/') {
                // load from admin root
            }
            $jsCode .= file_get_contents($scriptPath);
            $jsCode .= "\n";
        }
        return $jsCode;        
    }
    
    public static function getCSSCode($cssList, $useCache, $section, $ver) 
    {
        if ($useCache) {
            $cacheFileName = dirname(dirname(__FILE__)) . '/cache/admin/' . $section .'-css-' . $ver .'.css';
    
            if (! file_exists($cacheFileName)) {
                $cssCode = self::generateCSSCode($cssList);
                $cssCode = CssMin::minify($cssCode); // Not correct CSS 
                $createDirResult = !file_exists( dirname(dirname(dirname(__FILE__))) . '/cache/admin' ) ? mkdir(dirname(dirname(dirname(__FILE__))) . '/cache/admin', 0777, true) : true;
                file_put_contents($cacheFileName, $cssCode);
            } else {
                $cssCode .= file_get_contents($cacheFileName);
            }
        } else {
            $cssCode = self::generateCSSCode($cssList);
        }
        return $cssCode;
    }
    
    
    public static function generateCSSCode($cssList) 
    {
        $cssCode = '';
        foreach ($cssList as $css) {
            if (! empty($css) && $css[0] == '/') {
                // load from site root
                $cssPath = dirname(dirname(__FILE__)) . $css;
        
            } else if (! empty($css) && $css[0] != '/') {
                // load from admin root
                $cssPath = dirname(__FILE__) . '/'.  $css;
            }
            if (! empty($cssPath)) {
                $cssCode .= file_get_contents($cssPath);
            }
        }
        
        return $cssCode;
        
        
        
        print $cssCode;
    }
}