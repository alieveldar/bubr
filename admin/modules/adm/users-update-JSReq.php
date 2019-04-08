<?
session_start();
if ($_SESSION['userrole']>2) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	//@require "../../../modules/standart/Settings.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	$item=(int)$R["id"];
	$items=$R["id"];
	$pg=$R["pg"];
	$table="_users";
	$limit=50;
	$from=($pg - 1) * $limit;
	
		
	// операции =========================================================
	if ($R["act"]=="DEL") {
		$data = DB("SELECT `avatar` FROM `".$table."` WHERE (`id` IN (".$items."))");
		for ($i=0; $i<$data["total"]; $i++){
			@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); 
			@unlink($ROOT."/userfiles/avatar/".$ar['pic']);
			@unlink($ROOT."/sites/default/files/avatar/".$ar['pic']);
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


	// отправляемые данные ==============================================
	$data=DB("SELECT * FROM `".$table."` ORDER BY `nick` ASC LIMIT $from, $limit"); $text="";
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; }

		$lasttime=$ar["lasttime"] ? date("d.m.Y H:i:s", $ar["lasttime"]) : 'Не производился';
        $avatar=$ar["avatar"] ? "/".$ar["avatar"] : "/userfiles/avatar/no_photo.png";
		$info='ItemInfo('.$ar["id"].', \''.$ar["ip"].'\', \''.$GLOBAL["roles"][$ar["role"]].'\', \''.$ar["login"].'\', \''.$ar["vkontakte"].'\', \''.$ar["mailru"].'\', \''.$ar["twitter"].'\', \''.$ar["facebook"].'\', \''.$ar["odnoklas"].'\', \''.$ar["google"].'\', \''.$ar["yandex"].'\', \''.$ar["mail"].'\', \''.$ar["spectitle"].'\', \''.$ar["signature"].'\', \''.date("d.m.Y H:i:s", $ar["created"]).'\', \''.$lasttime.'\', \''.$avatar.'\');';
		$edit="ItemEdit( '".$ar["id"]."', '".$ar["login"]."', '".$ar["link"]."', '".$ar["runtime"]."')";

		$text.='<tr class="TRLine'.($i%2).'" id="Line'.$i.'">';			
		$text.='<td class="CheckInput"><input type="checkbox" id="RS-'.$ar["id"].'-'.$table.'" '.$chk.'></td>';		
		$text.="<td class='BigText'><a href='/users/view/".$ar["id"]."' target='_blank'>".$ar["nick"]."</a> <i>".$ar["mail"]."</i></td>";
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="'.$info.'" title="Информация">'.AIco('49').'</a></td>';		
		$text.='<td class="Act"><a href="?cat=adm_usersedit&id='.$ar["id"].'" title="Править">'.AIco('28').'</a></td>';
		$text.='<td class="Act"> </td>';
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$pg.'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.="</tr>";
	endfor; $AdminText.="<table>".$text."</table>";





	$result["content"]="ok";
	$GLOBALS['_RESULT']	= $result;
}
?>