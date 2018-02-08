<?php
list($lang)= preg_split ("/,/", getenv("HTTP_ACCEPT_LANGUAGE"));
define("LANG",$lang);
define ("LIMIT_LIST", 25); 
define('LOWERCASE',3);
define('UPPERCASE',1);
define("DEFAULT_SORT", 1);
define("CACHE_TIME", 60);
?>
