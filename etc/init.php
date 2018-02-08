<?php
require_once("main.cfg.php");
$MODULES = array();
function init(){
    global $MODULES;
    $libs = scandir(ROOT.LIBPHP);
    foreach ($libs as $lib) {
	if ($lib === '.' || $lib === '..' || $lib === 'sql' || $lib === 'nosql' || $lib === 'classes')  continue;
	if ( preg_match("/~/", $lib) ) continue;
	    $MODULES[substr($lib, 4, -4)] = require_once(ROOT.LIBPHP."/".$lib);
    }
    $MODULES[DB_TYPE] = require_once(ROOT.LIBPHP."/sql/lib_".DB_TYPE.".php");
}

init();
session_start();
?>
