<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	$table = '_widget_eventmap';
	$table2 = '_widget_eventtype';
	
	$data=DB("SELECT `$table`.*, `_pages`.`link` AS `plink` FROM `$table` LEFT JOIN `_pages` ON `_pages`.`module`='eventmap' WHERE (`$table`.`id`=$id) LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);	

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		@require($ROOT."/modules/standart/ImageResizeCrop.php");
		$pic = $node['pic'];
		$icon= $node['icon'];
		$data = $P['data'] ? strtotime($P['data']) : '';
		if($pic != $P["pic"]){				
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
		}
		if($icon != $P["icon"]){				
			if($icon) @unlink($ROOT."/userfiles/mapicon/".$icon);
			$icon = $P["icon"];				
			if($icon) {
				crop($ROOT."/userfiles/temp/".$icon, $ROOT."/userfiles/mapicon/".$icon);
				resize($ROOT."/userfiles/mapicon/".$icon, $ROOT."/userfiles/mapicon/".$icon, 100, 100);
			}
		}
		DB("UPDATE `$table` SET `name`='".$P['name']."', `pic`='".$pic."', `text`='".$P['text']."', `maps`='".$P['maps']."', `icon`='".$icon."', `stat`=".(int)$P['stat'].", `promo`=".(int)$P['promo'].", `tid`=".$P['tid'].", `period`=".$P['period'].", `data`='".$data."' WHERE(`id`=".$id.")");		
		$_SESSION["Msg"]="<div class='SuccessDiv'>Запись успешно сохранена!</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

	// ВЫВОД ПОЛЕЙ И ФОРМ
	
	$data=DB("SELECT `id`, `name` FROM `".$table2."` WHERE (`stat`=1) ORDER BY `rate` DESC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $types[$ar["id"]]=$ar["name"]; endfor;
			
	### Заполнение данных	
	if ($node["stat"]==1) { $chk="checked"; }
	if ($node["promo"]==1) { $chk2="checked"; }
		
	$AdminText='<script type="text/javascript" src="http://maps.api.2gis.ru/1.0"></script><script type="text/javascript" src="/admin/texteditor/ckeditor.js"></script><script type="text/javascript" src="/admin/texteditor/adapters/jquery.js"></script>';
	$AdminText.='<h2>Редактирование события: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";

	### Основные данные	
	$AdminText.="<div class='RoundText'><table>".'
	<tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Название события</td><td class="LongInput"><input name="name" id="name" type="text" value=\''.$node["name"].'\'></td><tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Тип события</td><td class="LongInput"><div class="sdiv"><select name="tid">'.GetSelected($types, $node["tid"]).'</select></div></td><tr>';		
	$AdminText.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Фотография</td><td class="LongInput"><div class="uploaderCon" style="'.($node['pic'] ? 'display:none;' : '').'"><div class="uploader"></div><div class="Info">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles">';
	if($node['pic']) $AdminText.='<span class="imgCon"><img src="/userfiles/picpreview/'.$node['pic'].'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="pic" value="'.$node['pic'].'" /></span>';
	$AdminText.='</div></td></tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Включено</td><td class="NormalInput"><input name="stat" type="checkbox" value="1" '.$chk.'></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Закреплено</td><td class="NormalInput"><input name="promo" type="checkbox" value="1" '.$chk2.'></td></tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Описание события</td><td class="LongInput"><textarea name="text" id="textedit">'.$node["text"].'</textarea></td><tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Дата</td><td class="LongInput"><div><input type="hidden" name="data" value="'.($node["data"] ? date('Y-m-d', $node["data"]) : '').'"><div id="datepicker"></div></div>'.$C5.'<a href="javascript:void(0);" onclick="clearCalendar();">Очистить</a></td><tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Длительность (дней)</td><td class="SmallInput"><input name="period" type="text" value="'.$node["period"].'"></td><tr>';	
	$AdminText.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Иконка маркера на карте</td><td class="LongInput"><div class="uploaderCon" style="'.($node['icon'] ? 'display:none;' : '').'"><div class="uploader"></div><div class="Info">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles">';
	if($node['icon']) $AdminText.='<span class="imgCon"><img src="/userfiles/mapicon/'.$node['icon'].'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="icon" value="'.$node['icon'].'" /></span>';
	$AdminText.='</div></td></tr>';			
	$AdminText.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top;">Координаты на карте</td><td class="LongInput"><div id="Map'.$node["id"].'" class="Map"></div>'.$C5.'<a href="javascript:void(0);" onclick="clearMap('.$node["id"].');">Очистить</a></td><tr>';
	$AdminText.='</table><input name="maps" class="maps_'.$node["id"].'" type="hidden" value="'.$node["maps"].'"><input class="maps_default" type="hidden" value="'.$VARS['maps'].'"></div>';

	### Сохранение
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div></form>";

	// ПРАВАЯ КОЛОНКА
	$AdminRight=$C25."<div class='SecondMenu'><a href='?cat=adm_eventmap'>Список событий карты</a></div><div class='SecondMenu'><a href='/".$node["plink"]."' target='_blank'>Просмотр «Карты событий»</a></div><div class='SecondMenu'><a href='?cat=adm_eventmapsets'>Настройки «Карты событий»</a></div>";
	}
}
$_SESSION["Msg"]="";
?>