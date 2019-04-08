<?
session_start();
if ($_SESSION['userrole']>2) {
	$GLOBAL["sitekey"]=1;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Settings.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	$parent=(int)$R["pid"];
	$item=(int)$R["id"];
	$items=$R["id"];
	$table=$R["link"].'_photos';
	$table2=$R["link"].'_albums';
	$pic=$R["pic"];

		
	// операции =========================================================
	
	if ($R["act"]=="DEL") {
		$data = DB("SELECT `pic` FROM `".$table."` WHERE (`id` IN (".$items."))");
		for ($i=0; $i<$data["total"]; $i++){
			@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); 
			foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { @unlink($ROOT."/userfiles/".$path."/".$ar['pic']); }
		}
		DB("DELETE FROM `".$table."` WHERE (`id` IN (".$items."))");
	}
	
	// операции =========================================================
	
	if ($R["act"]=="FORM") {
		$data=DB("SELECT `".$table."`.*, `".$table2."`.`uid` AS `puid`, `users1`.`nick` AS `pnick`, `users2`.`nick` FROM `".$table."` LEFT JOIN `".$table2."` ON `".$table2."`.`id`=`".$table."`.`pid` LEFT JOIN `_users` AS `users1` ON `users1`.`id`=`".$table2."`.`uid` LEFT JOIN `_users` AS `users2` ON `users2`.`id`=`".$table."`.`uid` WHERE (`".$table."`.`id`='".$item."') GROUP BY 1");
		@mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_assoc($data["result"]);
		$data=ToRusData($ar["data"]);
		$ar["data"] = $data[5];
		if(!$ar["author"]) $ar["author"] = $ar["uid"] && $ar["uid"] != $ar["puid"] ? $ar["nick"] : $ar["pnick"];
		$result["d"]=$ar;
	}
	
	// операции =========================================================
	
	if ($R["act"]=="EDIT") {
		$ar=explode(".", $R['d']['data']); $sdata1=mktime(0, 0, 0, $ar[1], $ar[0], $ar[2]);
		if($R['d']['main'] == 'checked') DB("UPDATE `".$table."` SET `main`=0 WHERE (`pid`='".$parent."')");
		DB("UPDATE `".$table."` SET `name`='".$R['d']['name']."', `text`='".$R['d']['text']."', `uid`=".$_SESSION['userid'].", `author`='".$R['d']['author']."', `maps`='".$R['d']['maps']."', `data`='".$sdata1."', `main`=".($R['d']['main'] == 'checked' ? 1 : 0).", `winner`=".($R['d']['winner'] == 'checked' ? 1 : 0)." WHERE (`id`='".$item."')");		
	}
	
	// операции =========================================================
	
	if ($R["act"]=="DOWN") {
		$data=DB("SELECT id, rate FROM `".$table."` WHERE (`pid`='".$parent."' && `rate`>=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."'))) ORDER BY `rate` ASC LIMIT 2");
		$t="SELECT id, rate FROM `".$table."` WHERE (`pid`='".$parent."' && `rate`>=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."'))) ORDER BY `rate` DESC LIMIT 2";
		if ($data["total"]==2) { @mysql_data_seek($data["result"], 0); $a1=@mysql_fetch_array($data["result"]); @mysql_data_seek($data["result"], 1); $a2=@mysql_fetch_array($data["result"]);
		$res=DB("INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)");
		$t.="<hr>INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)".$data["total"]; }
	}	
	
	// операции =========================================================
	
	if ($R["act"]=="UP") {
		$data=DB("SELECT id, rate FROM `".$table."` WHERE (`pid`='".$parent."' && `rate`<=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."'))) ORDER BY `rate` DESC LIMIT 2");
		$t="SELECT id, rate FROM `".$table."` WHERE (`pid`='".$parent."' && `rate`<=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."'))) ORDER BY `rate` ASC LIMIT 2";
		if ($data["total"]==2) { @mysql_data_seek($data["result"], 0); $a1=@mysql_fetch_array($data["result"]); @mysql_data_seek($data["result"], 1); $a2=@mysql_fetch_array($data["result"]);
		$res=DB("INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)");
		$t.="<hr>INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`) ".$data["total"]; }
	}

	
	$result["content"]="ok";
	$GLOBALS['_RESULT']	= $result;
}
?>