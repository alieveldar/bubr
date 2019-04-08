<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	$table=$alias."_lenta"; $table2="_widget_voting"; $table3="_widget_votes";

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {	
		$data=DB("SELECT * FROM `$table2` WHERE (`pid`=".(int)$id." AND `link`='".$alias."')");
		$votes = array();
		if($data["total"]){
			for ($i=0; $i<$data["total"]; $i++){
				@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
				if($ar['vid'] == 0) $vid = $ar['id'];
				else $votes[] = $ar["id"];
			}
		}
		
		if (!$vid) {
			DB("INSERT INTO `$table2` (`pid`, `link`, `name`, `stat`) VALUES ($id, '$alias', '".$P["name"]."', ".(int)$P["stat"].")");
			$vid = DBL();
		}
		else DB($q = "UPDATE `$table2` SET `name`='".$P["name"]."', `stat`=".(int)$P["stat"]." WHERE (`id`=$vid)");
		
		foreach ($P["votes"] as $key => $value) {
			$value=str_replace("'", "&#039;", $value);
			if($value == '' && in_array($key, $votes) || $P["name"] == '') DB("DELETE FROM `$table2` WHERE (`id`=$key)");
			else if(in_array($key, $votes) && $value != '') DB("UPDATE `$table2` SET `name`='$value' WHERE (`id`=$key)");
			else if($value != '') DB("INSERT INTO `$table2` (`pid`, `link`, `name`, `vid`) VALUES ($id, '$alias', '$value', $vid)");
		}
		
		$_SESSION["Msg"]="<div class='SuccessDiv'>Запись успешно сохранена!</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}


	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB($q = "SELECT `$table`.`name`, `$table`.`stat`, `$table2`.`name` AS `que`, `$table2`.`id` AS `vid`, `$table2`.`stat` AS `vstat` FROM `$table` LEFT JOIN `$table2` ON `$table2`.`pid`=`$table`.`id` AND `$table2`.`vid`=0 AND `$table2`.`link`='".$alias."' WHERE (`$table`.`id`=".(int)$id.") LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
			
	### Заполнение данных
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);
	if ($node["stat"]==1) { $chk="checked"; }
	if ($node["vstat"]==1) { $chk1="checked"; }
	
	$data=DB("SELECT `id`, `name` FROM `$table2` WHERE (`pid`=".(int)$id." AND `link`='".$alias."' AND `vid`=".$node["vid"].")");
		
	$AdminText='<h2>Голосование: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.='<form action="'.$_SERVER["REQUEST_URI"].'" enctype="multipart/form-data" method="post">';

	### Основные данные
	$AdminText.='<div class="RoundText"><table><tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Вопрос</td><td class="LongInput"><input name="name" type="text" value=\''.$node["que"].'\'></td><tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top; padding-top:15px;">Ответы</td><td class="AlmostLongInput Answers">';
	if($data["total"]){
		for ($i=0; $i<$data["total"]; $i++){
			@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
			$AdminText.='<div><input name="votes['.$ar["id"].']" type="text" value=\''.$ar["name"].'\'>';
			if($i >= 2) $AdminText.='<a title="Удалить" onclick="RemoveField($(this))" href="javascript:void(0);"><img style="margin:2px 0 0 3px; width:14px;" valign="middle" src="/admin/images/icons/exit.png"></a>';
			$AdminText.=$C5.'</div>';
		}
	}
	else{
		$AdminText.='<input name="votes[]" type="text">'.$C5;
		$AdminText.='<input name="votes[]" type="text">'.$C5;
	}
	$AdminText.='</td><tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarName"></td><td><a href="javascript:void(0);" onclick="AddField(\'votes\', $(\'.Answers\'));" class="AddField">Добавить поле</a></td><tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Включено</td><td><input type="checkbox" name="stat" value="1" '.$chk1.'></td><tr>';
	$AdminText.='</table>';	
	$AdminText.='</div>';
	
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";

	// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_lenta' ".$chk."></td><td><b>Опубликовано</b></td></tr></table></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_photo&id=".$id."'>Основная фотография</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_report&id=".$id."'>Виджет: Фото-отчет</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_album&id=".$id."'>Виджет: Фото-альбом</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_film&id=".$id."'>Виджет: Видео-вставка</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_voting&id=".$id."'>Виджет: Голосование</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_pretext&id=".$id."'>Виджет: Постскриптум</a></div>
	<br><div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div><br><br>
	<div class='SecondMenu2'><a href='/$alias/view/$id/' target='_blank'>Просмотр на сайте</a></div></form>";
	if ($_SESSION['userrole']>2) { $AdminRight.="<div class='SecondMenu'><a href='?cat=".$alias."_log&id=".$id."'>Лог редактирования записи</a></div>"; }
	
	}}
}
$_SESSION["Msg"]="";
?>