<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);
	$sets = explode('|', $raz['sets']);

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$dtags=","; foreach ($P["tags"] as $k=>$v) { $dtags.=$k.","; }
		$ar=explode(".", $P["ddata1"]); $sdata1=mktime($P["ddata2"], $P["ddata3"], $P["ddata4"], $ar[1], $ar[0], $ar[2]); 
		
		$q="UPDATE `".$alias."_albums` SET
		`uid`='".(int)$P['authid']."',
		`data`='".$sdata1."',  
		`name`='".$P["name"]."',
		`text`='".$P["text"]."', 
		`comments`=".$P["comments"].",
		`tags`='".$dtags."',
		`approved`=".(int)$P["approved"].", 
		`photofromusers`=".(int)$P["photofromusers"].", 
		`photoapproval`=".(int)$P["photoapproval"].", 
		`spec`=".(int)$P["concurs"].", 
		`email`='".$P["email"]."'
		WHERE (id='".(int)$id."')";
		
		///echo $q;
		DB($q); $_SESSION["Msg"]="<div class='SuccessDiv'>Запись успешно сохранена!</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}


	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT `".$alias."_albums`.*, `_users`.`role` FROM `".$alias."_albums` LEFT JOIN `_users` ON `_users`.`id`=`".$alias."_albums`.`uid` WHERE (`".$alias."_albums`.`id`='".(int)$id."') LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {

	### Заполнение данных
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);
	if ($node["stat"]==1) { $chk="checked"; }
	if ($node["approved"]==1) { $chk1="checked"; }
	if ($node["photofromusers"]==1) { $chk2="checked"; }
	if ($node["photoapproval"]==1) { $chk3="checked"; }
	if ($node["spec"]==1) { $chk4="checked"; }
	if ($node["comments"]==0) { $c1="selected"; } elseif ($node["comments"]==1) { $c2="selected"; } else { $c3="selected"; }
	$utags=explode(",", trim($node["tags"], ","));
	$usr=array(); $data=DB("SELECT `id`, `nick` FROM `_users` WHERE (`role`>0) ORDER BY `nick` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $usr[$ar["id"]]=$ar["nick"]; endfor;
		
	$AdminText='<h2>Редактирование: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'><script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/adapters/jquery.js'></script>";

	### Основные данные	
	$AdminText.="<div class='RoundText'><table>".'<tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Заголовок альбома<star>*</star></td><td class="LongInput"><textarea name="name" id="name" type="text">'.$node["name"].'</textarea></td><tr>
	<tr class="TRLine0"><td class="LongInput" colspan="2"><textarea name="text" id="textedit">'.$node["text"].'</textarea><script type="text/javascript">$("#textedit").ckeditor({customConfig:"/admin/texteditor/config.js"});</script><div class="C10"></div></td><tr>';
	
	if($node["role"] < 3 && $sets[3]) $AdminText.='<tr class="TRLine1"><td class="VarText">Одобрено</td><td class="NormalInput"><input name="approved" type="checkbox" value="1" '.$chk1.'></td></tr>';
	else $AdminText.='<!--<tr class="TRLine1"><td class="VarText">Одобрено</td><td class="NormalInput"><input name="approved" type="checkbox" value="1" '.$chk1.'></td></tr>-->';
	
	
	$AdminText.='<tr class="TRLine0"><td class="VarName" rowspan="3">Настройки альбома</td><td class="NormalInput"><input name="concurs" type="checkbox" value="1" '.$chk4.'> Закрепить фотоальбом на главной странице</td></tr>
	<tr><td class="NormalInput"><input name="photofromusers" type="checkbox" value="1" '.$chk2.'> Разрешить добавление фотографий пользователям</td></tr>
	<tr><td class="NormalInput"><input name="photoapproval" type="checkbox" value="1" '.$chk3.'> Включить отображение фотографии только после одобрения администратором</td></tr>';
	
	$AdminText.='<tr class="TRLine0"><td class="VarName"></td><td><div class="C5"></div><a href="javascript:void(0);" onclick="ShowSets();" id="ShowSets">Показать дополнительные настройки</a></td><tr>
	<tr class="TRLine1 ShowSets"><td class="VarName">Автор материала</td><td class="LongInput"><div class="sdiv"><select name="authid">'.GetSelected($usr, $node["uid"]).'</select></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">E-mail для уведомлений</td><td class="LongInput"><input name="email" type="text" value=\''.$node["email"].'\'></td><tr>
	<tr class="TRLine1 ShowSets"><td class="VarName">Комментарии</td><td class="LongInput"><div class="sdiv"><select name="comments"><option value="0" '.$c1.'>Чтение и добавление</option><option value="1" '.$c2.'>Только чтение</option><option value="2" '.$c3.'>Запретить комментарии</option></select></div></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Дата создания</td><td class="DateInput">'.GetDataSet($node["data"],"").'</td><tr>
	'."</table></div>";
	
	### Список тэгов публикцаций
	$tags=""; $data=DB("SELECT `id`, `name` FROM `".$alias."_tags` ORDER BY `name` ASC"); $line=1; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
	if (in_array($ar["id"], $utags)) { $chkt="checked"; } else { $chkt=""; }$tags.="<td width='1%'><input name='tags[".$ar["id"]."]' id='tags[".$ar["id"]."]' type='checkbox' class='tags' value='1' $chkt></td>
	<td width='20%'>".$ar["name"]."</td>"; if (($i+1)%3==0) { $tags.="</tr><tr class='TRLine".($line%2)."'>"; $line++; if ($line==3) { $line=1; }} endfor;
	$AdminText.="<h2>Тэги фотоальбома</h2><div class='InfoH2'>Выберите 2-4 темы, самые подходящие по содержанию альбома:</div><div class='RoundText TagsList'><table><tr class='TRLine0'>".$tags."</tr></table></div>";

	### Сохранение
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
	$AdminText.="</form>";

// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='SecondMenu2'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_photos&id=".$id."'>Фотографии</a></div>
	$C5<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_albums' ".$chk."></td><td><b>Альбом опубликован</b></td></tr></table></div>";
	}
	}
}
$_SESSION["Msg"]="";
?>