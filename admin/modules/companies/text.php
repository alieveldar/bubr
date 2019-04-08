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
		
		$q="UPDATE `".$alias."_items` SET `text`='".$P["PostText"]."', `anonce`='".$P["anonce"]."' WHERE (id='".(int)$id."')"; DB($q);
		$_SESSION["Msg"]="<div class='SuccessDiv'>Настройки успешно сохранены</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}	
	
	
	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT `anonce`, `text`, `name`, `stat` FROM `".$alias."_items` WHERE (`id`='".(int)$id."') LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
		@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]); if ($node["stat"]==1) { $chk="checked"; }
	
		$AdminText='<h2>Редактирование: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg"];
		$AdminText.="<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/filemanager/ajex.js'></script>";
		$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'><div class='RoundText'>";
		$AdminText.="<h2>Описание-анонс</h2><div class='LongInput'><textarea name='anonce'>".$node["anonce"]."</textarea></div>";
		$AdminText.="<h2>Основное содержание публикации</h2><textarea name='PostText' id='textedit' style='outline:none;'>".$node["text"]."</textarea>
		<script type='text/javascript'>var editor=CKEDITOR.replace('textedit'); AjexFileManager.init({ returnTo: 'ckeditor', editor: editor});</script>";
		$AdminText.=$C10."<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div></div></form>";
	} 

// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_contacts&id=".$id."'>Контакты и часы работы</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_pics&id=".$id."'>Фотографии компании</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_consults&id=".$id."'>Консультации</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_actions&id=".$id."'>Акции</a></div>
	$C5<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_items' ".$chk."></td><td><b>Материал опубликован</b></td></tr></table></div>";
	}
}
$_SESSION["Msg"]="";
?>