<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);
	
	$table = $alias.'_lenta';
	$table2 = '_widget_eventmap';
	$table3 = '_widget_eventtype';
	
	$data=DB("SELECT `$table`.`id` AS `iid`, `$table`.`name` AS `iname`, `$table`.`text` AS `itext`, `$table`.`pic` AS `ipic`, `$table`.`stat` AS `istat`, `$table`.`data` AS `idata`, `$table2`.* FROM `$table` LEFT JOIN `$table2` ON `$table2`.`pid`=$id AND `$table2`.`link`='$alias' WHERE (`$table`.`id`=$id) LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);	

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		@require($ROOT."/modules/standart/ImageResizeCrop.php");
		$name = $P['name'] ? $P['name'] : $node['iname'];
		$text = $P['text'] ? $P['text'] : $node['itext'];
		$pic = $node['pic'];
		$icon= $node['icon'];
		$data = $P['data'] ? strtotime($P['data']) : $node['idata'];
		
		if($pic != $P["pic"] || $P["pic"] == ''){				
			if($pic) { foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { @unlink($ROOT."/userfiles/".$path."/".$pic); }}
			$pic = $P["pic"];				
			if($pic) {					
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
			else if($node['ipic']) { $pic = $GLOBAL["pic"]; foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { copy($ROOT."/userfiles/".$path."/".$node['ipic'], $ROOT."/userfiles/".$path."/".$pic); }}
		}
		if($icon != $P["icon"] || $P["icon"] == ''){				
			if($icon) @unlink($ROOT."/userfiles/mapicon/".$icon);
			$icon = $P["icon"];				
			if($icon) {
				crop($ROOT."/userfiles/temp/".$icon, $ROOT."/userfiles/mapicon/".$icon);
				resize($ROOT."/userfiles/mapicon/".$icon, $ROOT."/userfiles/mapicon/".$icon, 100, 100);
			}
		}
		if($P['id']) DB("UPDATE `$table2` SET `name`='".$name."', `pic`='".$pic."', `text`='".$text."', `maps`='".$P['maps']."', `icon`='".$icon."', `stat`=".(int)$P['stat'].", `promo`=".(int)$P['promo'].", `tid`=".$P['tid'].", `period`=".$P['period'].", `data`='".$data."' WHERE(`id`=".$P['id'].")");
		else DB("INSERT INTO `$table2` (`name`, `pic`, `text`, `maps`, `icon`, `stat`, `promo`, `link`, `pid`, `tid`, `period`, `data`) VALUES('".$name."', '".$pic."', '".$text."', '".$P['maps']."', '".$icon."', ".(int)$P['stat'].", ".(int)$P['promo'].", '".$alias."', ".$id.", ".$P['tid'].", ".$P['period'].", '".$data."')");
		$_SESSION["Msg"]="<div class='SuccessDiv'>Запись успешно сохранена!</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

	// ВЫВОД ПОЛЕЙ И ФОРМ
	
	$data=DB("SELECT `id`, `name` FROM `".$table3."` WHERE (`stat`=1) ORDER BY `rate` DESC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $types[$ar["id"]]=$ar["name"]; endfor;
			
	### Заполнение данных	
	if ($node["istat"]==1) { $chk="checked"; }
	if ($node["stat"]==1) { $chk2="checked"; }
	if ($node["promo"]==1) { $chk3="checked"; }
		
	$AdminText='<script type="text/javascript" src="http://maps.api.2gis.ru/1.0"></script><script type="text/javascript" src="/admin/texteditor/ckeditor.js"></script><script type="text/javascript" src="/admin/texteditor/adapters/jquery.js"></script>';
	$AdminText.='<h2>Карта событий: &laquo'.$node["iname"].'&raquo;</h2>'.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете добавить только одно событие</div>";
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";

	### Основные данные	
	$AdminText.="<div class='RoundText'><table>".'
	<tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Название события</td><td class="LongInput"><input name="name" id="name" type="text" value=\''.$node["name"].'\'></td><tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Тип события</td><td class="LongInput"><div class="sdiv"><select name="tid">'.GetSelected($types, $node["tid"]).'</select></div></td><tr>';		
	$AdminText.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Фотография</td><td class="LongInput"><div class="uploaderCon" style="'.($node['pic'] ? 'display:none;' : '').'"><div class="uploader"></div><div class="Info">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles">';
	if($node['pic']) $AdminText.='<span class="imgCon"><img src="/userfiles/picpreview/'.$node['pic'].'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="pic" value="'.$node['pic'].'" /></span>';
	$AdminText.='</div></td></tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Включено</td><td class="NormalInput"><input name="stat" type="checkbox" value="1" '.$chk2.'></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Закреплено</td><td class="NormalInput"><input name="promo" type="checkbox" value="1" '.$chk3.'></td></tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Описание события</td><td class="LongInput"><textarea name="text" id="textedit">'.$node["text"].'</textarea></td><tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Дата</td><td class="LongInput"><div><input type="hidden" name="data" value="'.($node["data"] ? date('Y-m-d', $node["data"]) : '').'"><div id="datepicker"></div></div>'.$C5.'<a href="javascript:void(0);" onclick="clearCalendar();">Очистить</a></td><tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Длительность (дней)</td><td class="SmallInput"><input name="period" type="text" value="'.$node["period"].'"></td><tr>';	
	$AdminText.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Иконка маркера на карте</td><td class="LongInput"><div class="uploaderCon" style="'.($node['icon'] ? 'display:none;' : '').'"><div class="uploader"></div><div class="Info">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles">';
	if($node['icon']) $AdminText.='<span class="imgCon"><img src="/userfiles/mapicon/'.$node['icon'].'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="icon" value="'.$node['icon'].'" /></span>';
	$AdminText.='</div></td></tr>';			
	$AdminText.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top;">Координаты на карте</td><td class="LongInput"><div id="Map'.$node["iid"].'" class="Map"></div>'.$C5.'<a href="javascript:void(0);" onclick="clearMap('.$node["iid"].');">Очистить</a></td><tr>';
	$AdminText.='</table><input name="maps" class="maps_'.$node["iid"].'" type="hidden" value="'.$node["maps"].'"><input class="maps_default" type="hidden" value="'.$VARS['maps'].'"></div>';

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
	<div class='SecondMenu'><a href='?cat=".$alias."_contacts&id=".$id."'>Виджет: Лого и контакты</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_eventmap&id=".$id."'>Виджет: Карта событий</a></div>
	<br><div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div><br><br>
	<div class='SecondMenu2'><a href='/$alias/view/$id/' target='_blank'>Просмотр на сайте</a></div></form>";
	if ($_SESSION['userrole']>2) { $AdminRight.="<div class='SecondMenu'><a href='?cat=".$alias."_log&id=".$id."'>Лог редактирования записи</a></div>"; }
	
	}}
}
$_SESSION["Msg"]="";
?>