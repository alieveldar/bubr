<?
session_start(); $dir=explode("/", $_SERVER['HTTP_REFERER']); $HTTPREFERER=$dir[2];
if ($HTTPREFERER==$_SERVER['SERVER_NAME']) {
	
	$GLOBAL["sitekey"]=1;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Cache.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Settings.php";	
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";	
	$JsHttpRequest=new JsHttpRequest("utf-8");
	
	if (!isset($dir[4]) || $dir[4]=="") $dir[4] = 0;	
	
	// полученные данные ================================================
	
	$R = $_REQUEST;
	$vid = $R["vid"];
	$pid = $R["pid"];
	$link = $R["link"];
	
	$table = $link."_lenta";
	$table2 = '_widget_pics';
	$table3 = '_widget_votes';	
	
	$file=$table."-".$dir[2].".".$pid.".".$dir[4];
	
	// операции =========================================================
	$result = array();
	$or=$_SESSION['userid']?"OR `uid`=".$_SESSION['userid']:"";
	$q=DB("SELECT `id` FROM `$table3` WHERE ((`ip`='".$GLOBAL["ip"]."' $or) AND `pid`=$pid AND `link`='$link' AND `data`>".$GLOBAL["tonight"].")");
	$q2=DB("SELECT `voting` FROM `".$table."` WHERE (`id`=".$pid.") LIMIT 1");
	@mysql_data_seek($q2["result"], 0); $node=@mysql_fetch_array($q2["result"]);
	
	if ($q["total"]==1) {
		$result["Text"]="Вы уже голосовали сегодня в данном материале";
		$result["Code"]=0;
	} elseif ($node["voting"]==1 && $_SESSION['userid'] == 0) {
		$result["Text"]="Голосовать могут только зарегистрированные пользователи";
		$result["Code"]=0;
	} else {
		DB("INSERT INTO `$table3` (`link`,`vid`,`uid`,`data`,`ip`,`pid`) VALUES ('$link', $vid, ".$_SESSION['userid'].", '".$GLOBAL["now"]."', '".$GLOBAL["ip"]."', '$pid')");
		
		$data=DB("SELECT `".$table2."`.`id`, COUNT(`".$table3."`.`id`) as `cnt` FROM `".$table2."` LEFT JOIN `".$table3."` ON `".$table3."`.`vid`=`".$table2."`.`id` WHERE (`".$table2."`.`link`='".$link."' AND `".$table2."`.`pid`=".$pid." AND `".$table2."`.`stat`=1) GROUP BY 1");
		$result["Votes"] = array();
		for ($i=0; $i<$data["total"]; $i++){
			@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
			$result["Votes"][] = $ar["cnt"];
		}
		
		ClearCache($file);
		$result["Text"] = 'Спасибо! Ваш голос отправлен в систему учета!';
		$result["Code"]=1;	
	}
} else {
	$result["Text"]="--- Security alert ---";
	$result["Class"]="ErrorDiv";
	$result["Code"]=0;
}



// отправляемые данные ==============================================
$GLOBALS['_RESULT']	= $result;	
?>