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
		
		$q="UPDATE `".$alias."_lenta` SET `lid`='".str_replace("'", '&#039;', $P["lid"])."', `text`='".$P["PostText"]."', `alttext`='".$P["Post2Text"]."' WHERE (id='".(int)$id."')"; DB($q);
		$_SESSION["Msg"]="<div class='SuccessDiv'>Настройки успешно сохранены</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}	
	
	
	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT `lid`, `text`, `alttext`, `name`, `stat` FROM `".$alias."_lenta` WHERE (`id`='".(int)$id."') LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id);
	$GLOBAL["error"]=1; } else {
		@mysql_data_seek($data["result"], 0); 
		$node=@mysql_fetch_array($data["result"]);
		if ($node["stat"]==1) { $chk="checked"; }
	
		$AdminText='<h2>Редактирование: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg1"];
		$AdminText.="<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/filemanager/ajex.js'></script>";
		$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'><div class='RoundText'>";
		$AdminText.="<h2>Короткое описание опроса</h2><div class='LongInput'><textarea name='lid'>".$node["lid"]."</textarea></div>".$C15;
		$AdminText.="<h2>Содержание опроса</h2><textarea name='PostText' id='textedit' style='outline:none;'>".$node["text"]."</textarea>
		<script type='text/javascript'>var editor=CKEDITOR.replace('textedit'); AjexFileManager.init({ returnTo: 'ckeditor', editor: editor});</script>";
		
		$AdminText.="<div >".$C."<br><h2>Текст по окончанию опроса</h2><textarea name='Post2Text' id='text2edit' style='outline:none;'>".$node["alttext"]."</textarea>
		<script type='text/javascript'>var editor=CKEDITOR.replace('text2edit'); AjexFileManager.init({ returnTo: 'ckeditor', editor: editor});</script></div>";
		$AdminText.=$C10."<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div></div>";
	} 

// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
   	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные данные</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_sets&id=".$id."'>Настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_photo&id=".$id."'>Основная фотография</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	
	<div class='SecondMenu'><a href='?cat=".$alias."_voting&id=".$id."'>Вопросы и ответы</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_results&id=".$id."'>Результаты</a></div>
	$C5<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_lenta' ".$chk."></td><td><b>Материал опубликован</b></td></tr></table></div>";
	$AdminRight.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div></form>";
	}
}
$_SESSION["Msg"]="";
?>