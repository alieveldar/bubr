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
	$items_=explode(',', $R["id"]);
	$parent=(int)$R["pid"];
	$menuid=(int)$R["nid"];
		
	$table1="_menulist";
	$table2="_pages";
	
		
	// операции =========================================================
	
	if ($R["act"]=="DEL") {
		$items=array(); $dar=""; $data=DB("SELECT `id`,`pid` FROM `".$table2."`"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i);
		$ar=@mysql_fetch_array($data["result"]); $idr=$ar["id"]; $items[$idr]["id"]=$ar["id"]; $items[$idr]["pid"]=$ar["pid"]; endfor; $ptotal=$data["total"]; foreach ($items_ as $item) FindAllToDel($item);	
		DB("DELETE FROM `".$table2."` WHERE (`id` IN (".trim($dar, ",")."))");
	}
	
	// операции =========================================================
	
	if ($R["act"]=="UP") {
		$data=DB("SELECT id, rate FROM `".$table2."` WHERE (`pid`='".$parent."' && `rate`>=(SELECT `rate` FROM `".$table2."` WHERE (`id`='".$item."'))) ORDER BY `rate` ASC LIMIT 2");
		$t="SELECT id, rate FROM `".$table2."` WHERE (`pid`='".$parent."' && `rate`>=(SELECT `rate` FROM `".$table2."` WHERE (`id`='".$item."'))) ORDER BY `rate` DESC LIMIT 2";
		if ($data["total"]==2) { @mysql_data_seek($data["result"], 0); $a1=@mysql_fetch_array($data["result"]); @mysql_data_seek($data["result"], 1); $a2=@mysql_fetch_array($data["result"]);
		$res=DB("INSERT INTO `".$table2."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)");
		$t.="<hr>INSERT INTO `".$table2."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)".$data["total"]; }
	}	
	
	// операции =========================================================
	
	if ($R["act"]=="DOWN") {
		$data=DB("SELECT id, rate FROM `".$table2."` WHERE (`pid`='".$parent."' && `rate`<=(SELECT `rate` FROM `".$table2."` WHERE (`id`='".$item."'))) ORDER BY `rate` DESC LIMIT 2");
		$t="SELECT id, rate FROM `".$table2."` WHERE (`pid`='".$parent."' && `rate`<=(SELECT `rate` FROM `".$table2."` WHERE (`id`='".$item."'))) ORDER BY `rate` ASC LIMIT 2";
		if ($data["total"]==2) { @mysql_data_seek($data["result"], 0); $a1=@mysql_fetch_array($data["result"]); @mysql_data_seek($data["result"], 1); $a2=@mysql_fetch_array($data["result"]);
		$res=DB("INSERT INTO `".$table2."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)");
		$t.="<hr>INSERT INTO `".$table2."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`) ".$data["total"]; }
	}
	
	// отправляемые данные ==============================================
	
	$items=array();  $data=DB("SELECT `id`,`pid`,`name`,`stat`,`data`,`link`,`isindex`,`rate` FROM `".$table2."` WHERE (`id`!='1' && `main`='0' && `module`='') ORDER BY `rate` DESC");
	$items[0]["id"]=1; $items[0]["pid"]=1; for ($i=1; $i<=$data["total"]; $i++): @mysql_data_seek($data["result"], ($i-1));
	$ar=@mysql_fetch_array($data["result"]); $idr=$ar["id"]; $items[$idr]["id"]=$ar["id"]; $items[$idr]["pid"]=$ar["pid"]; $items[$idr]["name"]=$ar["name"];
	$items[$idr]["link"]=$ar["link"]; $items[$idr]["stat"]=$ar["stat"]; $items[$idr]["isindex"]=$ar["isindex"]; endfor;
	$stotal=$data["total"]+1; GetChild(0); $AdminText="<div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete()'>Удалить выбранные</a></div><table>".$itext."</table>";


	$result["content"]=$AdminText;
	$GLOBALS['_RESULT']	= $result;
}

// дополнительные функции ==============================================

function FindAllToDel($i) { global $dar, $items; $dar.=$i.","; foreach ($items as $key=>$item) { if ($item["pid"]==$items[$i]["id"]) { FindAllToDel($key); }}} 

function GetChild($i, $lvl=-1) {
	global $itext, $items, $mvl; if ($i!=0) { $itext.=HtmlChild($lvl, $i); }
	foreach ($items as $key=>$item) { if ($item["pid"]==$items[$i]["id"]) { $pid=$item["pid"]; if ($mvl[$pid]==0) { $lvl++; $mvl[$pid]=1; } if ($key!=0) { GetChild($key, $lvl); }}}
} 

function HtmlChild($lvl, $idi) {
	global $items, $count, $stotal, $prev, $table2; $id=1; $pid=$items[$idi]["pid"]; $count++; if ($items[$idi]["stat"]==1) { $chk="checked"; }
	if ($items[$idi]["isindex"]==1) { $isd=AIco('13','Главная страница сайта')." "; }
	$spacer="<img src='/admin/images/icons/sp.png' style='width:".($lvl*15)."px;' class='spacer' />";
	$text='<tr class="TRLine'.($count%2).'" id="Line'.$i.'"><td class="CheckInput"><input type="checkbox" id="RS-'.$idi.'-'.$table2.'" '.$chk.'></td>';	
	$text.="<td class='BigText'>".$spacer.$isd."<a href='/".$items[$idi]["link"]."' target='_blank'>".trim($items[$idi]["name"])."</a> <i>".$items[$idi]["link"]."</i></td>";
	$text.='<td class="Act"><a href="?cat=adm_staticadd&pid='.$idi.'">'.AIco('11','Добавить дочернюю страницу').'</a></td>';
	if ($count!=1) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$idi.'\', \''.$id.'\', \''.$pid.'\')" title="Поднять">'.AIco('3').'</a></td>'; } else { $text.='<td class="Act"></td>'; }
	if ($count<($stotal-1)) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$idi.'\', \''.$id.'\', \''.$pid.'\')" title="Опустить">'.AIco('4').'</a></td>'; } else { $text.='<td class="Act"></td>'; }
	$text.='<td class="Act"><a href="?cat=adm_staticedit&id='.$idi.'" title="Править">'.AIco('28').'</a></td>';
	$text.='<td class="Act">  </td>'; $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$idi.'\', \''.$id.'\', \''.$pid.'\')" title="Удалить">'.AIco('exit').'</a></td>'; $text.="</tr>"; return $text;
	$text.='<td class="Act"><input type="checkbox" id="'.$idi.'" class="selectItem"></td>';
}
?>