<?
### МЕНЮ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

$table=$alias."_cats";
$items=array();

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	
	if (isset($P["addbutton"])) {
		$stat = $P["stat"] == 'on' ? 1 : 0;
		if (isset($_FILES["photo"]["name"]) && $_FILES["photo"]["name"]!="") @require("modules/UploadPhoto.php");
		$res=DB("INSERT INTO $table (`stat`,`name`,`text`,`pic`,`pid`,`type`) VALUES (".$stat.", '".$P["name"]."', '".$P["text"]."', '".$picname."', ".$P["pid"].", 2)");
		$last=DBL(); DB("UPDATE $table SET `rate`='".$last."' WHERE  (id='".$last."')");
		$_SESSION["Msg"]="<div class='C20'></div><div class='SuccessDiv'>Данные успешно добавлены!</div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

	if (isset($P["savebutton"])) {
		if (isset($_FILES["photo"]["name"]) && $_FILES["photo"]["name"]!="") {
			if ($P["pic"]!="") { foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { @unlink("../userfiles/".$path."/".$P["pic"]); }}
			@require("modules/UploadPhoto.php");
		}
		else $picname = $P["pic"];
		$res=DB("UPDATE `".$table."` SET `name`='".$P["name"]."', `text`='".$P["text"]."', `pic`='".$picname."' WHERE (`id`='".(int)$P["id"]."')");
		$_SESSION["Msg"]="<div class='SuccessDiv'>Настройки сохранены</div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}
	
	$AdminText.=$C15.'<h2 style="float:left;">Редактирование разделов и категорий для консультаций</h2>
	<div style="float:right;" class="LinkG"><a href="javascript:void(0);" onclick="AddNewMenu(\'0\', \''.$table.'\', \'Добавить раздел\');">Добавить раздел</a></div>'."$C5
	<div id='Msg2' class='InfoDiv'>Вы можете менять порядок пунктов меню, а так же изменять их текст и ссылки</div>";
	$data=DB("SELECT * FROM `".$table."` WHERE (type=2) ORDER BY `rate`");
	$items[0]["id"]=0; $items[0]["pid"]=-1; for ($i=1; $i<=$data["total"]; $i++): @mysql_data_seek($data["result"], ($i-1));
	$ar=@mysql_fetch_array($data["result"]); $idr=$ar["id"]; $items[$idr]["id"]=$ar["id"]; $items[$idr]["pid"]=$ar["pid"]; $items[$idr]["name"]=$ar["name"]; $items[$idr]["text"]=$ar["text"]; 
	$items[$idr]["pic"]=$ar["pic"]; $items[$idr]["stat"]=$ar["stat"]; $items[$idr]["link"]="/".$alias."/consults/".$ar["id"]."/"; endfor; $stotal=$data["total"]+1; 
	GetChild_(0); $AdminText.="<div class='RoundText' id='Tgg'><table>".$itext."</table></div>";
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="";
	
}

//=============================================

function GetChild_($i, $lvl=-1) {
	global $itext, $items, $mvl; if ($i!=0) { $itext.=HtmlChild_($lvl, $i); }
	foreach ($items as $key=>$item) { if ($item["pid"]==$items[$i]["id"]) { $pid=$item["pid"]; if ($mvl[$pid]==0) { $lvl++; $mvl[$pid]=1; } if ($key!=0) { GetChild_($key, $lvl); }}}
} 

function HtmlChild_($lvl, $idi) {
	global $items, $count, $id, $stotal, $prev, $table, $alias, $VARS; $pid=$items[$idi]["pid"]; $count++; if ($items[$idi]["stat"]==1) { $chk="checked"; }
	$spacer="<img src='/admin/images/icons/sp.png' style='width:".($lvl*15)."px;' class='spacer' />";
	$text='<tr class="TRLine'.($count%2).'" id="Line'.$idi.'"><td class="CheckInput"><input type="checkbox" id="RS-'.$idi.'-'.$table.'" '.$chk.'></td>';	
	if($lvl == 0) $text.="<td class='BigText'>".$spacer.trim($items[$idi]["name"])."</td>";
	else $text.="<td class='BigText'>".$spacer."<a href='".str_replace("[mdomain]", $VARS["mdomain"], $items[$idi]["link"])."' target='_blank'>".trim($items[$idi]["name"])."</a> <i>".$items[$idi]["link"]."</i></td>";
	$text.='<td class="Act"><a href="javascript:void(0);" onclick="AddNewMenu(\''.$idi.'\', \''.$table.'\', \'Добавить подраздел\')" title="Добавить подраздел">'.AIco('11').'</a></td>';
	if ($count!=1) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$idi.'\', \''.$table.'\', \''.$pid.'\')" title="Поднять">'.AIco('3').'</a></td>';
		} else { $text.='<td class="Act"></td>'; }
	if ($count<($stotal-1)) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$idi.'\', \''.$table.'\', \''.$pid.'\')" title="Опустить">'.AIco('4').'</a></td>';
		} else { $text.='<td class="Act"></td>'; }
	$edit="ItemEdit('".$idi."', '".trim($items[$idi]["name"])."', '".trim($items[$idi]["text"])."', '".trim($items[$idi]["pic"])."', '".$table."', 'Редактировать раздел/подраздел')";
	$text.='<td class="Act"><a href="javascript:void(0);" onclick="'.$edit.'" title="Править">'.AIco('28').'</a></td>';
	if($lvl == 0 || $_SESSION['userrole']<2) $text.='<td class="Act"></td>';
	else $text.='<td class="Act"><a href="?cat='.$alias.'_qa&id='.$idi.'" title="Вопросы и ответы">'.AIco('49').'</a></td>';
	$text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$idi.'\', \''.$table.'\')" title="Удалить">'.AIco('exit').'</a></td>';
	$text.="</tr>"; return $text;
}
$_SESSION["Msg"]="";
?>