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
	
	$file='agregator-'.$link;
	
	// операции =========================================================
	$result = array();
	$data=DB("SELECT `voting`, `votingmode`, `votingend` FROM `".$table."` WHERE (`id`=".$pid.") LIMIT 1");
	@mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]);
	$where=$_SESSION['userid']?"`uid`=".$_SESSION['userid']:"`ip`='".$GLOBAL["ip"]."'";
	if($GLOBAL["now"] < $item["votingend"] && $item["votingmode"] == 1) $q=DB("SELECT `id` FROM `$table3` WHERE ($where AND `pid`=".$pid." AND `link`='$link' AND `data`>".$GLOBAL["tonight"].")");	
	
	if ($q["total"]) {
		$result["Text"]="Вы уже голосовали сегодня в данном материале";
		$result["Code"]=0;
	} elseif ($item["voting"]==1 && !$_SESSION['userid']) {
		$result["Text"]="Голосовать могут только зарегистрированные пользователи";
		$result["Code"]=0;
	} else if($GLOBAL["now"] < $item["votingend"]) {
		DB("INSERT INTO `$table3` (`link`,`vid`,`uid`,`data`,`ip`,`pid`) VALUES ('$link', $vid, ".$_SESSION['userid'].", '".$GLOBAL["now"]."', '".$GLOBAL["ip"]."', '$pid')");
		
		$data=DB("SELECT `".$table2."`.`id`, COUNT(`".$table3."`.`id`) as `cnt` FROM `".$table2."` LEFT JOIN `".$table3."` ON `".$table3."`.`vid`=`".$table2."`.`id` WHERE (`".$table2."`.`link`='".$link."' AND `".$table2."`.`pid`=".$pid." AND `".$table2."`.`stat`=1) GROUP BY 1 ORDER BY `".$table2."`.`rate` ASC");
		$result["Votes"] = array();
		for ($i=0; $i<$data["total"]; $i++){
			@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
			$result["Votes"][] = $ar["cnt"];
		}
		
		ClearCache($file);
		$result["Text"] = '<p align=center><br>Спасибо! Ваш голос отправлен в систему учета.<br><br><a href=\'/'.$link.'/view/'.$pid.'/\'><u>Перейти к комментариям</u>!</a><br><br></p>';
		$result["Code"]=1;
		if($item["votingmode"] == 1) $result["btnRemove"]=true;	
	}
} else {
	$result["Text"]="--- Security alert ---";
	$result["Class"]="ErrorDiv";
	$result["Code"]=0;
}



// отправляемые данные ==============================================
$GLOBALS['_RESULT']	= $result;	
?>