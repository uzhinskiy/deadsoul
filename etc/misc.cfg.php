<?php
/**
* Конфигурационный файл
* @package	deadsoul
* @author	Ужинский Борис email - boris.ujinsky@gmail.com
* @file		misc.cfg.php
* @version	0.0.2
* @filesource
*/
list($lang)= preg_split ("/,/", getenv("HTTP_ACCEPT_LANGUAGE"));
define("LANG",$lang);
define ("LIMIT_LIST", 25); 
define('LOWERCASE',3);
define('UPPERCASE',1);
define("DEFAULT_SORT", 1);
define("CACHE_TIME", 60);
?>
