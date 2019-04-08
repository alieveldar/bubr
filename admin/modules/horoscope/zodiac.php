<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	$table=$alias."_lenta"; $table2=$alias."_items";

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$q="INSERT INTO $table2 (`pid`, `aries`, `taurus`, `gemini`, `cancer`, `leo`, `virgo`, `libra`, `scorpio`, `sagittarius`, `capricorn`, `aquarius`, `pisces`) 
		VALUES (".$P["pid"].", '".$P["aries"]."', '".$P["taurus"]."', '".$P["gemini"]."', '".$P["cancer"]."', '".$P["leo"]."', '".$P["virgo"]."', '".$P["libra"]."', '".$P["scorpio"]."', '".$P["sagittarius"]."', '".$P["capricorn"]."', '".$P["aquarius"]."', '".$P["pisces"]."') 
		ON DUPLICATE KEY UPDATE `aries`='".$P["aries"]."', `taurus`='".$P["taurus"]."', `gemini`='".$P["gemini"]."', `cancer`='".$P["cancer"]."', `leo`='".$P["leo"]."', `virgo`='".$P["virgo"]."', `libra`='".$P["libra"]."', `scorpio`='".$P["scorpio"]."', `sagittarius`='".$P["sagittarius"]."', `capricorn`='".$P["capricorn"]."', `aquarius`='".$P["aquarius"]."', `pisces`='".$P["pisces"]."'";
		
		DB($q); 
		$_SESSION["Msg"]="<div class='SuccessDiv'>Запись успешно сохранена!</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}


	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT $table.`name`, $table.`stat`, $table2.* FROM `$table` LEFT JOIN $table2 ON $table2.`pid`=".(int)$id." WHERE (`id`=".(int)$id.") LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
		
	### Заполнение данных
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);
	if ($node["stat"]==1) { $chk="checked"; }
		
	$AdminText='<h2>Знаки зодиака: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.="<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/adapters/jquery.js'></script>";
	$AdminText.='<form action="'.$_SERVER["REQUEST_URI"].'" enctype="multipart/form-data" method="post">';

	### Основные данные
	$AdminText.="<div class='RoundText'><table>";
	foreach ($GLOBAL["zodiac"] as $key => $value):
		$AdminText.='<tr class="TRLine" id="Line'.$ar["id"].'" style="border-bottom:2px dotted #CCC;">
		<td class="LongInput" style="width:10%;" valign="top" align="center"><strong>'.$value["name"].'</strong>'.$C5.'<img src="/userfiles/images/zodiac/'.$key.'.jpg" /></td>
		<td class="LongInput" style="width:80%;" valign="top">';
		$AdminText.='<textarea name="'.$key.'" id="textedit'.$key.'" style="outline:none;" class="texteditors">'.$node[$key].'</textarea>';		
		$AdminText.='</td></tr>';
	endforeach;
	$AdminText.='</table><input type="hidden" name="pid" value="'.(int)$id.'" /><script type="text/javascript">$(".texteditors").ckeditor({customConfig:"/admin/texteditor/config_sm.js"});</script></div>';

	### Сохранение
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
	$AdminText.="</form>";	

// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_photo&id=".$id."'>Основная фотография</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_zodiac&id=".$id."'>Виджет: Знаки Зодиака</a></div>
	$C5<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_lenta' ".$chk."></td><td><b>Материал опубликован</b></td></tr></table></div>";
	}
	}
}
$_SESSION["Msg"]="";
?>