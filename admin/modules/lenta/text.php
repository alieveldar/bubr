<?
### МЕНЮ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$q="UPDATE `".$alias."_lenta` SET `lid`='".str_replace("'", "\'", $P["lid"])."', `text`='".str_replace("'", "\'", $P["PostText"])."' WHERE (id='".(int)$id."')"; DB($q);
		DB("INSERT INTO `_lentalog` (`link`, `id`, `uid`, `data`, `ip`, `text`) VALUES ('".$alias."', '".$id."', '".$_SESSION['userid']."', '".time()."', '".$_SERVER['REMOTE_ADDR']."', 'Редактирование содержания (text)')");
		$_SESSION["Msg"]="<div class='SuccessDiv'>Настройки успешно сохранены</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}	
	
	
	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT `lid`, `text`, `name`, `stat` FROM `".$alias."_lenta` WHERE (`id`='".(int)$id."') LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
		@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]); if ($node["stat"]==1) { $chk="checked"; }
	
		$AdminText='<h2>Редактирование: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg1"];
		
	
		
		$AdminText.="<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/filemanager/ajex.js'></script>";
		$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'><div class='RoundText'>";
		$AdminText.="<h2>Короткое описание публикации</h2><div class='LongInput'><textarea name='lid' style='height:84px; font-size:11px; line-height:17px; padding:4px;'>".$node["lid"]."</textarea></div>".$C15;
		$AdminText.="<h2>Основное содержание публикации</h2><textarea name='PostText' id='textedit' style='outline:none;'>".$node["text"]."</textarea>".$C10;
		$AdminText.=$C10."<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div></div>";
		$AdminText.="<script type='text/javascript'>$(document).ready(function() { var beditor=CKEDITOR.replace('textedit'); AjexFileManager.init({ returnTo: 'ckeditor', editor: beditor}); });</script>";
	} 

	// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_lenta' ".$chk."></td><td><b>Опубликовано</b></td></tr></table></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_photo&id=".$id."'>Основная фотография</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_report&id=".$id."'>Виджет: Фото-отчет</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_album&id=".$id."'>Виджет: Фото-альбом</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_film&id=".$id."'>Виджет: Видео-вставка</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_voting&id=".$id."'>Виджет: Голосование</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_pretext&id=".$id."'>Виджет: Постскриптум</a></div>
	<br><div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div><br><br>
	<div class='SecondMenu2'><a href='/$alias/view/$id/' target='_blank'>Просмотр на сайте</a></div></form>";
	if ($_SESSION['userrole']>2) { $AdminRight.="<div class='SecondMenu'><a href='?cat=".$alias."_log&id=".$id."'>Лог редактирования записи</a></div>"; }
	
	}
}
$_SESSION["Msg"]="";
?>