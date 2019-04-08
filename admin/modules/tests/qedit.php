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
		
	$q="UPDATE  `".$alias."_queries` SET `name`='".$P["qname"]."',`types`='".(int)$P["qtypes"]. "',`text`='".$P["qtext"]."'WHERE id='".(int)$id."'";
	$_SESSION["Msg"]="<div class='SuccessDiv'>Вопрос  успешно отредактирован! <a href ='?cat=".
	$alias.	"_voting&id=".(int)$_GET["pid"]."'>Перейти к списку вопросов</a>   </div>";
		
		
		
		
		
		
		$data=DB($q); 
		//$last=DBL(); 
		//DB("UPDATE `".$alias."_lenta` SET `rate`='".$last."' WHERE  (id='".$last."')");
		//$ya_request = file_get_contents("http://site.yandex.ru/ping.xml?urls=".urlencode("http://".$VARS['mdomain']."/".$alias."/view/".$last)."&login=v-Disciple&search_id=2043787&key=315057c26103684b3ab8224c10107ad8ef55f963");
		@header("location: ?cat=".$alias."_qedit&id=".$id);
		exit();
	}
	}
// ВЫВОД ПОЛЕЙ И ФОРМ
/*
	$site=array(); $data=DB("SELECT `id`, `name` FROM `".$alias."_cats` ORDER BY `rate` DESC"); 
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); 
	$ar=@mysql_fetch_array($data["result"]);
	$site[$ar["id"]]=$ar["name"]; endfor;
	$usr=array(); $data=DB("SELECT `id`, `nick` FROM `_users` WHERE (`role`>0) ORDER BY `nick` ASC"); 
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i);
	$ar=@mysql_fetch_array($data["result"]); 
	$usr[$ar["id"]]=$ar["nick"]; 
	endfor;
*/
	//$AdminText='<h2>Добавление материала &laquo;'.$raz["shortname"].'&raquo;</h2>'.$_SESSION["Msg"];
	//$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";

	### Основные данные
	/*$AdminText.="<div class='RoundText'><table>".'<tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Заголовок <star>*</star></td><td class="LongInput"><input name="dname" id="dname" type="text" class="JsVerify2" maxlength="80"></td><tr>
	<tr class="TRLine1"><td class="VarText">Категория</td><td class="LongInput"><div class="sdiv"><select name="site">'.GetSelected($site, 0).'</select></div></td><tr>
	
	<tr class="TRLine0"><td class="VarName"></td><td><a href="javascript:void(0);" onclick="ShowSets();" id="ShowSets">Показать дополнительные настройки</a></td><tr>	
	
	<tr class="TRLine1 ShowSets"><td class="VarName">Автор </td><td class="LongInput"><div class="sdiv"><select name="authid">'.GetSelected($usr, $_SESSION['userid']).'</select></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Ключевые слова (keywords)</td><td class="LongInput"><input name="dkw" type="text"></td><tr>
	<tr class="TRLine1 ShowSets"><td class="VarName">Описание (description)</td><td class="LongInput"><input name="dds" type="text"></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Цензор материала</td><td class="LongInput"><input name="cens" type="text" value="16+"></td><tr>
	<tr class="TRLine1 ShowSets"><td class="VarName">Источник </td><td class="LongInput"><input name="realinfo" type="text"></td><tr>
	
	
	
	
	
	<tr class="TRLine0 ShowSets"><td class="VarName">Комментарии</td><td class="LongInput"><div class="sdiv"><select name="comms"><option value="0">Чтение и добавление</option><option value="1">Только чтение</option><option value="2">Запретить комментарии</option></select></div></td><tr>	
	<tr class="TRLine1 ShowSets"><td class="VarName">Дата создания</td><td class="DateInput">'.GetDataSet().'</td><tr>
	
	<tr class="TRLine0 ShowSets"><td class="VarName">Автопубликация</td><td class="DateInput">'.GetDataSet(0, 1).' включить таймер: <input type="checkbox" name="autoon" id="autoon" value="1"></td><tr>
	'."</table></div>";
	*/
	### Экспорт материала
	/*$AdminText.="<h2>Отображение и экспорт материала</h2><div class='RoundText TagsList'><table>
	<tr class='TRLine0'>
		<td width='1%'><input name='comrs' id='comrs' type='checkbox' value='1'></td><td width='20%'>Коммерческая новость</td>
		<td width='1%'><input name='ontv' id='ontv' type='checkbox' value='1'></td><td width='20%'>Поместить в телевизор</td>
		<td width='1%'><input name='spec' id='spec' type='checkbox' value='1'></td><td width='20%'>Спец. размещение</td>
	</tr>
	<tr class='TRLine1'>
		<td width='1%'><input name='yarss' id='yarss' type='checkbox' value='1'></td><td width='20%'>Отправить в Яндекс RSS</td>
		<td width='1%'><input name='mailrss' id='mailrss' type='checkbox' value='1' checked></td><td width='20%'>Отправить в Mail RSS</td>
		<td width='1%'><input name='tavto' id='tavto' type='checkbox' value='1'></td><td width='20%'>Отправить в тизер TAVTO</td>
	</tr>
	<tr class='TRLine0'>
		<td width='1%'><input name='mailtizer' id='mailtizer' type='checkbox' value='1'></td><td width='20%'>Отправить в тизер Mail</td>
		<td width='1%'><input name='redak' id='redak' type='checkbox' value='1'></td><td width='20%'>Редакционная колонка</td>
		<td width='1%'><input name='gis' id='gis' type='checkbox' value='1'></td><td width='20%'>Отправлять в ГисМетео</td>		
	</tr>
	</table></div>";
	*/
	$answers=array(0=>"Поле для ввода текста",1=>"Ответ - картинка",2=>"Единичный выбор из вариантов",3=>"Множественный выбор из вариантов");
	
	
	
	$data=DB("SELECT `id`,`pid`, `name`, `text`, `types` FROM `".$alias."_queries` WHERE (`id`='".(int)$id."') "); 
		
		@mysql_data_seek($data["result"], 0); 
		$node=@mysql_fetch_array($data["result"]); 
	    //echo 'I"m Writing : '.$node["name"];
	    
		
		
		
	$AdminText='<h2>Редактирование: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."&pid=".$node["pid"]."' enctype='multipart/form-data' method='post'>";	
	
	$AdminText.="<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/filemanager/ajex.js'></script>";
		
	
	$AdminText.="<div class='RoundText'><table>".
	'<tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Заголовок вопроса<star>*</star></td>
	<td class="LongInput"><input name="qname" id="qname" type="text" class="JsVerify2" maxlength="80" value="'.$node["name"].'"></td><tr>
	
	<tr><td>Тип вопроса :</td><td class="LongInput"><div class="sdiv"><select name="qtypes" id="qtypes">'.GetSelected($answers, $node["types"]).'</select></div></td></tr>'.
	
	
	'<tr><td colspan="2">'.
	"<h2>Содержание вопроса</h2><textarea name='qtext' id='textedit' style='outline:none;'>".$node["text"]."</textarea>
		<script type='text/javascript'>var editor=CKEDITOR.replace('textedit'); AjexFileManager.init({ returnTo: 'ckeditor', editor: editor});</script>".'
		
	</td></tr>'
	
	.
	
	"</table></div>";
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	### Сохранение
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные '></div>";
	$AdminText.="</form>";

// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br><div class='SecondMenu2'><a href='?cat=".$alias."_aedit&id=".$id."' title='Редактировать ответы'>Редактировать ответы</a></div><br>.";

$_SESSION["Msg"]="";
?>