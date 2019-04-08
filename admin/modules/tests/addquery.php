<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { 
	$AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; 
	} else {
	@mysql_data_seek($data["result"], 0); 
	$raz=@mysql_fetch_array($data["result"]); 

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		/*$dtags=","; foreach ($P["tags"] as $k=>$v) {
		$dtags.=$k.","; 
		}
		$ar=explode(".", $P["ddata1"]); 
		$sdata1=mktime($P["ddata2"], $P["ddata3"], $P["ddata4"], $ar[1], $ar[0], $ar[2]); 
		$ar=explode(".", $P["ddata11"]); 
		$sdata2=mktime($P["ddata21"], $P["ddata31"], $P["ddata41"], $ar[1], $ar[0], $ar[2]); 
		*/
		//Запрос сохранения полей
		/*
		$q="INSERT INTO `".$alias."_queries` (`pid`, `cat`, `name`, `kw`, `ds`, `cens`, `realinfo`, `comments`, `data`, `astat`, `adata`, `promo`, `onind`, `spec`, `yarss`, `mailrss`, `tavto`, `tags`, `redak`,`gis`,`mailtizer`)
		VALUES ('".(int)$P['authid']."', '".(int)$P["site"]."', '".str_replace("'", '&#039;', $P["dname"])."', '".str_replace("'", '&#039;', $P["dkw"])."', '".str_replace("'", '&#039;', $P["dds"])."', '".$P["cens"]."', '".str_replace("'", '&#039;', $P["realinfo"])."', '".$P["comms"]."', '".$sdata1."',
		'".$P["autoon"]."', '".$sdata2."', '".$P["comrs"]."','".$P["ontv"]."', '".$P["spec"]."', '".$P["yarss"]."', '".$P["mailrss"]."', '".$P["tavto"]."', '".$dtags."', '".$P["redak"]."', '".$P["gis"]."', '".$P["mailtizer"]."')";
		
		*/
		
		$q="INSERT INTO `".$alias."_queries`(`pid`,`name`,`types`,`text`) VALUES('".$id."','".$P['qtitle']."','".$P['qtypes']."','".$P['qtext']."')";
		$data=DB($q); 
		$last=DBL();
		$_SESSION["Msg"]="<div class='SuccessDiv'>Ваш вопрос успешно создан ! <a href='?cat=".$alias."_aedit&id=".$last."'>Добавить варианты ответов</a></div>";
		 
		DB("UPDATE `".$alias."_lenta` SET `rate`='".$last."' WHERE  (id='".$last."')");
		//$ya_request = file_get_contents("http://site.yandex.ru/ping.xml?urls=".urlencode("http://".$VARS['mdomain']."/".$alias."/view/".$last)."&login=v-Disciple&search_id=2043787&key=315057c26103684b3ab8224c10107ad8ef55f963");
		@header("location: ?cat=".$alias."_addquery&id=".$id); 
		exit();
	}
// ВЫВОД ПОЛЕЙ И ФОРМ

	/*$site=array(); $data=DB("SELECT `id`, `name` FROM `".$alias."_cats` ORDER BY `rate` DESC"); 
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); 
	$ar=@mysql_fetch_array($data["result"]);
	$site[$ar["id"]]=$ar["name"]; endfor;
	$usr=array(); $data=DB("SELECT `id`, `nick` FROM `_users` WHERE (`role`>0) ORDER BY `nick` ASC"); 
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i);
	$ar=@mysql_fetch_array($data["result"]); 
	$usr[$ar["id"]]=$ar["nick"]; 
	endfor; */
    $answers=array(0=>"Поле для ввода текста",1=>"Ответ - картинка",2=>"Единичный выбор из вариантов",3=>"Множественный выбор из вариантов");
	$AdminText='<h2>Добавление материала &laquo;'.$raz["shortname"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";

	### Основные данные
	$AdminText.="<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/filemanager/ajex.js'></script>";
	$AdminText.="<div class='RoundText'><table>".'<tr class="TRLine0">
	<td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Заголовок вопроса<star>*</star></td>
	<td class="LongInput"><input name="qtitle" id="qtitle" type="text" class="JsVerify2" maxlength="80"></td></tr>
	

	<tr><td>Тип вопроса :</td><td><select name="qtypes" id="qtypes">'.GetSelected($answers, $node["types"]).'</select></td></tr>
	
	<tr><td colspan="2">'."<h2>Основное содержание публикации</h2><textarea name='qtext' id='textedit' style='outline:none;'>".$node["text"]."</textarea>
		<script type='text/javascript'>var editor=CKEDITOR.replace('textedit'); AjexFileManager.init({ returnTo: 'ckeditor', editor: editor});</script>".'
		
	
	
</td></tr>'."</table></div>";

//<tr class="TRLine0"><td class="VarText"><a href="?cat='.$alias.'_addAnswers&id='.$id.'">Добавить варианты ответов</a><td></tr> 
    
//<div class="sdiv">
	### Сохранение
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Создать '></div>";
	$AdminText.="</form>";

// ПРАВАЯ КОЛОНКА
	//$AdminRight="<br><br><div class='SecondMenu2'><a href='".$_SERVER["REQUEST_URI"]."'>Основные настройки</a></div><br>После сохранения основных настроек, вы сможете перейти к наполнению публикации контентом, загрузить фотографии и править остальные параметры записи.";
    ///$AdminRight="<br><br><div class='SecondMenu'><a href='?cat=".$alias."_addAnswers&id=".$id."'>Добавить варианты ответов</a></div>";
	
	
	
	
	}




	}
$_SESSION["Msg"]="";
?>