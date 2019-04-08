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
		
		if ((int)$P['pkmain']==1) { DB("UPDATE `".$alias."_lenta` SET `pkmain`=0"); } 
		
		$q="INSERT INTO `".$alias."_lenta` (`type`,`uid`,`bid`,`pkmain`,`samara`,`pknews`,`pk3st`,`cat`, `name`, `lname`, `sname`, `kw`, `ds`, `cens`, `realinfo`, `comments`, `data`, `astat`, `adata`, `promo`, `onind`, `spec`, `yarss`, `mailrss`, `tavto`, `tags`, `redak`,`gis`,`mailtizer`) VALUES
		('".(int)$P['type']."','".(int)$P['authid']."', '".(int)$P['bid']."', '".(int)$P['pkmain']."', '".(int)$P['samara']."', '".(int)$P['pknews']."','".(int)$P['pk3st']."', '".(int)$P["site"]."', '".str_replace("'", "\'", $P["dname"])."', '".str_replace("'", "\'", $P["lname"])."', '".str_replace("'", "\'", $P["sname"])."', '".str_replace("'", '&#039;', $P["dkw"])."', '".str_replace("'", '&#039;', $P["dds"])."', '".$P["cens"]."', '".str_replace("'", "\'", $P["realinfo"])."', '".$P["comms"]."', '".$sdata1."',
		'".$P["autoon"]."', '".$sdata2."', '".$P["comrs"]."','".$P["ontv"]."', '".$P["spec"]."', '".$P["yarss"]."', '".$P["mailrss"]."', '".$P["tavto"]."', '".$dtags."', '".$P["redak"]."', '".$P["gis"]."', '".$P["mailtizer"]."')";
		
		$_SESSION["Msg"]="<div class='SuccessDiv'>Новая публикация успешно создана!</div>"; $data=DB($q); $last=DBL(); DB("UPDATE `".$alias."_lenta` SET `rate`='".$last."' WHERE  (id='".$last."')");
		DB("INSERT INTO `_lentalog` (`link`, `id`, `uid`, `data`, `ip`, `text`) VALUES ('".$alias."', '".$last."', '".$_SESSION['userid']."', '".time()."', '".$_SERVER['REMOTE_ADDR']."', 'Создание #".$last.": ".str_replace("'", '&#039;', $P["dname"])."')");
		//$ya_request = file_get_contents("http://site.yandex.ru/ping.xml?urls=".urlencode("http://".$VARS['mdomain']."/".$alias."/view/".$last)."&login=v-Disciple&search_id=2043787&key=315057c26103684b3ab8224c10107ad8ef55f963");
		@header("location: ?cat=".$raz["link"]."_edit&id=".$last); exit();
	}
// ВЫВОД ПОЛЕЙ И ФОРМ

	$types=array(0=>"Основная часть - ТЕКСТ", 1=>"Основная часть - ВИДЕО");

	$site=array(); $data=DB("SELECT `id`, `name` FROM `".$alias."_cats` ORDER BY `rate` DESC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $site[$ar["id"]]=$ar["name"]; endfor;
	$usr=array(); $data=DB("SELECT `id`, `nick` FROM `_users` WHERE (`role`>0) ORDER BY `nick` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $usr[$ar["id"]]=$ar["nick"]; endfor;

	$AdminText='<h2>Добавление материала &laquo;'.$raz["shortname"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";

	### Основные данные
	$AdminText.="<div class='RoundText'><table>".'<tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Заголовок материала<star>*</star></td><td class="LongInput"><textarea name="dname" id="dname" type="text"></textarea></td><tr>
	<tr class="TRLine1"><td class="VarText">Пальмочка</td><td class="LongInput"><textarea name="lname" id="lname" type="text"></textarea></td><tr>
	<tr class="TRLine0"><td class="VarText">Крышечка</td><td class="LongInput"><input name="sname" type="text"></td><tr>
	<tr class="TRLine1"><td class="VarText">Тип содержимого</td><td class="LongInput"><div class="sdiv"><select name="type">'.GetSelected($types, 0).'</select></div></td><tr>
	<tr class="TRLine0"><td class="VarText">Ключевые слова (keywords)<star>*</star></td><td class="LongInput"><input name="dkw" type="text"></td><tr>
	<tr class="TRLine1"><td class="VarText">Описание (description)<star>*</star></td><td class="LongInput"><input name="dds" type="text"></td><tr>
	<tr class="TRLine0"><td class="VarText">Категория</td><td class="LongInput"><div class="sdiv"><select name="site">'.GetSelected($site, 0).'</select></div></td><tr>
	<tr class="TRLine0"><td class="VarName"></td><td><a href="javascript:void(0);" onclick="ShowSets();" id="ShowSets">Показать дополнительные настройки</a></td><tr>	
	<tr class="TRLine0 ShowSets"><td class="VarName">Автор материала</td><td class="LongInput"><div class="sdiv"><select name="authid">'.GetSelected($usr, $_SESSION['userid']).'</select></td><tr>
	<tr class="TRLine1 ShowSets"><td class="VarName">Номер BID (<a href="?cat=banners_list" target="_blank">список БС</a>)</td><td class="LongInput"><input name="bid" type="text" value="0" placeholder="Введите номер BID из баннерной системы"></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Цензор материала</td><td class="LongInput"><input name="cens" type="text" value="16+"></td><tr>
	<tr class="TRLine1 ShowSets"><td class="VarName">Источник материала</td><td class="LongInput"><input name="realinfo" type="text"></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Комментарии</td><td class="LongInput"><div class="sdiv"><select name="comms"><option value="0">Чтение и добавление</option><option value="1">Только чтение</option><option value="2">Запретить комментарии</option></select></div></td><tr>	
	<tr class="TRLine1 ShowSets"><td class="VarName">Дата создания</td><td class="DateInput">'.GetDataSet().'</td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Автопубликация</td><td class="DateInput">'.GetDataSet(0, 1).' включить таймер: <input type="checkbox" name="autoon" id="autoon" value="1"></td><tr>
	'."</table></div>";
	
	### Экспорт материала
	$AdminText.="<div class='RoundText TagsList'><table>
	<tr class='TRLine0'>
		<td width='1%'><input name='ontv' id='ontv' type='checkbox' value='1'></td><td width='20%'>В <b>«Телевизор»</b></td>
		<td width='1%'><input name='spec' id='spec' type='checkbox' value='1'></td><td width='20%'>В <b>«Спецразмещение»</b></td>
		<td width='1%'><input name='gis' id='gis' type='checkbox' value='1'></td><td width='20%'>Анонс <b>«Правая колонка»</b></td>
	</tr>
		<tr class='TRLine1'>
		<td width='1%'><input name='yarss' id='yarss' type='checkbox' value='1' checked></td><td width='20%'>Отправить в <b>RSS</b></td>
		<td width='1%'><input name='mailrss' id='mailrss' type='checkbox' value='1' checked></td><td width='20%'>Отправить в <b>FULL RSS</b></td>
		<td width='1%'><input name='mailtizer' id='mailtizer' type='checkbox' value='1' checked></td><td width='20%'>Отправить в <b>WIDGET</b></td>
	</tr>
	<tr class='TRLine0'>
		<td width='1%'><input name='pkmain' id='pkmain' type='checkbox' value='1'></td><td width='20%'>ProKazan: в телевизор</td>
		<td width='1%'><input name='pknews' id='pknews' type='checkbox' value='1'></td><td width='20%'>ProKazan: в колонку новостей</td>
		<td width='1%'><input name='pk3st' id='pk3st' type='checkbox' value='1'></td><td width='20%'>ProKazan: три новости в блоке</td>
	</tr>
	<tr class='TRLine1'>
		<td width='1%'><input name='comrs' id='comrs' type='checkbox' value='1'></td><td width='20%'>Коммерческая публикация</td>
		<td width='1%'><input name='samara' id='samara' type='checkbox' value='1'></td><td width='20%'>Запрет Самары</td>
	</tr>
	</table></div>";
	
	### Список тэгов публикцаций
	$tags=""; $data=DB("SELECT `id`, `name` FROM `_tags` ORDER BY `name` ASC"); $line=1; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); 
	$tags.="<td width='1%'><input name='tags[".$ar["id"]."]' id='tags[".$ar["id"]."]' type='checkbox' class='tags' value='1'></td><td width='20%'>".$ar["name"]."</td>";
	if (($i+1)%3==0) { $tags.="</tr><tr class='TRLine".($line%2)."'>"; $line++; if ($line==3) { $line=1; }} endfor;
	$AdminText.=$C5."<div class='InfoH2' align='center'>Тэги публикации</div><div class='RoundText TagsList' style='max-height:500px;'><table><tr class='TRLine0'>".$tags."</tr></table></div>";
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Создать запись'></div>";

// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br><div class='SecondMenu2'><a href='".$_SERVER["REQUEST_URI"]."'>Основные настройки</a></div><br>После сохранения основных настроек, вы сможете перейти к наполнению публикации контентом, загрузить фотографии и править остальные параметры записи.
	<div class='C20'></div><div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Создать запись'></div></form>";
}




	}
$_SESSION["Msg"]="";
?>