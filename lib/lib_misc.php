<?phpo
/**
* Набор вспомогательных ф-ций
* @package	deadsoul
* @author	Ужинский Борис email - boris.ujinsky@gmail.com
* @file		lib_misc.php
* @version	0.0.3
* @filesource
*/

function make_seed() {
	list($usec, $sec) = explode(' ', microtime());
	return (float) $sec + ((float) $usec * 100000);
}

function email_generate($fname, $lname, $year) {
	mt_srand(make_seed());
	$type = mt_rand(1,8);
	switch ($type) {
		case 1 : { $ret = $fname.".".$lname; break; }
		case 2 : { $ret = $fname."_".$lname; break; }
		case 3 : { $ret = $lname.".".$fname; break; }
		case 4 : { $ret = $lname."_".$fname; break; }
		case 5 : { $ret = $fname.".".$lname.$year; break; }
		case 6 : { $ret = $fname."_".$lname.$year; break; }
		case 7 : { $ret = $lname.".".$fname.$year; break; }
		case 8 : { $ret = $lname."_".$fname.$year; break; }
	}
	return $ret;
}

function get_gravatar( $email, $s = 128, $d = 'identicon', $r = 'g', $img = false, $atts = array() ) {
    $url = 'http://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
        $url = '<img src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}


// функции подсчета контрольной суммы для валидации СНИЛС-а
function checkSumm($s) {
	if ($s < 10) {
		return "0".$s;
	}
	if ($s < 100) {
		return $s;
	}
	if ($s === 100 || $s === 101) {
		return "00";
	}
	if ($s > 101) {
		return checkSumm($s % 101);
	}
}

function calcControlSummSnils($snils) {
	// Расчёт суммы
	$summ = 0;
	for ($i = 0; $i < 9; $i++) {
		$summ += (9 - $i) * ($snils[$i]);
	}
	return checkSumm($summ);
}

function num_spell($string, $num)
{
	switch ($num) {
		case 1: $ending = 'й'; break;
		case 2:
		case 3:
		case 4: $ending = 'я'; break;
		default : $ending = 'ев';
	}

	if ( ( $str >  9 ) && ( $str < 20 ) ) { $ending = 'ев'; }
	$string .= $ending;
	return $string;
}

/**
*Функция generate_password(void) - генерирует произвольные пароли
* @return string	пароль
*/
function generate_password() {
	return substr(md5(uniqid(rand(1,20),true)), rand(1,20),6 );
}

/**
*Функция uniq_id(void) - генерирует произвольное значение
* @return string	32-знаковая строка
*/
function uniq_id() {
	srand((double) microtime() * 1234567892524324);
	$id = md5( uniqid(rand( rand(0,93583746), rand(1,357409823) ), pow(getmypid(),rand(time(),getmyinode()) ) ) );
	return $id;
}


function _httprequest($url, $post=0, $params=""){
        $ch = curl_init(); // initialize curl handle
        curl_setopt($ch, CURLOPT_VERBOSE, 0); // set url to post to
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 40); // times out after 4s
        if ($post==1) {
        	curl_setopt($ch, CURLOPT_POSTFIELDS, urldecode(http_build_query($params))); // add POST fields
        	curl_setopt($ch, CURLOPT_POST, 1);
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch); // run the whole process
        if (empty($response)) {
                // some kind of an error happened
                die(curl_error($ch));
                curl_close($ch); // close cURL handler
        } else {
                $info = curl_getinfo($ch);
                curl_close($ch); // close cURL handler
                if (empty($info['http_code'])) {
                                die("No HTTP code was returned");
                } else {
                        $resp_body = $response;
                }
        }
        return $resp_body;
}


function date2timestamp($date){
	$t = preg_split("/ /", $date);
	$td = preg_split("/\./", $t[0]);
	$day = $td[0];
	$month = $td[1];
	$year = $td[2];
	$th = ($t[1]) ? preg_split("/:/", $t[1]) : preg_split("/:/", "00:00:00");
	$hour = $th[0];
	$minute = $th[1];
	$secund = $th[2];
	$timestamp = mktime ($hour, $minute, $secund, $month, $day , $year);
	return $timestamp;
}

function json_header(){
	header("HTTP/1.0 200 OK");
	header('Content-type: application/json; charset=utf-8');
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 2027 05:00:00 GMT");
	header("Pragma: no-cache");
}

function json_response($obj){
	return json_encode($obj);
}


function html_header(){
	header("HTTP/1.0 200 OK");
	header('Content-type: text/html; charset=utf-8');
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 2027 05:00:00 GMT");
	header("Pragma: no-cache");
}

function html_response($obj){
	echo "<table class=\"table table-condensed\">";
	echo $obj;
	echo "</table>";
}


function sql_encode($obj){
	$str = "insert into users(email, passwd, nickname, phone, city, full_name, gender, age, birthdate, avatar,socialID) values ";
	for ($c=0;$c<count($obj);$c++){


		$str .= " ('".$obj[$c]["email"]."', ";
		$str .= "'".$obj[$c]["password"]."', ";
		$str .= "'".$obj[$c]["nickname"]."', ";
		$str .= "'".$obj[$c]["phone"]."', ";
		$str .= "'".$obj[$c]["city"]."', ";
		$str .= "'".$obj[$c]["name"]["full"]."', ";
		$str .= "'".$obj[$c]["gender"]."' ";
		$str .= $obj[$c]["age"].", ";
		$str .= "'".$obj[$c]["birthdate"]."', ";
		$str .= "'".$obj[$c]["avatar"]."', ";
		$str .= "'".$obj[$c]["socialID"]."'),";
	}
	return substr($str, 0,-1);

}


function xml_header(){
	header("HTTP/1.0 200 OK");
	header('Content-Type: text/xml; charset=UTF-8');
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 2027 05:00:00 GMT");
	header("Pragma: no-cache");
}

function xml_response($obj, $type=""){
	$str = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
	if ($type!="")
		$str .= "<response type=\"".$type."\">\n";
	else
		$str .= "<response>\n";
	$str .= $obj;
	$str .= "</response>\n";
	return $str;
}

function xml_encode($obj){
	$str = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
	$str .= "<persons>\n";
	for ($c=0;$c<count($obj);$c++){
		$str .= "<person id='".$obj[$c]["md5"]."'>";
		$str .= "<name firstname='".$obj[$c]["name"]["first"]."' lastname='".$obj[$c]["name"]["last"]."'>".$obj[$c]["name"]["full"]."</name>";
		$str .= "<email>".$obj[$c]["email"]."</email>";
		$str .= "<nickname>".$obj[$c]["nickname"]."</nickname>";
		$str .= "<passwd>".$obj[$c]["password"]."</passwd>";
		$str .= "<birthdate>".$obj[$c]["birthdate"]."</birthdate>";
		$str .= "<age>".$obj[$c]["age"]."</age>";
		$str .= "<gender>".$obj[$c]["gender"]."</gender>";
		$str .= "<city>".$obj[$c]["city"]."</city>";
		$str .= "<phone>".$obj[$c]["phone"]."</phone>";
		$str .= "<avatar><![CDATA[ ".$obj[$c]["avatar"]." ]]></avatar>";
		$str .= "<socialID>".$obj[$c]["socialID"]."</socialID>";
		$str .= "</person>";
	}
	$str .= "</persons>\n";
	return $str;
}



/**
* Функция translate($string) - транслитирует кириллическую строчку
*
*/
function translate($in_string) {

	$string = mb_strtolower($in_string,'UTF-8');
	$string = preg_replace("/\"/","",$string);
	$string = preg_replace("/\?/","",$string);
	$string = preg_replace("/\#/","",$string);
	$string = preg_replace("/\+/","",$string);
	$string = preg_replace("/\*/","",$string);
	$string = preg_replace("/\./","",$string);

	$string = preg_replace("/[_\s\.,?!\[\](){}\/]+/","_",$string);

	$search = array ("'<script[^>]*?>.*?</script>'si",  // Strip out javascript
                 "'<[\/\!]*?[^<>]*?>'si",           // Strip out html tags
                 "'([\r\n])[\s]+'",                 // Strip out white space
                 "'&(quot|#34);'i",                 // Replace html entities
                 "'&(amp|#38);'i",
				 "'\&'i",
                 "'&(lt|#60);'i",
                 "'&(gt|#62);'i",
                 "'&(nbsp|#160);'i",
                 "'&(iexcl|#161);'i",
                 "'&(cent|#162);'i",
                 "'&(pound|#163);'i",
                 "'&(copy|#169);'i",
                 "'&#(\d+);'e");                    // evaluate as php

	$replace = array ("_",
                  "_",
                  "_",
                  "_",
                  "_",
				  "_",
                  "_",
                  "_",
                  "_",
                  "_",
                  "_",
                  "_",
                  "_",
                  "_");

	$string = preg_replace ($search, $replace, $string);
	$string = mb_strtolower($string,'UTF-8');
	$from = array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ы','ъ','э','ю','я','А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ь','Ы','Ъ','Э','Ю','Я');
	$to = array('a','b','v','g','d','e','yo','zh','z','i','j','k','l','m','n','o','p','r','s','t','u','f','kh','c','ch','sh','sch','','y','~','e','yu','ya','A','B','V','G','D','E','Yo','Zh','Z','I','J','K','L','M','N','O','P','R','S','T','U','F','Kh','C','Ch','Sh','Sch','','Y','~','E','Yu','Ya');
	for ($i=0; $i<strlen($string); $i++) {
		for ($ii=0; $ii < 65; $ii++) {
			$string = preg_replace("/".$from[$ii]."/",$to[$ii],$string);
			$string = str_replace(" ","_",$string);
		}
	}
	$string = preg_replace("/_{1,}/","_",$string);
	unset($from);
	unset($to);
	return $string;
}

function __autoload($class_name) {
        $filename = strtolower($class_name) . '.php';
        $file = ROOT.LIBPHP . '/classes/' . $filename;
        if (file_exists($file) == false) {
                return false;
        } else {
        	include ($file);
        }
}


/**
 * Error handler, passes flow over the exception logger with new ErrorException.
 */
function error_handler( $num, $str, $file, $line, $context = null )
{
    exception_handler( new ErrorException( $str, 0, $num, $file, $line ) );
}

/**
 * Uncaught exception handler.
 */
function exception_handler( Exception $e ) {
        print "<div style='text-align: center;'>";
        print "<h2 style='color: rgb(190, 50, 50);'>Exception Occured:</h2>";
        print "<table style='width: 800px; display: inline-block;'>";
        print "<tr style='background-color:rgb(230,230,230);'><th style='width: 80px;'>Type</th><td>" . get_class( $e ) . "</td></tr>";
        print "<tr style='background-color:rgb(240,240,240);'><th>Message</th><td>{$e->getMessage()}</td></tr>";
        print "<tr style='background-color:rgb(230,230,230);'><th>File</th><td>{$e->getFile()}</td></tr>";
        print "<tr style='background-color:rgb(240,240,240);'><th>Line</th><td>{$e->getLine()}</td></tr>";
        print "</table></div>";
//    exit();
}

/**
 * Checks for a fatal error, work around for set_error_handler not working on fatal errors.
 */
function fatal_handler()
{
    $error = error_get_last();
    if ( $error["type"] == E_ERROR )
        error_handler( $error["type"], $error["message"], $error["file"], $error["line"] );
}

register_shutdown_function("fatal_handler");
set_error_handler("error_handler");
set_exception_handler("exception_handler");

?>
