<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);

	global $pg;
	$table=$alias."_items";
	$table2=$alias."_actions";
	
	// ЭЛЕМЕНТЫ
	$data=DB("SELECT name, orderby, onpage FROM `_pages` WHERE (`link`='".$alias."')"); @mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);
	
	$data=DB("SELECT `name`, `stat` FROM `$table` WHERE (`id`=".(int)$id.") LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
	
	### Заполнение данных
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);
	if ($node["stat"]==1) { $chk="checked"; }
	
	$AdminText='<h2 style="float:left;">Акции: &laquo<span class="companyName">'.$node["name"].'</span>&raquo;</h2><div class="LinkG" style="float:right;"><a href="?cat='.$alias.'_actionadd&id='.$id.'">Добавить акцию</a></div>'.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете редактировать и удалять акции</div>";
	$data=DB("SELECT * FROM `".$table2."` WHERE (pid=".$id.")"); $text="";
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk1="checked"; } else { $chk1=""; } $datan=ToRusData($ar["todata"]);		
		$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';			
		$text.='<td class="CheckInput"><input type="checkbox" id="RS-'.$ar["id"].'-'.$table2.'" '.$chk1.'></td>';		
		$text.="<td class='BigText'><a href='/".$alias."/action/view/".$ar["id"]."' target='_blank'>".$ar["name"]."</a> <i>$ar[catn]</i></td>";	
		$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:10px;" ><i>'.$datan[5].'</i></td>';
		$text.='<td class="Act"><a href="?cat='.$alias.'_actionedit&id='.$ar["id"].'" title="Править">'.AIco('28').'</a></td>';		
		$text.='<td class="Act" id="Act'.$ar["id"].'"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$table2.'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.='<td class="Act"><input type="checkbox" id="'.$ar["id"].'" class="selectItem"></td>';
		$text.="</tr>";
	endfor;
	
	if($text) $AdminText.="<div class='RoundText' id='Tgg'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete(\"".$table2."\")'>Удалить выбранные</a></div><table>".$text."</table></div>";
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_contacts&id=".$id."'>Контакты и часы работы</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_pics&id=".$id."'>Фотографии компании</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_consults&id=".$id."'>Консультации</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_actions&id=".$id."'>Акции</a></div>
	$C5<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$table."' ".$chk."></td><td><b>Материал опубликован</b></td></tr></table></div>";
	}
	}
}

//=============================================
$_SESSION["Msg"]="";
?>