<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	
// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST; $module="eventmap"; $table="_widget_eventmap"; $table2="_widget_eventtype";
	
	if (isset($P["savebutton"])) {
		$P["settings"][2]=(int)$P["settings"][2]; $P["settings"][3]=(int)$P["settings"][3]; $P["settings"][4]=(int)$P["settings"][4]; $settings = implode('|', $P["settings"]); $hid=(int)$P["fstat"]?1:0;
		DB("UPDATE `_pages` SET `name`='".trim($P["name"])."', `sets`='".$settings."', `text`='".$P["PostText"]."', `stat`='".$hid."', `link`='".trim($P["link"])."' WHERE (`module`='".$module."')");
		$_SESSION["Msg"]="<div class='C20'></div><div class='SuccessDiv'>Данные успешно сохранены!</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

/*	
1. Включено или выключено (чекбокс)
2. Центр карты по умолчанию (координаты)
3. Заголовок страницы с картой (text input)
4. Адрес (alias) по которому открывается карта
5. Текстовое описание выводится над картой (WYSIWYG)
6. Название города Именительный падеж (text input) 
7. Название города Родительный падеж (text input)
8. «Период показа», за который отображать события (в днях)
9. Размер карты ширина и высота 
*/
	
	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT `id`,`name`,`sets`,`text`,`stat`,`link` FROM `_pages` WHERE (`module`='".$module."')");
	@mysql_data_seek($data["result"],0); $ar=@mysql_fetch_array($data["result"]); if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; }
	$settings=explode('|', $ar["sets"]); list($nameip, $namerp, $seedays, $width, $height)=explode('|', $ar["sets"]);
	
	$AdminText='<h2 style="float:left;">'.$ar["name"].'</h2>'.$_SESSION["Msg"];	
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";
	$AdminText.="<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/filemanager/ajex.js'></script>";
	$AdminText.="<div class='RoundText'><table>";
		$AdminText.='<tr class="TRLine0"><td class="VarText">Название раздела</td><td class="LongInput"><input name="name" type="text" value=\''.$ar["name"].'\' placeholder="Карта Казани с событиями"></td></tr>';
		$AdminText.='<tr class="TRLine1"><td class="VarText">Город (именительный падеж)</td><td class="LongInput"><input name="settings[0]" type="text" value="'.$settings[0].'" placeholder="Казань"></td></tr>';
		$AdminText.='<tr class="TRLine0"><td class="VarText">Город (родительный падеж)</td><td class="LongInput"><input name="settings[1]" type="text" value="'.$settings[1].'" placeholder="Казани"></td></tr>';
		$AdminText.='<tr class="TRLine1"><td class="VarText">Отображение событий (дней)</td><td class="LongInput"><input name="settings[2]" type="text" value="'.$settings[2].'" placeholder="30"></td></tr>';
		$AdminText.='<tr class="TRLine0"><td class="VarText">Размеры карты</td><td class="SmallInput">ширина: <input name="settings[3]" type="text" value="'.$settings[3].'" placeholder="700">      высота: <input name="settings[4]" type="text" value="'.$settings[4].'" placeholder="500"></td></tr>';
		$AdminText.='<tr class="TRLine1"><td class="VarText">Адрес страницы на сайте</td><td class="LongInput"><input name="link" type="text" value="'.$ar["link"].'" placeholder="eventmap"></td></tr>';
		$AdminText.='<tr class="TRLine0"><td class="VarText">Активность раздела</td><td class="SmallInput"><input name="fstat" id="RS-'.$ar["id"].'-_pages" type="checkbox" value="1" '.$chk.'></td></tr>';
	$AdminText.="</table></div>".$C10;
	$AdminText.="<h2>Текст модуля (выводится над картой)</h2><textarea name='PostText' id='textedit' style='outline:none;'>".$ar["text"]."</textarea><script type='text/javascript'>var editor=CKEDITOR.replace('textedit'); AjexFileManager.init({ returnTo: 'ckeditor', editor: editor});</script>";
	$AdminText.=$C10."<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
	$AdminText.="</form>";
	
	// ПРАВАЯ КОЛОНКА
	$types=""; $type=array(); $data=DB("SELECT `id`, `name`, `stat` FROM `".$table2."` order by `rate` DESC"); if ($data["total"]==0) { $types="<tr><td>Нет типов событий</td></tr>";  } else {
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; } $type[$ar["id"]]=$ar;
	$types.="<tr class='TRLine TRLine".($i%2)."'><td class='CheckInput'><input type='checkbox' id='RS-".$ar["id"]."-".$table2."' ".$chk."></td><td>".$ar["name"]."</td></tr>"; endfor; }
	$AdminRight.="<h2>Типы событий</h2><div class='SecondMenu'><a href='?cat=adm_eventmaptype'>Редактировать список</a></div><div class='RoundText' id='Tgg'><table>".$types."</table></div>";
	$AdminRight.=$C20."<div class='SecondMenu'><a href='?cat=adm_eventmap'>Список событий карты</a></div><div class='SecondMenu'><a href='/".$page["link"]."' target='_blank'>Просмотр «Карты событий»</a></div>";
}
$_SESSION["Msg"]="";
?>