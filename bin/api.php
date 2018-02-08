<?php
require_once("../etc/init.php");
define("YEAR", 31536000);
define("MONTH",2628000);

$cm = new Cache(PACKAGE);
$cm->connect(MEM_HOST, MEM_PORT);

$valid_request = array("help", "debug", "format", "count","gender", "age_range", "country");


if (key_exists("help", $_REQUEST) && $_REQUEST["help"]==1) {
	echo "<h2>Короткая справка по использованию</h2>";
	echo "<ul>";
	echo "<li>/api/?gender=a|f|m &mdash; Указывает пол пользователей: a - любой; m - только мужчины; f - только женщины. По умолчанию выбираются пользователи обоих полов;</li>";
	echo "<li>/api/?age_range=minAGE-maxAGE &mdash; Указывает возростные ограничения в выборке. По умолчанию возрастной диапазон: 15-35лет;</li>";
	echo "<li>/api/?format=[json|xml|sql] &mdash; Выбор этого параметра позволяет указать на формат выходных данных. На данный момент доступны JSON, SQL, XML. По умолчанию данные отдаются в формате JSON;</li>";
	echo "<li>/api/?count=[N] &mdash; Этот параметр указывает, сколько именно пользователей вам необходимо. Верхий предел = 100. По умолчанию возвращается 1 запись.</li>";
	echo "<li>/api/?country=[ru] &mdash; Язык результата - ru или us. По умолчанию ru.</li>";
	echo "</ul>";
	echo "<p>Пример: <a href='http://dsoul.controlcash.ru/api/?count=5&age_range=22-30&gender=m' target='_blank'>На оптовую базу нужно 5 крепких парней. Данные вернуть в формате JSON</a></p>";
	echo "<p><a href='/'>На главную</a></p>";

} else {

	// обработка входящих параметров
	$debug = (key_exists("debug", $_REQUEST) && $_REQUEST["debug"]==1)?true:false;
	$sex = (key_exists("gender", $_REQUEST))?$_REQUEST["gender"]:"a";
	$format = (key_exists("format", $_REQUEST))?$_REQUEST["format"]:"json";
	$count = (key_exists("count", $_REQUEST))?(int)$_REQUEST["count"]:1;
	$age_range =  (key_exists("age_range", $_REQUEST) && preg_match("/^\d{1,2}-\d{1,2}$/", $_REQUEST["age_range"]))?explode("-", $_REQUEST["age_range"]):array(16,35);
	$domain = (key_exists("domain", $_REQUEST))?$_REQUEST["domain"]:"exmail.ru";
	$country = (key_exists("country", $_REQUEST))?$_REQUEST["country"]:"ru";

	sort($age_range);
	$age_range[0] = ($age_range[0]<16) ? 16:$age_range[0];
	$age_range[1] = ($age_range[0]>60) ? 60:$age_range[1];

	$count = ($count>100)?100:$count;
	$count = ($count<0 or $count =="")?1:$count;

	// подготовка массивов с именами, телефонами, городами
	$random_db = $cm->get("random_db_".$country);
	if (!$random_db) {
		$js = file_get_contents(ROOT."/etc/db_".$country.".json");
		$random_db = json_decode($js);
		$cm->set("random_db_".$country, $random_db, 30*60);
	}

	// Подсчет размеров полученных массивов
	$phc = count($random_db->phones_prefix);
	$fnmc = count($random_db->firstname_male);
	$lnmc = count($random_db->lastname_male);
	$fnfc = count($random_db->firstname_female);
	$lnfc = count($random_db->lastname_female);
	$ctc = count($random_db->cities);
	$cities = $random_db->cities;
	$phones_prefix = $random_db->phones_prefix;

	// основной цикл
	$persons = array();

	for ($c=0;$c<$count;$c++){
		mt_srand(make_seed());
		$age = mt_rand($age_range[0], $age_range[1]);
		$btime = time()-($age*YEAR+mt_rand(3600*20,YEAR));
		$bdate = date("d.m.Y", $btime);
		$year_suffix = date("y",$btime);
		$rtime = time() - MONTH*mt_rand(1,36);
		if ($sex=="a") {
			$s = mt_rand(0,1);
			$fn =($s==0)?$random_db->firstname_male:$random_db->firstname_female;
			$ln =($s==0)?$random_db->lastname_male:$random_db->lastname_female;
			$fn_cnt = ($s==0)?$fnmc:$fnfc;
			$ln_cnt = ($s==0)?$lnmc:$lnfc;
			$gender = ($s==0)?"male":"female";
		} else if ($sex=="f") {
			$fn = $random_db->firstname_female;
			$ln = $random_db->lastname_female;
			$fn_cnt = $fnfc;
			$ln_cnt = $lnfc;
			$gender = "female";
		} else if ($sex=="m") {
			$fn = $random_db->firstname_male;
			$ln = $random_db->lastname_male;
			$fn_cnt = $fnmc;
			$ln_cnt = $lnmc;
			$gender = "male";
		}
		$firstname = $fn[mt_rand(0,$fn_cnt-1)];
		$lastname = $ln[mt_rand(0,$ln_cnt-1)];

		$ln_tr = translate($lastname);
		$fn_tr = translate($firstname);

		$city = $cities[mt_rand(0,$ctc-1)];
		$pass = generate_password();

		if ($country=="ru") {
			$snils_a = array(mt_rand(100,999),mt_rand(100,999),mt_rand(100,999));
			$crc = calcControlSummSnils($snils_a[0].$snils_a[1].$snils_a[2]);
			$snils = implode("-", $snils_a)." ".$crc;
			$phone = "+7(".$phones_prefix[mt_rand(0,$phc-1)].")".mt_rand(1000000,9999999);
		} else {
			$snils = mt_rand(0,999)."-".mt_rand(0,99)."-".mt_rand(0,9999);
			$phone = "+1 ".$phones_prefix[mt_rand(0,$phc-1)]."-".mt_rand(200,299)."-".mt_rand(1000,9999);
			$domain = "exmail.com";
		}	

		$nick = email_generate($fn_tr, $ln_tr, $year_suffix);
		$email = $nick."@".$domain;

		$md5 = md5($age.$bdate.$gender.$lastname.$firstname.$city.$phone.$email.$nick.$pass.$snils);

		$pers = array(
			"age"=>$age,
			"birthdate" =>$bdate,
			"gender"=>$gender,
			"name"=>array(
				"last"=>$lastname,
				"first"=>$firstname,
				"full"=>$lastname." ".$firstname
			),
			"city"=>$city,
			"phone"=>$phone,
			"email"=>$email,
			"nickname"=>$nick,
			"password"=>$pass,
			"avatar"=>get_gravatar($email),
			"registred"=>$rtime,
			"socialID"=>$snils,
			"md5"=>$md5
		);
		$persons[]=$pers;

	}


	// вывод информации
	if ($debug) {
		echo "<pre><code>";print_r($persons);echo "</code></pre>";
	} else {
		switch ($format) {
			case "json" :  {
				$js = json_encode($persons);
				json_header();
				echo $js;
				break;
			}
			case "sql" : {
				$sql = sql_encode($persons);
				html_header();
				echo $sql;
				break;
			}
			case "xml" : {
				$xml = xml_encode($persons);
				xml_header();
				echo $xml;
				break;
			}
			default : {
				$js = json_encode($persons);
				json_header();
				echo $js;
				break;
			}
		}
	}
}
?>
