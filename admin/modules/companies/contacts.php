<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	$table=$alias."_items";
	$table2=$alias."_contacts";

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["addbutton"])) {
		$worktime=implode('|', $P["worktime"]);
		if(!$P["adres"]) $P["maps"] = '';
		$res=DB("INSERT INTO $table2 (`pid`,`adres`,`phone`,`maps`,`worktime`) VALUES (".(int)$id.", '".$P["adres"]."', '".$P["phone"]."', '".$P["maps"]."', '".$worktime."')");
		$_SESSION["Msg"]="<div class='C20'></div><div class='SuccessDiv'>Данные успешно добавлены!</div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}
	
	if (isset($P["savebutton"])) {
		foreach ($P["adres"] as $key=>$val) {			
			$adres=$val; $phone=$P["phone"][$key]; $maps=$adres ? $P["maps"][$key] : ''; $worktime=implode('|', $P["worktime"][$key]);
			$q="UPDATE $table2 SET `adres`='".$adres."', `phone`='".$phone."', `maps`='".$maps."', `worktime`='".$worktime."' WHERE (`id`='".(int)$key."' && pid='".(int)$id."')"; DB($q);
		}
		$_SESSION["Msg"]="<div class='SuccessDiv'>Запись успешно сохранена!</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}


	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT `name`, `stat` FROM `$table` WHERE (`id`=".(int)$id.") LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
		
	### Заполнение данных
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);
	if ($node["stat"]==1) { $chk="checked"; }
		
	$AdminText='<script type="text/javascript" src="http://maps.api.2gis.ru/1.0"></script><h2 style="float:left;">Контакты и часы работы: &laquo<span class="companyName">'.$node["name"].'</span>&raquo;</h2><div class="LinkG" style="float:right;"><a href="javascript:void(0);" onclick="ToggleBlock2(\'#Tgg\');">Добавить контакты</a></div>'.$C5.$_SESSION["Msg"];
	$AdminText.='<form action="'.$_SERVER["REQUEST_URI"].'" enctype="multipart/form-data" method="post">';
	### Основные данные
	$AdminText.='<div id="Line0" class="Line"><div class="RoundTextHide" id="Tgg"><table><tr class="TRLine0"><td style="width:15%;"></td><td style="width:85%;"></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Адрес</td><td class="LongInput"><input name="adres" class="adres_0" type="text" onfocus="adresInpFocus($(this));" onblur="adresInpBlur($(this));"><a href="javascript:void(0)" onclick="GetGeoObjects($(this), 0)" class="GetGeoObjects">Найти на карте</a></td><tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Телефон</td><td class="LongInput"><input name="phone" class="phone_0" type="text" value=""></td><tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Время работы</td><td class="LongInput Worktime">
		<span>Понедельник<br /><input name="worktime[]" class="worktime_0" type="text"></span>
		<span>Вторник<br /><input name="worktime[]" class="worktime_0" type="text"></span>
		<span>Среда<br /><input name="worktime[]" class="worktime_0" type="text"></span>
		<span>Четверг<br /><input name="worktime[]" class="worktime_0" type="text"></span>
		<span>Пятница<br /><input name="worktime[]" class="worktime_0" type="text"></span>
		<span>Суббота<br /><input name="worktime[]" class="worktime_0" type="text"></span>
		<span>Воскресенье<br /><input name="worktime[]" class="worktime_0" type="text"></span>
		<div class="Info">Образец: 09:00-18:00</div>
	</td><tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top;">Координаты на карте</td><td class="LongInput"><div id="Map0" class="Map"></div></td><tr>';
	$AdminText.='</table><input name="maps" class="maps_0" type="hidden" value="'.$VARS['maps'].'"><input class="maps_default" type="hidden" value="'.$VARS['maps'].'"><div class="C5"></div><div class="CenterText"><input type="submit" name="addbutton" id="addbutton" class="SaveButton" value="Добавить данные"></div></div></div></form>';	
	
	
	$data=DB("SELECT * FROM $table2 WHERE (`pid`=".(int)$id.")");
	if ($data["total"]){	
		$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";		
		### Переменные пользователей	
		for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $worktime = explode('|', $ar["worktime"]);
			$AdminText.='<div id="Line'.$ar["id"].'" class="Line"><div class="RoundText"><table><tr class="TRLine0"><td style="width:15%;"></td><td style="width:85%;"><div class="Act" id="Act'.$ar["id"].'"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$table2.'\')">'.AIco('exit').'</a></div></td></tr>';
			$AdminText.='<tr class="TRLine0"><td class="VarText">Адрес</td><td class="LongInput"><input name="adres['.$ar["id"].']" class="adres_'.$ar["id"].'" type="text" value=\''.$ar["adres"].'\' onfocus="adresInpFocus($(this));" onblur="adresInpBlur($(this));"><a href="javascript:void(0)" onclick="GetGeoObjects($(this), '.$ar["id"].')" class="GetGeoObjects">Найти на карте</a></td><tr>';
			$AdminText.='<tr class="TRLine1"><td class="VarText">Телефон</td><td class="LongInput"><input name="phone['.$ar["id"].']" class="phone_'.$ar["id"].'" type="text" value="'.$ar["phone"].'"></td><tr>';
			$AdminText.='<tr class="TRLine0"><td class="VarText">Время работы</td><td class="LongInput Worktime">
				<span>Понедельник<br /><input name="worktime['.$ar["id"].'][]" class="worktime_'.$ar["id"].'" type="text" value="'.$worktime[0].'"></span>
				<span>Вторник<br /><input name="worktime['.$ar["id"].'][]" class="worktime_'.$ar["id"].'" type="text" value="'.$worktime[1].'"></span>
				<span>Среда<br /><input name="worktime['.$ar["id"].'][]" class="worktime_'.$ar["id"].'" type="text" value="'.$worktime[2].'"></span>
				<span>Четверг<br /><input name="worktime['.$ar["id"].'][]" class="worktime_'.$ar["id"].'" type="text" value="'.$worktime[3].'"></span>
				<span>Пятница<br /><input name="worktime['.$ar["id"].'][]" class="worktime_'.$ar["id"].'" type="text" value="'.$worktime[4].'"></span>
				<span>Суббота<br /><input name="worktime['.$ar["id"].'][]" class="worktime_'.$ar["id"].'" type="text" value="'.$worktime[5].'"></span>
				<span>Воскресенье<br /><input name="worktime['.$ar["id"].'][]" class="worktime_'.$ar["id"].'" type="text" value="'.$worktime[6].'"></span>
			</td><tr>';
			$AdminText.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top;">Координаты на карте</td><td class="LongInput"><div id="Map'.$ar["id"].'" class="Map"></div></td><tr>';
			$AdminText.='</table><input name="maps['.$ar["id"].']" class="maps_'.$ar["id"].'" type="hidden" value="'.$ar["maps"].'"></div>'.$C15.'</div>';
		endfor;
		$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
		$AdminText.="</form>";
	}

// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_contacts&id=".$id."'>Контакты и часы работы</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_pics&id=".$id."'>Фотографии компании</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_consults&id=".$id."'>Консультации</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_actions&id=".$id."'>Акции</a></div>
	$C5<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_items' ".$chk."></td><td><b>Материал опубликован</b></td></tr></table></div>";
	}
	}
}
$_SESSION["Msg"]="";
?>