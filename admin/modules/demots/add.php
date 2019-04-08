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
		$ar=explode(".", $P["ddata11"]); $sdata2=mktime($P["ddata21"], $P["ddata31"], $P["ddata41"], $ar[1], $ar[0], $ar[2]);
		
		$q="INSERT INTO `".$alias."_lenta` (`uid`, `name`, `kw`, `ds`, `cens`, `realinfo`, `comments`, `data`, `astat`, `adata`, `votingend`, `promo`, `onind`, `spec`, `yarss`, `mailrss`, `tavto`, `tags`, `redak`,`gis`,`mailtizer`)
		VALUES ('".$_SESSION['userid']."', '".$P["dname"]."', '".$P["dkw"]."', '".$P["dds"]."', '".$P["cens"]."', '".$P["realinfo"]."', '".$P["comms"]."', '".$sdata1."',
		'".$P["autoon"]."', '".$sdata2."', '".(time() + 7 * 24 * 60 * 60)."', '".$P["comrs"]."','".$P["ontv"]."', '".$P["spec"]."', '".$P["yarss"]."', '".$P["mailrss"]."', '".$P["tavto"]."', '".$dtags."', '".$P["redak"]."', '".$P["gis"]."', '".$P["mailtizer"]."')";
		
		$_SESSION["Msg"]="<div class='SuccessDiv'>Новая публикация успешно создана!</div>";
		$data=DB($q); $last=DBL(); DB("UPDATE `".$alias."_lenta` SET `rate`='".$last."' WHERE  (id='".$last."')");
		@header("location: ?cat=".$raz["link"]."_edit&id=".$last); exit();
	}
	

// ВЫВОД ПОЛЕЙ И ФОРМ	
	$AdminText='<h2>Добавление материала &laquo;'.$raz["shortname"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";

	### Основные данные
	$AdminText.="<div class='RoundText'><table>".'<tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Заголовок материала<star>*</star></td><td class="LongInput"><input name="dname" id="dname" type="text" class="JsVerify2"></td><tr>
	
	
	<tr class="TRLine0"><td class="VarName"></td><td><a href="javascript:void(0);" onclick="ShowSets();" id="ShowSets">Показать дополнительные настройки</a></td><tr>	
	
	<tr class="TRLine1 ShowSets"><td class="VarName">Ключевые слова (keywords)</td><td class="LongInput"><input name="dkw" type="text"></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Описание (description)</td><td class="LongInput"><input name="dds" type="text"></td><tr>
	<tr class="TRLine1 ShowSets"><td class="VarName">Цензор материала</td><td class="LongInput"><input name="cens" type="text" value="16+"></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Источник материала</td><td class="LongInput"><input name="realinfo" type="text"></td><tr>
	<tr class="TRLine1 ShowSets"><td class="VarName">Комментарии</td><td class="LongInput"><div class="sdiv"><select name="comms"><option value="0">Чтение и добавление</option><option value="1">Только чтение</option><option value="2">Запретить комментарии</option></select></div></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Дата создания</td><td class="DateInput">'.GetDataSet().'</td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Автопубликация</td><td class="DateInput">'.GetDataSet(0, 1).'  включить таймер: <input type="checkbox" name="autoon" id="autoon" value="1"></td><tr>
	'."</table></div>";
	
	### Экспорт материала
	$AdminText.="<h2>Отображение и экспорт материала</h2><div class='RoundText TagsList'><table>
	<tr class='TRLine0'>
		<td width='1%'><input name='comrs' id='comrs' type='checkbox' value='1'></td><td width='20%'>Коммерческая новость</td>
		<td width='1%'><input name='ontv' id='ontv' type='checkbox' value='1'></td><td width='20%'>Поместить в телевизор</td>
		<td width='1%'><input name='spec' id='spec' type='checkbox' value='1'></td><td width='20%'>Спец. размещение</td>
	</tr>
	<tr class='TRLine1'>
		<td width='1%'><input name='yarss' id='yarss' type='checkbox' value='1'></td><td width='20%'>Отправить в Яндекс RSS</td>
		<td width='1%'><input name='mailrss' id='mailrss' type='checkbox' value='1'></td><td width='20%'>Отправить в Mail RSS</td>
		<td width='1%'><input name='tavto' id='tavto' type='checkbox' value='1'></td><td width='20%'>Отправить в тизер TAVTO</td>
	</tr>
	<tr class='TRLine0'>
		<td width='1%'><input name='mailtizer' id='mailtizer' type='checkbox' value='1'></td><td width='20%'>Отправить в тизер Mail</td>
		<td width='1%'><input name='redak' id='redak' type='checkbox' value='1'></td><td width='20%'>Редакционная колонка</td>
		<td width='1%'><input name='gis' id='gis' type='checkbox' value='1'></td><td width='20%'>Отправлять в ГисМетео</td>		
	</tr>
	</table></div>";
	
	### Список тэгов публикцаций
	$tags=""; $data=DB("SELECT `id`, `name` FROM `_tags` ORDER BY `name` ASC"); $line=1; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); 
	$tags.="<td width='1%'><input name='tags[".$ar["id"]."]' id='tags[".$ar["id"]."]' type='checkbox' class='tags' value='1'></td><td width='20%'>".$ar["name"]."</td>";
	if (($i+1)%3==0) { $tags.="</tr><tr class='TRLine".($line%2)."'>"; $line++; if ($line==3) { $line=1; }} endfor;
	$AdminText.="<h2>Тэги публикации</h2><div class='InfoH2'>Выберите 2-4 темы, самые подходящие по смыслу публикации:</div><div class='RoundText TagsList'><table><tr class='TRLine0'>".$tags."</tr></table></div>";

	### Сохранение
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Создать запись'></div>";
	$AdminText.="</form>";

// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br><div class='SecondMenu2'><a href='".$_SERVER["REQUEST_URI"]."'>Основные настройки</a></div><br>После сохранения основных настроек, вы сможете перейти к наполнению публикации контентом, загрузить фотографии и править остальные параметры записи.";
}




	}
$_SESSION["Msg"]="";
?>