<?
session_start();
if ($_SESSION['userrole']>2) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	@require "../../../modules/standart/Settings.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	$table=$R["tab"]; $k=explode("_", $table); $link=$k[0];
	$item=(int)$R["id"];
	$items=$R["id"];
	$ord=(int)$R["ord"];
	

		
	// операции =========================================================
	
	if ($R["act"]=="DEL") {
		$data = DB("SELECT `pic` FROM `".$table."` WHERE (`id` IN (".$items."))");
		for ($i=0; $i<$data["total"]; $i++){
			@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); 
			foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { @unlink($ROOT."/userfiles/".$path."/".$ar['pic']); }
		}
		$data = DB("SELECT `pic` FROM `_widget_pics` WHERE (`pid` IN (".$items.") && `link`='".$link."')");
		for ($i=0; $i<$data["total"]; $i++){
			@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); 
			foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { @unlink($ROOT."/userfiles/".$path."/".$ar['pic']); }
		}
		DB("DELETE FROM `".$table."` WHERE (`id` IN (".$items."))");
		DB("DELETE FROM `_widget_pics` WHERE (`pid` IN (".$items.") && `link`='".$link."')");
		DB("DELETE FROM `_widget_voting` WHERE (`pid` IN (".$items.") && `link`='".$link."')");
		DB("DELETE FROM `_widget_video` WHERE (`pid` IN (".$items.") && `link`='".$link."')");
	}
	
	// операции =========================================================
	
	if (($R["act"]=="DOWN" && $ord==6) || ($R["act"]=="UP" && $ord==5)) {
		$data=DB("SELECT id, rate FROM `".$table."` WHERE (`rate`>=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."'))) ORDER BY `rate` ASC LIMIT 2");
		$t="SELECT id, rate FROM `".$table."` WHERE (`rate`>=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."'))) ORDER BY `rate` DESC LIMIT 2";
		if ($data["total"]==2) { @mysql_data_seek($data["result"], 0); $a1=@mysql_fetch_array($data["result"]); @mysql_data_seek($data["result"], 1); $a2=@mysql_fetch_array($data["result"]);
		$res=DB("INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)");
		$t.="<hr>INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)".$data["total"]; }
	}	

		
	// операции =========================================================
	
	if (($R["act"]=="UP" && $ord==6) || ($R["act"]=="DOWN" && $ord==5)) {
		$data=DB("SELECT id, rate FROM `".$table."` WHERE (`rate`<=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."'))) ORDER BY `rate` DESC LIMIT 2");
		$t="SELECT id, rate FROM `".$table."` WHERE (`rate`<=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."'))) ORDER BY `rate` ASC LIMIT 2";
		if ($data["total"]==2) { @mysql_data_seek($data["result"], 0); $a1=@mysql_fetch_array($data["result"]); @mysql_data_seek($data["result"], 1); $a2=@mysql_fetch_array($data["result"]);
		$res=DB("INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)");
		$t.="<hr>INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`) ".$data["total"]; }
	}


	
	$result["content"]="ok";
	$GLOBALS['_RESULT']	= $result;
}
?>