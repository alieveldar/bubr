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
	$parent=(int)$R["pid"];
	$menuid=(int)$R["nid"];
		
	$table1="_menulist";
	$table2="_menuitem";
	
		
	// операции =========================================================
	
	if ($R["act"]=="DEL") {
		$items=array(); $dar=""; $data=DB("SELECT `id`,`pid` FROM `".$table2."` WHERE (`nid`='".$menuid."')"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i);
		$ar=@mysql_fetch_array($data["result"]); $idr=$ar["id"]; $items[$idr]["id"]=$ar["id"]; $items[$idr]["pid"]=$ar["pid"]; endfor; $ptotal=$data["total"]; FindAllToDel($item);		
		DB("DELETE FROM `".$table2."` WHERE (`id` IN (".trim($dar, ",")."))");
	}
	
	// операции =========================================================
	
	if ($R["act"]=="UP") {
		$data=DB("SELECT id, rate FROM `".$table2."` WHERE (`nid`='".$menuid."' && `rate`>=(SELECT `rate` FROM `".$table2."` WHERE (`id`='".$item."'))) ORDER BY `rate` ASC LIMIT 2");
		$t="SELECT id, rate FROM `".$table2."` WHERE (`pid`='".$parent."' && `rate`>=(SELECT `rate` FROM `".$table2."` WHERE (`id`='".$item."'))) ORDER BY `rate` DESC LIMIT 2";
		if ($data["total"]==2) { @mysql_data_seek($data["result"], 0); $a1=@mysql_fetch_array($data["result"]); @mysql_data_seek($data["result"], 1); $a2=@mysql_fetch_array($data["result"]);
		$res=DB("INSERT INTO `".$table2."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)");
		$t.="<hr>INSERT INTO `".$table2."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)".$data["total"]; }
	}	
	
	// операции =========================================================
	
	if ($R["act"]=="DOWN") {
		$data=DB("SELECT id, rate FROM `".$table2."` WHERE (`nid`='".$menuid."' && `rate`<=(SELECT `rate` FROM `".$table2."` WHERE (`id`='".$item."'))) ORDER BY `rate` DESC LIMIT 2");
		$t="SELECT id, rate FROM `".$table2."` WHERE (`pid`='".$parent."' && `rate`<=(SELECT `rate` FROM `".$table2."` WHERE (`id`='".$item."'))) ORDER BY `rate` ASC LIMIT 2";
		if ($data["total"]==2) { @mysql_data_seek($data["result"], 0); $a1=@mysql_fetch_array($data["result"]); @mysql_data_seek($data["result"], 1); $a2=@mysql_fetch_array($data["result"]);
		$res=DB("INSERT INTO `".$table2."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)");
		$t.="<hr>INSERT INTO `".$table2."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`) ".$data["total"]; }
	}
	
	// отправляемые данные ==============================================

	$items=array(); $data=DB("SELECT * FROM `".$table2."` WHERE (`nid`='".$menuid."') ORDER BY `rate` DESC"); $items[0]["id"]=0; $items[0]["pid"]=-1; for ($i=1; $i<=$data["total"]; $i++): 
	@mysql_data_seek($data["result"], ($i-1)); $ar=@mysql_fetch_array($data["result"]); $idr=$ar["id"]; $items[$idr]["id"]=$ar["id"]; $items[$idr]["pid"]=$ar["pid"]; $items[$idr]["name"]=$ar["name"];
	$items[$idr]["link"]=$ar["link"];	$items[$idr]["stat"]=$ar["stat"]; $items[$idr]["class"]=$ar["class"]; endfor; $stotal=$data["total"]+1; GetChild(0); $AdminText="<table>".$itext."</table>";

	$result["content"]=$AdminText;
	//$result["log"]=$t;
	$GLOBALS['_RESULT']	= $result;
}

// дополнительные функции ==============================================

function FindAllToDel($i) { global $dar, $items; $dar.=$i.","; foreach ($items as $key=>$item) { if ($item["pid"]==$items[$i]["id"]) { FindAllToDel($key); }}} 

function GetChild($i, $lvl=-1) {
	global $itext, $items, $mvl; if ($i!=0) { $itext.=HtmlChild($lvl, $i); }
	foreach ($items as $key=>$item) { if ($item["pid"]==$items[$i]["id"]) { $pid=$item["pid"]; if ($mvl[$pid]==0) { $lvl++; $mvl[$pid]=1; } if ($key!=0) { GetChild($key, $lvl); }}}
} 

function HtmlChild($lvl, $idi) {
	global $items, $count, $menuid, $stotal, $prev, $table2; $pid=$items[$idi]["pid"]; $count++; if ($items[$idi]["stat"]==1) { $chk="checked"; }
	$spacer="<img src='/admin/images/icons/sp.png' style='width:".($lvl*15)."px;' class='spacer' />";
	$text='<tr class="TRLine'.($count%2).'" id="Line'.$i.'"><td class="CheckInput"><input type="checkbox" id="RS-'.$idi.'-'.$table2.'" '.$chk.' /></td>';
	$text.="<td class='BigText'>".$spacer."<a href='".$items[$idi]["link"]."' target='_blank'>".trim($items[$idi]["name"])."</a> <i>".$items[$idi]["link"]."</i></td>";
	$text.='<td class="Act"><a href="javascript:void(0);" onclick="AddNewMenu(\''.$menuid.'\', \''.$idi.'\')" title="Добавить подраздел">'.AIco('11').'</a></td>';
	if ($count!=1) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$idi.'\', \''.$menuid.'\', \''.$pid.'\')" title="Поднять">'.AIco('3').'</a></td>';
		} else { $text.='<td class="Act"></td>'; }
	if ($count<($stotal-1)) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$idi.'\', \''.$menuid.'\', \''.$pid.'\')" title="Опустить">'.AIco('4').'</a></td>';
		} else { $text.='<td class="Act"></td>'; }
	$edit="ItemEdit('".$idi."', '".$menuid."', '".$pid."', '".$items[$idi]["name"]."', '".$items[$idi]["link"]."', '".$items[$idi]["class"]."')";
	$text.='<td class="Act"><a href="javascript:void(0);" onclick="'.$edit.'"" title="Править">'.AIco('28').'</a></td>';
	$text.='<td class="Act">  </td><td class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$idi.'\', \''.$menuid.'\', \''.$pid.'\')" title="Удалить">'.AIco('exit').'</a></td>';
	$text.="</tr>"; return $text;
}
?>