<?
### МЕНЮ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST; if (isset($P["savebutton"])) {
		$P["PostText"]=str_replace("'", "\'", $P["PostText"]);
		$P["Post2Text"]=str_replace("'", "\'", $P["Post2Text"]);
		$P["Post3Text"]=str_replace("'", "\'", $P["Post3Text"]);
		$P["lid"]=str_replace("'", "\'", $P["lid"]);
		$q="UPDATE `".$alias."_lenta` SET `pay`='".$P["lid"]."', `alttext`='".$P["PostText"]."', `endtext`='".$P["Post3Text"]."', `adv`='".$P["Post2Text"]."' WHERE (id='".(int)$id."')"; DB($q);
		$_SESSION["Msg"]="<div class='SuccessDiv'>Настройки успешно сохранены</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}	
	
	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT `pay`, `endtext`, `alttext`, `adv`, `name`, `stat` FROM `".$alias."_lenta` WHERE (`id`='".(int)$id."') LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
		@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]); if ($node["stat"]==1) { $chk="checked"; }
		$AdminText='<h2>Редактирование: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg1"];
		$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'><div class='RoundText'>";
		$AdminText.=$C."<h2>Заключительный текст публикации</h2><textarea name='Post3Text' id='text3edit' style='outline:none;'>".$node["endtext"]."</textarea>".$C15;
		$AdminText.="<h2>Ссылка на источник</h2><div class='LongInput'><input name='lid' id='lid' value='$node[pay]' placeholder='yandex.ru'></div>".$C5;
		#$AdminText.=$C10."<h2>Альтернативный текст</h2><textarea name='PostText' id='textedit' style='outline:none;'>".$node["alttext"]."</textarea>";
		#$AdminText.=$C10."<h2>Оплаченная часть публикации</h2><textarea name='Post2Text' id='text2edit' style='outline:none;'>".$node["adv"]."</textarea>";
		$AdminText.="</div>".$C5."<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
		$AdminText.="<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/adapters/jquery.js'></script>";
		$AdminText.="<script type='text/javascript'>$(document).ready(function() { $('#text3edit').ckeditor({customConfig:'/admin/texteditor/config_md.js'}); /*$('#textedit').ckeditor({customConfig:'/admin/texteditor/config_sm.js'}); $('#text2edit').ckeditor({customConfig:'/admin/texteditor/config_sm.js'});*/ });</script>";
	} 

	// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_lenta' ".$chk."></td><td><b>Опубликовано</b></td></tr></table></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_photo&id=".$id."'>Основная фотография</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_report&id=".$id."'>Виджет: Фото-отчет</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_album&id=".$id."'>Виджет: Фото-альбом</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_film&id=".$id."'>Виджет: Видео-вставка</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_voting&id=".$id."'>Виджет: Голосование</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_pretext&id=".$id."'>Виджет: Постскриптум</a></div>
	<br><div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div><br><br>
	<div class='SecondMenu2'><a href='/$alias/view/$id/' target='_blank'>Просмотр на сайте</a></div></form>";
	if ($_SESSION['userrole']>2) { $AdminRight.="<div class='SecondMenu'><a href='?cat=".$alias."_log&id=".$id."'>Лог редактирования записи</a></div>"; }
	
	}
}
$_SESSION["Msg"]="";
?>