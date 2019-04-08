<?

### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// РАЗДЕЛ
	/*$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { 
	$AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); 
	$GLOBAL["error"]=1; 
	} else {
	@mysql_data_seek($data["result"], 0); 
	$raz=@mysql_fetch_array($data["result"]); 
*/
// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		
		//Запрос сохранения полей
		
		
		//$q="INSERT INTO `".$alias."_answers`(`pid`,`name`,`types`,`text`) VALUES('".$id."','".$P['qtitle']."','".$P['qtypes']."','".$P['qtext']."')";
		
		
		
		//$data=DB($q); 
		//$last=DBL();
		//$_SESSION["Msg"]="<div class='SuccessDiv'>Ваш вопрос успешно создан ! <a href='?cat=".$alias."_addAnswers&id=".$last."'>Добавить варианты ответов</a></div>";
		 
		//DB("UPDATE `".$alias."_lenta` SET `rate`='".$last."' WHERE  (id='".$last."')");
		//$ya_request = file_get_contents("http://site.yandex.ru/ping.xml?urls=".urlencode("http://".$VARS['mdomain']."/".$alias."/view/".$last)."&login=v-Disciple&search_id=2043787&key=315057c26103684b3ab8224c10107ad8ef55f963");
		@header("location: ?cat=".$alias."_addAnswers&id=".$id); 
		exit();
	}
// ВЫВОД ПОЛЕЙ И ФОРМ

    $G=$_GET;
    $sets=DB("SELECT `id`,`name`,`types` FROM `".$alias."_queries` where `id`='".(int)$id."'");
	
	 
	var_dump($sets);
	@mysql_data_seek($sets["result"], 0); 
	$ar=@mysql_fetch_array($sets["result"]);
	
	
	$AdminText='<h2>Добавление материала &laquo;'.$ar["name"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";
	
	switch ($ar["types"]) {
    case 0://формируем строку Поле для ввода текста
     		  $AdminText.="<tr ><td><input type='text' value='Введите свой тескт'>  </td> </tr>";
	
        break;
    case 1:
        $AdminText.="";//формируем строку Ответ - картинка 
        break;
    case 2:
        $AdminText.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top; padding-top:15px;">Ответы</td><td class="AlmostLongInput Answers">';
		if($sets["total"]){
		for ($i=0; $i<$sets["total"]; $i++){
			$AdminText.='<div><input name="votes['.$G["id"].']" type="text" value=\''.$ar["name"].'\'>';
			if($i >= 2) 
			$AdminText.='<a title="Удалить" onclick="RemoveField($(this))" href="javascript:void(0);"><img style="margin:2px 0 0 3px; width:14px;" valign="middle" src="/admin/images/icons/exit.png"></a>';
			$AdminText.=$C5.'</div>';
		}
		}//формируем строку Единичный выбор из вариантов
        break;
	case 3:
        $AdminText="";//формируем строку Множественный выбор из вариантов
        break;

	
	};
	
	
	
	
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Создать '></div>";
	$AdminText.="</form>";

   
	### Основные данные
	//$AdminText.="<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/filemanager/ajex.js'></script>";
	/*$AdminText.="<div class='RoundText'><table>".'<tr class="TRLine0">
	<td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Заголовок вопроса<star>*</star></td>
	<td class="LongInput"><input name="qtitle" id="qtitle" type="text" class="JsVerify2" maxlength="80"></td></tr>
	

	<tr><td>Тип вопроса :</td><td><select name="qtypes" id="qtypes">'."1".'</select></td></tr>
	
	<tr><td colspan="2">'."<h2>Основное содержание публикации</h2><textarea name='qtext' id='textedit' style='outline:none;'>"."2"."</textarea>
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
	
	*/
	
	
	}




	
$_SESSION["Msg"]="";
?>