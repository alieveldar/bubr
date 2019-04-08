<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]); 

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$dtags=","; foreach ($P["tags"] as $k=>$v) { $dtags.=$k.","; }
		$ar=explode(".", $P["ddata1"]); $sdata1=mktime($P["ddata2"], $P["ddata3"], $P["ddata4"], $ar[1], $ar[0], $ar[2]);
		
		$q="INSERT INTO `".$alias."_albums` (`data`, `stat`, `name`, `text`, `uid`, `comments`, `tags`, `approved`, `photofromusers`, `photoapproval`, `spec`, `email`)
		VALUES ('".$sdata1."', ".(int)$P["stat"].", '".$P["name"]."', '".$P["text"]."', ".(int)$P['authid'].", ".$P["comments"].", '".$dtags."', ".(int)$P["approved"].", ".(int)$P["photofromusers"].", ".(int)$P["photoapproval"].", ".(int)$P["concurs"].", '".$P["email"]."')";
		
		$_SESSION["Msg"]="<div class='SuccessDiv'>Новая публикация успешно создана!</div>";
		$data=DB($q); $last=DBL(); DB("UPDATE `".$alias."_albums` SET `rate`='".$last."' WHERE  (id='".$last."')");
		@header("location: ?cat=".$raz["link"]."_edit&id=".$last); exit();
	}
	

// ВЫВОД ПОЛЕЙ И ФОРМ
	
	$AdminText='<h2>Добавление материала &laquo;'.$raz["shortname"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'><script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/adapters/jquery.js'></script>";
	$usr=array(); $data=DB("SELECT `id`, `nick` FROM `_users` WHERE (`role`>0) ORDER BY `nick` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $usr[$ar["id"]]=$ar["nick"]; endfor;
	### Основные данные
	$AdminText.="<div class='RoundText'><table>".'<tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Заголовок альбома<star>*</star></td><td class="LongInput"><textarea name="name" id="name" type="text"></textarea></td><tr>
	<tr class="TRLine0"><td class="LongInput" colspan="2"><textarea name="text" id="textedit"></textarea><script type="text/javascript">$("#textedit").ckeditor({customConfig:"/admin/texteditor/config.js"});</script></td><tr>
	
	<tr class="TRLine0"><td class="VarName" rowspan="4">Настройки альбома</td><td class="NormalInput"><input name="stat" type="checkbox" value="1"> Включить отображение фотоальбома</td></tr>
	<tr><td class="NormalInput"><input name="concurs" type="checkbox" value="1"> Закрепить фотоальбом на главной странице</td></tr>
	<tr><td class="NormalInput"><input name="photofromusers" type="checkbox" value="1"> Разрешить добавление фотографий пользователям</td></tr>
	<tr><td class="NormalInput"><input name="photoapproval" type="checkbox" value="1" checked> Включить отображение фотографии только после одобрения администратором</td></tr>

	<tr class="TRLine0"><td class="VarName"></td><td><a href="javascript:void(0);" onclick="ShowSets();" id="ShowSets">Показать дополнительные настройки</a></td><tr>
	
	<tr class="TRLine1 ShowSets"><td class="VarName">Автор материала</td><td class="LongInput"><div class="sdiv"><select name="authid">'.GetSelected($usr, $_SESSION['userid']).'</select></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">E-mail для уведомлений</td><td class="LongInput"><input name="email" id="name" type="text" class="JsVerify2"></td><tr>
	<tr class="TRLine1 ShowSets"><td class="VarName">Комментарии</td><td class="LongInput"><div class="sdiv"><select name="comments"><option value="0">Чтение и добавление</option><option value="1">Только чтение</option><option value="2">Запретить комментарии</option></select></div></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Дата создания</td><td class="DateInput">'.GetDataSet().'</td><tr>
	'."</table></div>";
	
	### Список тэгов публикцаций
	$tags=""; $data=DB("SELECT `id`, `name` FROM `".$alias."_tags` ORDER BY `name` ASC"); $line=1; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); 
	$tags.="<td width='1%'><input name='tags[".$ar["id"]."]' id='tags[".$ar["id"]."]' type='checkbox' class='tags' value='1'></td><td width='20%'>".$ar["name"]."</td>";
	if (($i+1)%3==0) { $tags.="</tr><tr class='TRLine".($line%2)."'>"; $line++; if ($line==3) { $line=1; }} endfor;
	$AdminText.="<h2>Тэги фотоальбома</h2><div class='InfoH2'>Выберите 2-4 темы, самые подходящие по содержанию альбома:</div><div class='RoundText TagsList'><table><tr class='TRLine0'>".$tags."</tr></table></div>";

	### Сохранение
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Создать запись'></div>";
	$AdminText.="</form>";

// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br><div class='SecondMenu2'><a href='".$_SERVER["REQUEST_URI"]."'>Основные настройки</a></div><br>После сохранения основных настроек, вы сможете перейти к наполнению публикации контентом, загрузить фотографии и править остальные параметры записи.";
}




	}
$_SESSION["Msg"]="";
?>