<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 *
 * See http://code.google.com/p/minify/wiki/CustomSource for other ideas
 **/
$path = isset($_GET['path']) ? $_GET['path'] : '/config';
$configFile = $_SERVER['DOCUMENT_ROOT'] . $path . '/assets.php';

return require $configFile;