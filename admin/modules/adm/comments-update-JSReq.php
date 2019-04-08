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
	$item=(int)$R["id"];
	$items=$R["id"];
	$pg=$R["pg"];
	$table="_comments";
	$table2="_users";
	$table3="_commentf";
	$limit=50;
	$from=($pg - 1) * $limit;
	
		
	// операции =========================================================
	if ($R["act"]=="DEL") {
		$data = DB("SELECT `pic` FROM `".$table."` WHERE (`pid` IN (".$items."))");
		for ($i=0; $i<$data["total"]; $i++){
			@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); 
			foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { @unlink($ROOT."/userfiles/".$path."/".$ar['pic']); }
		}
		DB("DELETE FROM `".$table."` WHERE (`id` IN (".$items."))");
	}
	
	// операции =========================================================
	if ($R["act"]=="UP") {
		$data=DB("SELECT id, rate FROM `".$table."` WHERE (`rate`>=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."'))) ORDER BY `rate` ASC LIMIT 2");
		$t="SELECT id, rate FROM `".$table."` WHERE (`rate`>=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."'))) ORDER BY `rate` DESC LIMIT 2";
		if ($data["total"]==2) { @mysql_data_seek($data["result"], 0); $a1=@mysql_fetch_array($data["result"]); @mysql_data_seek($data["result"], 1); $a2=@mysql_fetch_array($data["result"]);
		$res=DB("INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)");
		$t.="<hr>INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)".$data["total"]; }
	}	
	
	// операции =========================================================
	if ($R["act"]=="DOWN") {
		$data=DB("SELECT id, rate FROM `".$table."` WHERE (`rate`<=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."'))) ORDER BY `rate` DESC LIMIT 2");
		$t="SELECT id, rate FROM `".$table."` WHERE (`rate`<=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."'))) ORDER BY `rate` ASC LIMIT 2";
		if ($data["total"]==2) { @mysql_data_seek($data["result"], 0); $a1=@mysql_fetch_array($data["result"]); @mysql_data_seek($data["result"], 1); $a2=@mysql_fetch_array($data["result"]);
		$res=DB("INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)");
		$t.="<hr>INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`) ".$data["total"]; }
	}
/*

	// отправляемые данные ==============================================
	$data=DB("SELECT `".$table."`.`id`, `".$table."`.`uid`, `".$table."`.`text`, `".$table."`.`ip`, `".$table."`.`data`, `".$table2."`.`nick` FROM `".$table."` LEFT JOIN `".$table2."` ON `".$table2."`.`id`=`".$table."`.`uid` ORDER BY `".$table."`.`data` DESC LIMIT $from, $limit"); $text="";
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]);
		$text.='<tr class="TRLine'.($i%2).'" id="Line'.$i.'">';	
		$text.="<td class='BigText' style='padding:10px 4px;'><p>".$d[4]." | <strong><a href='http://".$VARS["mdomain"]."/users/view/".$ar["uid"]."'>".$ar["nick"]."</a></strong> <i>".$ar["ip"]."</i></p>".$ar["text"]."</td>";
		$text.='<td class="Act" valign="top"><a href="javascript:void(0);" onclick="ActionAndUpdate('.$ar["id"].', \'DEL\', '.$pg.');" title="Удалить">'.AIco('exit').'</a></td>';
		$text.="</tr>";
	endfor; $AdminText.="<div class='RoundText' id='Tgg'><table>".$text."</table></div>";



*/

	$result["content"]="ok";
	$GLOBALS['_RESULT']	= $result;
}
?>