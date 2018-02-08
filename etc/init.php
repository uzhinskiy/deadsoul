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
set_locale();

$registry = new Registry;

$cm = new Cache(PACKAGE);
$cm->connect(MEM_HOST, MEM_PORT);

$registry->set ('CM', $cm); unset($cm);
$registry->set("SEF",1);
?>
