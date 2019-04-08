<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { 
	$AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); 
	$GLOBAL["error"]=1;
	} else {
	@mysql_data_seek($data["result"], 0);
	$raz=@mysql_fetch_array($data["result"]);

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		//$dtags=","; 
		//foreach ($P["tags"] as $k=>$v) { 
		//$dtags.=$k.","; 
		//}
		//$ar=explode(".", $P["ddata1"]);
		//$sdata1=mktime($P["ddata2"], $P["ddata3"], $P["ddata4"], $ar[1], $ar[0], $ar[2]); 
		//$ar=explode(".", $P["ddata11"]); 
		//$sdata2=mktime($P["ddata21"], $P["ddata31"], $P["ddata41"], $ar[1], $ar[0], $ar[2]); 
		//запрос обновления данных в бд 
		/*$q="UPDATE `".$alias."_lenta` SET 
		`uid`='".(int)$P['authid']."',
		`name`='".str_replace("'", '&#039;', $P["dname"])."',
		`cat`='".$P["site"]."',
		`kw`='".str_replace("'", '&#039;', $P["dkw"])."', 
		`ds`='".str_replace("'", '&#039;', $P["dds"])."', 
		`cens`='".$P["cens"]."',
		`realinfo`='".str_replace("'", '&#039;', $P["realinfo"])."', 
		`comments`='".$P["comms"]."', 
		`data`='".$sdata1."',
		`astat`='".$P["autoon"]."', 
		`adata`='".$sdata2."', 
		`promo`='".$P["comrs"]."', 
		`onind`='".$P["ontv"]."', 
		`spec`='".$P["spec"]."', 
		`yarss`='".$P["yarss"]."', 
		`mailrss`='".$P["mailrss"]."', 
		`tavto`='".$P["tavto"]."', 
		`tags`='".$dtags."',
		`redak`='".$P["redak"]."', 
		`gis`='".$P["gis"]."', 
		`mailtizer`='".$P["mailtizer"]."'
		WHERE (id='".(int)$id."')";*/
		
		$q="UPDATE `".$alias."_lenta` SET `authoremail`='".$P['authemail']."',`hideshow`='".$P['showhide']."',`showtouser`='".$P['showuser']."',`savebd`='".$P['savebd']."' WHERE (id='".(int)$id."')";
	
		
		DB($q); $_SESSION["Msg"]="<div class='SuccessDiv'>Запись успешно сохранена!</div>"; 
		@header("location: ".$_SERVER["REQUEST_URI"]); 
		exit();
	}

	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT * FROM `".$alias."_lenta` WHERE (`id`='".(int)$id."') LIMIT 1"); 
	if ($data["total"]!=1) { 
	$AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; 
	} else {
		
	### Заполнение данных
	@mysql_data_seek($data["result"], 0); 
	$node=@mysql_fetch_array($data["result"]);
	if ($node["hideshow"]==1) { $chk="checked"; }
	if ($node["showtouser"]==1) { $chk1="checked"; }
	if ($node["savebd"]==1) { $chk2="checked"; }
	

	
	
		
	$AdminText='<h2>Редактирование: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";	
	//$ya_request = file_get_contents("http://site.yandex.ru/ping.xml?urls=".$VARS['mdomain']."/".$alias."/view/".$node["id"]."&login=v-Disciple&search_id=2043787&key=315057c26103684b3ab8224c10107ad8ef55f963");
	//echo $ya_request."<hr>";

	### Основные данные
	$AdminText.="<div class='RoundText'><table>".'<tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Email для результата :</td><td class="LongInput"><input name="authemail" id="dname" type="text" value=\''.$node["authoremail"].'\' maxlength="80"></td><tr>
	
	
	
	<tr><div class="VarText"><input type="checkbox" name="showhide" id="showhide" value="1" '.$chk.'>Открывать только по ссылке</div><tr>
	<tr><div class="VarText"><input type="checkbox" name="showuser" id="showuser" value="1" '.$chk1.'>Показывать результаты пользователю</div><tr>
	<tr><div class="VarText"><input type="checkbox" name="savebd" id="savebd" value="1" '.$chk2.'>Сохранять результат пользователя</div><tr>
    
	
	'."</table></div>";
	
	### Экспорт материала
	/*$AdminText.="<h2>Отображение и экспорт материала</h2><div class='RoundText TagsList'><table>
	<tr class='TRLine0'>
		<td width='1%'><input name='comrs' id='comrs' type='checkbox' value='1' $chk2></td><td width='20%'>Коммерческая новость</td>
		<td width='1%'><input name='ontv' id='ontv' type='checkbox' value='1' $chk3></td><td width='20%'>Поместить в телевизор</td>
		<td width='1%'><input name='spec' id='spec' type='checkbox' value='1' $chk4></td><td width='20%'>Спец. размещение</td>
	</tr>
	<tr class='TRLine1'>
		<td width='1%'><input name='yarss' id='yarss' type='checkbox' value='1' $chk5></td><td width='20%'>Отправить в Яндекс RSS</td>
		<td width='1%'><input name='mailrss' id='mailrss' type='checkbox' value='1' $chk6></td><td width='20%'>Отправить в Mail RSS</td>
		<td width='1%'><input name='tavto' id='tavto' type='checkbox' value='1' $chk7></td><td width='20%'>Отправить в тизер TAVTO</td>
	</tr>
	<tr class='TRLine0'>
		<td width='1%'><input name='mailtizer' id='mailtizer' type='checkbox' value='1' $chk8></td><td width='20%'>Отправить в тизер Mail</td>
		<td width='1%'><input name='redak' id='redak' type='checkbox' value='1' $chk9></td><td width='20%'>Редакционная колонка</td>
		<td width='1%'><input name='gis' id='gis' type='checkbox' value='1' $chk10></td><td width='20%'>Отправлять в ГисМетео</td>		
	</tr>
	</table></div>";
	*/
	### Список тэгов публикцаций
	/*
	$tags=""; $data=DB("SELECT `id`, `name` FROM `_tags` ORDER BY `name` ASC"); $line=1; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
	if (in_array($ar["id"], $utags)) { $chkt="checked"; } else { $chkt=""; }$tags.="<td width='1%'><input name='tags[".$ar["id"]."]' id='tags[".$ar["id"]."]' type='checkbox' class='tags' value='1' $chkt></td>
	<td width='20%'>".$ar["name"]."</td>"; if (($i+1)%3==0) { $tags.="</tr><tr class='TRLine".($line%2)."'>"; $line++; if ($line==3) { $line=1; }} endfor;
	$AdminText.="<h2>Тэги публикации</h2><div class='InfoH2'>Выберите 2-4 темы, самые подходящие по смыслу публикации:</div><div class='RoundText TagsList'><table><tr class='TRLine0'>".$tags."</tr></table></div>";
     */
	### Сохранение
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";

	// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные данные</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_sets&id=".$id."'>Настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_photo&id=".$id."'>Основная фотография</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_voting&id=".$id."'>Вопросы и ответы</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_results&id=".$id."'>Результаты</a></div>
	
	$C5<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_lenta' ".$chk."></td><td><b>Материал опубликован</b></td></tr></table></div>";
	$AdminRight.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div></form>";
	}
	}
}
$_SESSION["Msg"]="";
?>