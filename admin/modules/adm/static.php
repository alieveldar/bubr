<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	$table2="_pages"; $items=array(); $itext=""; $id=1;
	
	
	$AdminText='<h2 style="float:left;">Статичные страницы сайта</h2><div style="float:right;" class="LinkG"><a href="?cat=adm_staticadd">Добавить новую страницу</a></div>'.$C5.$_SESSION["Msg"]; $pg=(int)$pg;	
	$AdminText.="<div id='Msg2' class='InfoDiv'>Вы можете менять порядок страниц, а так же изменять их текст и настройки</div>";

	// ДОЧЕРНИЕ ЭЛЕМЕНТЫ
	$data=DB("SELECT `id`,`pid`,`name`,`stat`,`data`,`link`,`isindex`, `rate` FROM `".$table2."` WHERE (`id`!='1' && `main`='0' && `module`='') ORDER BY `rate` DESC");
	$items[0]["id"]=1; $items[0]["pid"]=1; for ($i=1; $i<=$data["total"]; $i++): @mysql_data_seek($data["result"], ($i-1));
	$ar=@mysql_fetch_array($data["result"]); $idr=$ar["id"]; $items[$idr]["id"]=$ar["id"]; $items[$idr]["pid"]=$ar["pid"]; $items[$idr]["name"]=$ar["name"];
	$items[$idr]["link"]=$ar["link"]; $items[$idr]["stat"]=$ar["stat"]; $items[$idr]["isindex"]=$ar["isindex"]; endfor;
	$stotal=$data["total"]+1; GetChild(0); $AdminText.="<div class='RoundText' id='Tgg'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete()'>Удалить выбранные</a></div><table>".$itext."</table></div>";
	
	// ПРАВАЯ КОЛОНКА	
	$AdminRight="<div class='C20'></div>".ATextReplace('Static-Module')."<div class='C10'></div>";
}


// =================================================================================================================================================================================

function GetChild($i, $lvl=-1) {
	global $itext, $items, $mvl; if ($i!=0) { $itext.=HtmlChild($lvl, $i); }
	foreach ($items as $key=>$item) { if ($item["pid"]==$items[$i]["id"]) { $pid=$item["pid"]; if ($mvl[$pid]==0) { $lvl++; $mvl[$pid]=1; } if ($key!=0) { GetChild($key, $lvl); }}}
} 

function HtmlChild($lvl, $idi) {
	global $items, $count, $id, $stotal, $prev, $table2; $pid=$items[$idi]["pid"]; $count++; if ($items[$idi]["stat"]==1) { $chk="checked"; }
	if ($items[$idi]["isindex"]==1) { $isd=AIco('13','Главная страница сайта')." "; }
	$spacer="<img src='/admin/images/icons/sp.png' style='width:".($lvl*15)."px;' class='spacer' />";
	$text='<tr class="TRLine'.($count%2).'" id="Line'.$i.'"><td class="CheckInput"><input type="checkbox" id="RS-'.$idi.'-'.$table2.'" '.$chk.'></td>';	
	$text.="<td class='BigText'>".$spacer.$isd."<a href='/".$items[$idi]["link"]."' target='_blank'>".trim($items[$idi]["name"])."</a> <i>".$items[$idi]["link"]."</i></td>";
	$text.='<td class="Act"><a href="?cat=adm_staticadd&pid='.$idi.'">'.AIco('11','Добавить дочернюю страницу').'</a></td>';
	if ($count!=1) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$idi.'\', \''.$id.'\', \''.$pid.'\')" title="Поднять">'.AIco('3').'</a></td>'; } else { $text.='<td class="Act"></td>'; }
	if ($count<($stotal-1)) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$idi.'\', \''.$id.'\', \''.$pid.'\')" title="Опустить">'.AIco('4').'</a></td>'; } else { $text.='<td class="Act"></td>'; }
	$text.='<td class="Act"><a href="?cat=adm_staticedit&id='.$idi.'" title="Править">'.AIco('28').'</a></td>';
	$text.='<td class="Act"> </td>';
	$text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$idi.'\', \''.$id.'\', \''.$pid.'\')" title="Удалить">'.AIco('exit').'</a></td>';
	$text.='<td class="Act"><input type="checkbox" id="'.$idi.'" class="selectItem"></td>';
	$text.="</tr>"; return $text;
}

$_SESSION["Msg"]="";
?>