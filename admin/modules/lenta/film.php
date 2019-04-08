<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	$table=$alias."_lenta"; $table2="_widget_video";

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {	
		
		if ($P["vtext"]!="") {
			$data=DB("SELECT id FROM `$table2` WHERE (`pid`=".(int)$id." AND `link`='".$alias."') LIMIT 1");
			if ($data["total"]==0) { DB("INSERT INTO `$table2` (`pid`, `link`, `name`, `text`) VALUES (".(int)$id.", '".$alias."', '".$P["vname"]."', '".$P["vtext"]."')");
			} else { DB("UPDATE `$table2` SET `name`='".$P["vname"]."', `text`='".$P["vtext"]."' WHERE (`pid`=".(int)$id." AND `link`='".$alias."') LIMIT 1 "); }
			$msg="<div class='SuccessDiv'>Запись успешно сохранена!</div>";
		} else {
			DB("DELETE FROM `$table2` WHERE (`pid`=".(int)$id." AND `link`='".$alias."') LIMIT 1");
			$msg="<div class='ErrorDiv'>Запись успешно удалена!</div>";
		}
		
		$_SESSION["Msg"]=$msg; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}


	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT `name`, `stat` FROM `$table` WHERE (`id`=".(int)$id.") LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
		
	### Заполнение данных
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);  if ($node["stat"]==1) { $chk="checked"; }
	
	$v=DB("SELECT `id`, `name`, `text` FROM `$table2` WHERE (`pid`=".(int)$id." AND `link`='".$alias."') LIMIT 1");
	@mysql_data_seek($v["result"], 0); $vid=@mysql_fetch_array($v["result"]);
		
	$AdminText='<h2>Видео: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.='<form action="'.$_SERVER["REQUEST_URI"].'" enctype="multipart/form-data" method="post">';

	### Основные данные
	$AdminText.='<div class="RoundText"><table><tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Название</td><td class="LongInput"><input name="vname" type="text" value="'.$vid["name"].'"></td><tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Коды видео</td><td class="LongInput"><textarea style="height:400px;" name="vtext">'.$vid["text"].'</textarea></td><tr>';
	$AdminText.='</table></div>';

	### Сохранение
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";

	// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_lenta' ".$chk."></td><td><b>Опубликовано</b></td></tr></table></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_photo&id=".$id."'>Основная фотография</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_report&id=".$id."'>Виджет: Фото-отчет</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_album&id=".$id."'>Виджет: Фото-альбом</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_film&id=".$id."'>Виджет: Видео-вставка</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_voting&id=".$id."'>Виджет: Голосование</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_pretext&id=".$id."'>Виджет: Постскриптум</a></div>
	<br><div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div><br><br>
	<div class='SecondMenu2'><a href='/$alias/view/$id/' target='_blank'>Просмотр на сайте</a></div></form>";
	if ($_SESSION['userrole']>2) { $AdminRight.="<div class='SecondMenu'><a href='?cat=".$alias."_log&id=".$id."'>Лог редактирования записи</a></div>"; }
	
	}}
}
$_SESSION["Msg"]="";
?>