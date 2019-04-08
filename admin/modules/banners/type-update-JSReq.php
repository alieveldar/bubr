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
	$table="_banners_pos";
	$table2="_banners_list";
	
		
	// операции =========================================================
	if ($R["act"]=="DEL") {
		DB("DELETE FROM `".$table."` WHERE (`id`='".$item."')");
		DB("DELETE FROM `".$table2."` WHERE (`pid`='".$item."')");
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
	$data=DB("SELECT * FROM `".$table."` ORDER BY `rate` DESC"); $text="";
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; }
		$edit="ItemEdit( '".$ar["id"]."', '".$ar["name"]."', '".$ar["width"]."', '".$ar["height"]."', '".$ar["rotate"]."')";
		$text.='<tr class="TRLine'.($i%2).'" id="Line'.$i.'">';	
		$text.='<td class="CheckInput"><input type="checkbox" id="RS-'.$ar["id"].'-'.$table.'" '.$chk.'></td>';
		$text.="<td class='BigText'>".$ar["name"]." <i>ширина: ".$ar["width"].", высота: ".$ar["height"]."</i></td>";
		$text.='<td class="Act" align="center">Позиция: '.$ar['id'].'</td>';
		$text.='<td class="Act" align="center"><a href="?cat=banners_rotate&id='.$ar["id"].'" title="Изменить приоритеты"><b>Ротация</b></a>: '.$ar['rotate'].'</td>';		
		
		if ($i!=0) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$ar["id"].'\')" title="Поднять">'.AIco('3').'</a></td>'; } else { $text.='<td class="Act"></td>'; }
		if ($i<($data["total"]-1)) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$ar["id"].'\')" title="Опустить">'.AIco('4').'</a></td>'; } else { $text.='<td class="Act"></td>'; }
		
		$text.='<td class="Act" width="1%"><a href="javascript:void(0);" onclick="'.$edit.'" title="Править">'.AIco('28').'</a></td>';
		$text.='<td class="Act" width="1%"> </td>';
		$text.='<td class="Act" width="1%"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.="</tr>";
	endfor;  $AdminText.="<table>".$text."</table>";





	$result["content"]=$AdminText;
	$GLOBALS['_RESULT']	= $result;
}
?>