<?php
/**
* Конфигурационный файл
* @package	deadsoul
* @author	Ужинский Борис email - boris.ujinsky@gmail.com
* @file		main.cfg.php
* @version	0.0.3
* @filesource
*/

ob_start("ob_gzhandler");
function someInputCheck($var) {
	if ($var)
		return @htmlspecialchars($var,ENT_QUOTES);
}

$_REQUEST = array_map("someInputCheck", $_REQUEST);
// Название пакета
define ("PACKAGE","deadsoul");
$base_root = getenv('SERVER_NAME').((dirname($_SERVER["SCRIPT_NAME"]) == "/") ? "" : dirname($_SERVER["SCRIPT_NAME"]));
//Уникальный идентификатор сайта
define("SITE_ID", substr(md5(getenv('SERVER_NAME')),0,4));
// define CLI or BROWSER
if(php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])) {
	define("CLI", true);
} else {
	define("CLI", false);
}

// CURRENT SCRIPT
define("THIS_PAGE", $_SERVER["SCRIPT_NAME"]);

// set ROOT PATH to progrms files
define ("ROOT", ($_SERVER["DOCUMENT_ROOT"]) ? $_SERVER["DOCUMENT_ROOT"]:realpath("../"));

// set BASE url for all hrefs and anchors
define ("BASE", "http://$base_root/") ;

//set path to PHP LIB files
define ("LIBPHP", "/lib");

//set path to log-files files
define ("LOGS", "/var/log");

define ("CACHE", "/var/cache");
define ("TMP", "/var/tmp");
define ("UPLOAD", "/var/upload");

define ("COOKIE_EXPIRY",60*60*24*365);
ini_set("session.save_handler", "memcached");
ini_set("session.save_path", "localhost:11211:75");
ini_set("session.cookie_path","/");
ini_set("session.cookie_domain", getenv('SERVER_NAME'));

define ("DEBUG_LEVEL", E_ALL);
error_reporting(DEBUG_LEVEL);
ini_set( "display_errors", "0" );

include "db.cfg.php";
include "misc.cfg.php";
?>
