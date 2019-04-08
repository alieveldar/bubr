<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	@require_once 'demot-create.php';

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	# upload
	if (isset($P["addbutton"])) {
		if (isset($_FILES["photo"]["name"]) && $_FILES["photo"]["name"]!="") {			
		if ($P["picname"]!="") { foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) {@unlink("../userfiles/".$path."/".$P["picname"]); } @unlink("../userfiles/demots/".$P["picname".'.jpg']); }
		@require("modules/UploadPhoto.php");
		demotivator($GLOBAL['userfilesPath'].'picoriginal/'.$picname, $GLOBAL['userfilesPath'].'demots/'.$picname.'.jpg', '', '', '', $VARS["mdomain"]); 			
		if ($loaded==1) { $class="SuccessDiv"; $q="UPDATE `".$alias."_lenta` SET `pic`='".$picname."' WHERE (id='".(int)$id."')"; DB($q); } else { $class="ErrorDiv";	}
		} $_SESSION["Msg1"]="<div class='".$class."'>".$msg."</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}
	
	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT `pic`, `name`, `stat` FROM `".$alias."_lenta` WHERE (`id`='".(int)$id."') LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
		
	### Загрузка фотографии
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]); $OriginalPic="userfiles/picoriginal/".$node["pic"]; if ($node["stat"]==1) { $chk="checked"; }
	
	$AdminText='<h2>Редактирование: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg1"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";
	$AdminText.="<div class='RoundText'>";
	$AdminText.="<input name='picname' type='hidden' value='".$node["pic"]."'>";
		$AdminText.="<div title='Нажмите для выбора файла' id='Podstava3' class='Podstava4'><input type='file' id='photo' name='photo' accept='image/jpeg,image/gif,image/x-png' onChange='getFileName();' /></div>";
		$AdminText.="<div id='FileName'></div><div style='float:right;'><input type='submit' name='addbutton' id='savebutton' class='SaveButton' value='Загрузить фотографию'></div>";
		$AdminText.=$C5."<div class='Info' align='center'>Вы можете загружать файлы jpg, png, gif до 10М и размером не более 10.000px на 10.000px</div>";
	$AdminText.="</div></form>";
	
	### Параметры фотографии
	if ($node["pic"]!="") $AdminText.='<div class="CenterText"><img src="/userfiles/demots/'.$node['pic'].'.jpg" /></div>';


// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_photo&id=".$id."'>Основная фотография</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_voting&id=".$id."'>Виджет: Голосование</a></div>
	$C5<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_lenta' ".$chk."></td><td><b>Материал опубликован</b></td></tr></table></div>";
	}
	}
}
$_SESSION["Msg1"]="";
?>