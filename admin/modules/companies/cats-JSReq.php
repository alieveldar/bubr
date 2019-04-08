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
	$ord=(int)$R["ord"];
	$pid=(int)$R["pid"];
	
	
	function GetChild($i, $lvl=-1) {
		global $itext, $items, $mvl; if ($i!=0) { $itext.=HtmlChild($lvl, $i); }
		foreach ($items as $key=>$item) { if ($item["pid"]==$items[$i]["id"]) { $pid=$item["pid"]; if ($mvl[$pid]==0) { $lvl++; $mvl[$pid]=1; } if ($key!=0) { GetChild($key, $lvl); }}}
	} 
	
	function HtmlChild($lvl, $idi) {
		global $items, $count, $id, $stotal, $prev, $table, $VARS; $pid=$items[$idi]["pid"]; $count++; if ($items[$idi]["stat"]==1) { $chk="checked"; }
		$spacer="<img src='/admin/images/icons/sp.png' style='width:".($lvl*15)."px;' class='spacer' />";
		$text='<tr class="TRLine'.($count%2).'" id="Line'.$idi.'"><td class="CheckInput"><input type="checkbox" id="RS-'.$idi.'-'.$table.'" '.$chk.'></td>';	
		$text.="<td class='BigText'>".$spacer."<a href='".str_replace("[mdomain]", $VARS["mdomain"], $items[$idi]["link"])."' target='_blank'>".trim($items[$idi]["name"])."</a> <i>".$items[$idi]["link"]."</i></td>";
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="AddNewMenu(\''.$idi.'\', \''.$table.'\', \'Добавить подраздел\')" title="Добавить подраздел">'.AIco('11').'</a></td>';
		if ($count!=1) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$idi.'\', \''.$table.'\', \''.$pid.'\')" title="Поднять">'.AIco('3').'</a></td>';
			} else { $text.='<td class="Act"></td>'; }
		if ($count<($stotal-1)) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$idi.'\', \''.$table.'\', \''.$pid.'\')" title="Опустить">'.AIco('4').'</a></td>';
			} else { $text.='<td class="Act"></td>'; }
		$edit="ItemEdit('".$idi."', '".trim($items[$idi]["name"])."', '".trim($items[$idi]["text"])."', '".trim($items[$idi]["pic"])."', '".$table."', 'Редактировать раздел/подраздел')";
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="'.$edit.'" title="Править">'.AIco('28').'</a></td>';
		$text.='<td class="Act">  </td>';
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$idi.'\', \''.$table.'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.="</tr>"; return $text;
	}

		
	// операции =========================================================
	
	if ($R["act"]=="DEL") {
		DB("DELETE FROM `".$table."` WHERE (`id`='".$item."' || `pid`='".$item."')");
	}
	
	// операции =========================================================
	
	if ($R["act"]=="UP") {
		$data=DB("SELECT id, rate FROM `".$table."` WHERE (`rate`<=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."')) AND `pid`='".$pid."') ORDER BY `rate` DESC LIMIT 2");
		$t="SELECT id, rate FROM `".$table."` WHERE (`rate`<=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."'))) ORDER BY `rate` DESC LIMIT 2";
		if ($data["total"]==2) { @mysql_data_seek($data["result"], 0); $a1=@mysql_fetch_array($data["result"]); @mysql_data_seek($data["result"], 1); $a2=@mysql_fetch_array($data["result"]);
		$res=DB("INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)");
		$t.="<hr>INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)".$data["total"]; }
	}	

		
	// операции =========================================================
	
	if ($R["act"]=="DOWN") {
		$data=DB("SELECT id, rate FROM `".$table."` WHERE (`rate`>=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."')) AND `pid`='".$pid."') ORDER BY `rate` ASC LIMIT 2");
		$t="SELECT id, rate FROM `".$table."` WHERE (`rate`>=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$item."'))) ORDER BY `rate` ASC LIMIT 2";
		if ($data["total"]==2) { @mysql_data_seek($data["result"], 0); $a1=@mysql_fetch_array($data["result"]); @mysql_data_seek($data["result"], 1); $a2=@mysql_fetch_array($data["result"]);
		$res=DB("INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)");
		$t.="<hr>INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`) ".$data["total"]; }
	}
	
	
	$data=DB("SELECT * FROM `".$table."` WHERE (type=1) ORDER BY `rate`");
	$items[0]["id"]=0; $items[0]["pid"]=-1; for ($i=1; $i<=$data["total"]; $i++): @mysql_data_seek($data["result"], ($i-1));
	$ar=@mysql_fetch_array($data["result"]); $idr=$ar["id"]; $items[$idr]["id"]=$ar["id"]; $items[$idr]["pid"]=$ar["pid"]; $items[$idr]["name"]=$ar["name"]; $items[$idr]["text"]=$ar["text"]; 
	$items[$idr]["pic"]=$ar["pic"]; $items[$idr]["link"]=$ar["link"]; $items[$idr]["stat"]=$ar["stat"]; $items[$idr]["class"]=$ar["class"]; endfor; $stotal=$data["total"]+1; 
	GetChild(0); $text.="<table>".$itext."</table>";

	
	$result["content"]=$text;
	$GLOBALS['_RESULT']	= $result;
}
?>