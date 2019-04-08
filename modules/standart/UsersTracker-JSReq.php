<?
session_start(); $dir=explode("/", $_SERVER['HTTP_REFERER']); $HTTPREFERER=$dir[2];
if ($HTTPREFERER==$_SERVER['HTTP_HOST']) {
	
	$GLOBAL["sitekey"]=1;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	//@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Settings.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	
	// полученные данные ================================================
	
	$R = $_REQUEST;
	$last=(int)$R["last"];
	$text="";
	$q="";
	if ((int)$_SESSION['userid']!=0) {
		### Сколько обновлений
		$res=DB("SELECT `pid`, `link` FROM `_tracker` WHERE (`uid`='".(int)$_SESSION['userid']."' && `stat`='1')");	
		$result["count"]=$res["total"];
	
		if ($result["count"]!=0 && $result["count"]!=$last) {
			for($i=0; $i<$res["total"]; $i++) { @mysql_data_seek($res["result"], $i); $tab=@mysql_fetch_array($res["result"]); $tbls[$tab["link"]][]=$tab["pid"]; }
			foreach($tbls as $link=>$pids) { $table=$link."_lenta"; $q.="(SELECT `$table`.`id`, `$table`.`data`, `$table`.`name`, `_pages`.`link`,  `_pages`.`name` as `rname` FROM `$table` LEFT JOIN `_pages` ON `_pages`.`link`='$link' WHERE (`$table`.`stat`='1' && `$table`.`id` IN (0, ".implode(",", $pids).")) GROUP BY 1) UNION "; }
			$q=trim($q, "UNION ")." ORDER BY `data` DESC LIMIT 5"; $data=DB($q); for($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $text.="<div class='C10'></div><u><a href='/".$ar["link"]."/view/".$ar["id"]."/'>".trim($ar["name"], ". ")."</a></u><i> / ".$ar["rname"]."</i>"; }
		}
		$result["reply"]=$text;
		$result["log"]="[- $last -] ";
	}
} else {
	$result["Text"]="--- Security alert ---";
	$result["Class"]="ErrorDiv";
	$result["Code"]=0;
	var_dump($result);
}

// отправляемые данные ==============================================
$GLOBALS['_RESULT']	= $result;	
?>