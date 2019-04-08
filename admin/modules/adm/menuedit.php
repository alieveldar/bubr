<?
### МЕНЮ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

$table1="_menulist";
$table2="_menuitem";
$items=array();

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;

	if (isset($P["savebutton"])) {
		$res=DB("UPDATE `".$table1."` SET `name`='".DBcut($P["Inp0"])."', `link`='".DBcut($P["Inn0"])."', `stat`='".(int)DBcut($P["Inc0"])."' WHERE (`id`='".(int)$id."') LIMIT 1");
		$_SESSION["Msg"]="<div class='SuccessDiv'>Настройки сохранены</div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

// ДАННЫЕ РОДИТЕЛЯ
$data=DB("SELECT * FROM `".$table1."` WHERE (id='".(int)$id."')"); @mysql_data_seek($data["result"], 0); $menu=@mysql_fetch_array($data["result"]);
if ((int)$menu["id"]==0) { $AdminText=ATextReplace('Item-Module-Error', $id, $table1); $GLOBAL["error"]=1; } else { if ($menu["stat"]==1) { $chk="checked"; }

	$AdminText='<h2>Навигация сайта: '.$menu["name"].'</h2>'.$_SESSION["Msg"];
	
	// ФОРМА РЕДАКТИРОВАНИЯ
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";
	$AdminText.="<div class='RoundText'><table><tr class='TRLineC'><td>Переменная</td><td>Название раздела меню</td><td>Включено</td></tr>";
	$AdminText.='<tr class="TRLine" id="Line0"><td class="SmallInput"><input name="Inn0" type="text" class="JsVerify" value="'.$menu["link"].'"></td>
	<td class="LongInput"><input name="Inp0" type="text" value="'.$menu["name"].'"></td><td class="CheckInput"><input type="checkbox" id="Inc0" name="Inc0" '.$chk.' value="1" /></td>
	</tr>'."</table>".$C5."<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить'></div></div></form>";	
	### Существующие занятые имена переменных
	$data=DB("SELECT `link` FROM `".$table1."` WHERE (id!='".(int)$id."')"); $AdminText.="<script type='text/javascript'>var NotAvaliable=new Array("; for ($i=0; $i<$data["total"]; $i++):
	@mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $AdminText.="'".$ar["link"]."',"; endfor; $AdminText.="'error');</script>";

	// ДОЧЕРНИЕ ЭЛЕМЕНТЫ
	$AdminText.=$C15.'<h2 style="float:left;">Редактирование списка элементов меню</h2>
	<div style="float:right;" class="LinkG"><a href="javascript:void(0);" onclick="AddNewMenu(\''.(int)$id.'\', \'0\');">Добавить ссылку в меню</a></div>'."$C5
	<div id='Msg2' class='InfoDiv'>Вы можете менять порядок пунктов меню, а так же изменять их текст и ссылки</div>";
	$data=DB("SELECT * FROM `".$table2."` WHERE (`nid`='".$id."') ORDER BY `rate` DESC");
	$items[0]["id"]=0; $items[0]["pid"]=-1; for ($i=1; $i<=$data["total"]; $i++): @mysql_data_seek($data["result"], ($i-1));
	$ar=@mysql_fetch_array($data["result"]); $idr=$ar["id"]; $items[$idr]["id"]=$ar["id"]; $items[$idr]["pid"]=$ar["pid"]; $items[$idr]["name"]=$ar["name"];
	$items[$idr]["link"]=$ar["link"]; $items[$idr]["stat"]=$ar["stat"]; $items[$idr]["class"]=$ar["class"]; endfor; $stotal=$data["total"]+1; 
	GetChild(0); $AdminText.="<div class='RoundText' id='Tgg'><table>".$itext."</table></div>";
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight=ATextReplace('Menu-Edit-Module', $menu["link"]).$C30."<div class='LinkR'><a href='javascript:void(0);' onclick='LinkBlank(\"Удалить рубрикатор?\",\"Удалить безвозвратно элемент навигации <b>&laquo;".$menu["name"]."&raquo;</b> и все его дочерние пункты меню?\", \"?cat=adm_menudel&id=".$id."\")'> Удалить этот раздел меню </a></div>";
	
}}

//=============================================

function GetChild($i, $lvl=-1) {
	global $itext, $items, $mvl; if ($i!=0) { $itext.=HtmlChild($lvl, $i); }
	foreach ($items as $key=>$item) { if ($item["pid"]==$items[$i]["id"]) { $pid=$item["pid"]; if ($mvl[$pid]==0) { $lvl++; $mvl[$pid]=1; } if ($key!=0) { GetChild($key, $lvl); }}}
} 

function HtmlChild($lvl, $idi) {
	global $items, $count, $id, $stotal, $prev, $table2, $VARS; $pid=$items[$idi]["pid"]; $count++; if ($items[$idi]["stat"]==1) { $chk="checked"; }
	$spacer="<img src='/admin/images/icons/sp.png' style='width:".($lvl*15)."px;' class='spacer' />";
	$text='<tr class="TRLine'.($count%2).'" id="Line'.$i.'"><td class="CheckInput"><input type="checkbox" id="RS-'.$idi.'-'.$table2.'" '.$chk.'></td>';	
	$text.="<td class='BigText'>".$spacer."<a href='".str_replace("[mdomain]", $VARS["mdomain"], $items[$idi]["link"])."' target='_blank'>".trim($items[$idi]["name"])."</a> <i>".$items[$idi]["link"]."</i></td>";
	$text.='<td class="Act"><a href="javascript:void(0);" onclick="AddNewMenu(\''.$id.'\', \''.$idi.'\')" title="Добавить подраздел">'.AIco('11').'</a></td>';
	if ($count!=1) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$idi.'\', \''.$id.'\', \''.$pid.'\')" title="Поднять">'.AIco('3').'</a></td>';
		} else { $text.='<td class="Act"></td>'; }
	if ($count<($stotal-1)) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$idi.'\', \''.$id.'\', \''.$pid.'\')" title="Опустить">'.AIco('4').'</a></td>';
		} else { $text.='<td class="Act"></td>'; }
	$edit="ItemEdit('".$idi."', '".$id."', '".$pid."', '".$items[$idi]["name"]."', '".$items[$idi]["link"]."', '".$items[$idi]["class"]."')";
	$text.='<td class="Act"><a href="javascript:void(0);" onclick="'.$edit.'" title="Править">'.AIco('28').'</a></td>';
	$text.='<td class="Act">  </td>';
	$text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$idi.'\', \''.$id.'\', \''.$pid.'\')" title="Удалить">'.AIco('exit').'</a></td>';
	$text.="</tr>"; return $text;
}
$_SESSION["Msg"]="";
?>