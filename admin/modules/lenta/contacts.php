<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);
	
	$table = $alias.'_lenta';
	$table2 = '_widget_contacts';
	$data=DB("SELECT `$table`.`name` AS `iname`, `$table`.`stat`, `$table2`.* FROM `$table` LEFT JOIN `$table2` ON `$table2`.`pid`=$id AND `$table2`.`link`='$alias' WHERE (`$table`.`id`=$id) LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);	

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$pic = $node['pic'];
		if($pic != $P["pic"]){				
			if($pic) { foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { @unlink($ROOT."/userfiles/".$path."/".$pic); }}
			$pic = $P["pic"];				
			if($pic) {
				@require($ROOT."/modules/standart/ImageResizeCrop.php");
	
				foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) {			
					if (!is_dir($ROOT."/userfiles/".$path)) { mkdir($ROOT."/userfiles/".$path, 0777); }
					list($w,$h)=getimagesize($ROOT."/userfiles/temp/".$pic);
					list($sw, $sh)=explode("-", $size);
					
					if ($path!="picoriginal") {
						if($path=="picpreview") resize($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/".$path."/".$pic, $sw, $sh);
						else{					
							$k = min($w / $sw, $h / $sh);
							$x = round(($w - $sw * $k) / 2); $y = round(($h - $sh * $k) / 2);
							crop($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/".$path."/".$pic, array($x, $y, round($sw * $k), round($sh * $k)));
							resize($ROOT."/userfiles/".$path."/".$pic, $ROOT."/userfiles/".$path."/".$pic, $sw, $sh);			
						}			
					}
				}
				rename($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/picoriginal/".$pic);
			}
		}
		if($P['id']) DB("UPDATE `$table2` SET `name`='".$P['name']."', `pic`='".$P['pic']."', `address`='".$P['address']."', `phone`='".$P['phone']."', `anonce`='".$P['anonce']."' WHERE(`id`=".$P['id'].")");
		else DB("INSERT INTO `$table2` (`name`, `pic`, `address`, `phone`, `anonce`, `link`, `pid`) VALUES('".$P['name']."', '".$P['pic']."', '".$P['address']."', '".$P['phone']."', '".$P['anonce']."', '".$alias."', ".$id.")");
		$_SESSION["Msg"]="<div class='SuccessDiv'>Запись успешно сохранена!</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

	// ВЫВОД ПОЛЕЙ И ФОРМ
			
	### Заполнение данных	
	if ($node["stat"]==1) { $chk="checked"; }
		
	$AdminText='<h2>Редактирование логотипа и контактов: &laquo'.$node["iname"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";

	### Основные данные	
	$AdminText.="<div class='RoundText'><table>".'
	<tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Название компании</td><td class="LongInput"><input name="name" id="name" type="text" value=\''.$node["name"].'\'></td><tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top; padding-top:10px;">Логотип компании</td><td class="LongInput"><div class="uploaderCon" style="'.($node['pic'] ? 'display:none;' : '').'"><div class="uploader"></div><div class="Info">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles">';
	if($node['pic']) $AdminText.='<span class="imgCon"><img src="/userfiles/picpreview/'.$node['pic'].'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="pic" value="'.$node['pic'].'" /></span>';
	$AdminText.='</div></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Адрес</td><td class="LongInput"><input name="address" class="address" type="text" value=\''.$node["address"].'\'></td><tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Телефон</td><td class="LongInput"><input name="phone" class="phone" type="text" value=\''.$node["phone"].'\'></td><tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Краткое описание</td><td class="LongInput"><textarea name="anonce">'.$node["anonce"].'</textarea></td><tr>
	'."</table></div>";

	### Сохранение
	$AdminText.="<div class='CenterText'><input name='id' type='hidden' value='".$node["id"]."'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";

	// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_lenta' ".$chk."></td><td><b>Опубликовано</b></td></tr></table></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_photo&id=".$id."'>Основная фотография</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_pretext&id=".$id."'>Виджет: Текстовые поля</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_voting&id=".$id."'>Виджет: Голосование</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_report&id=".$id."'>Виджет: Фото-отчет</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_album&id=".$id."'>Виджет: Фото-альбом</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_film&id=".$id."'>Виджет: Видео-вставка</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_contacts&id=".$id."'>Виджет: Лого и контакты</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_eventmap&id=".$id."'>Виджет: Карта событий</a></div>
	<br><div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div><br><br>
	<div class='SecondMenu2'><a href='/$alias/view/$id/' target='_blank'>Просмотр на сайте</a></div></form>";
	if ($_SESSION['userrole']>2) { $AdminRight.="<div class='SecondMenu'><a href='?cat=".$alias."_log&id=".$id."'>Лог редактирования записи</a></div>"; }
	
	}}
}
$_SESSION["Msg"]="";
?>