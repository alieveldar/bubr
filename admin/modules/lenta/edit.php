<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]); $bst="";

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$dtags=","; foreach ($P["tags"] as $k=>$v) { $dtags.=$k.","; }
		$ar=explode(".", $P["ddata1"]); $sdata1=mktime($P["ddata2"], $P["ddata3"], $P["ddata4"], $ar[1], $ar[0], $ar[2]); 
		$ar=explode(".", $P["ddata11"]); $sdata2=mktime($P["ddata21"], $P["ddata31"], $P["ddata41"], $ar[1], $ar[0], $ar[2]); 

		if ((int)$P['pkmain']==1) { DB("UPDATE `".$alias."_lenta` SET `pkmain`=0"); }
				
		$q="UPDATE `".$alias."_lenta` SET 
		`uid`='".(int)$P['authid']."',
		`bid`='".(int)$P['bid']."',
		`pkmain`='".(int)$P['pkmain']."',
		`pknews`='".(int)$P['pknews']."',
		`pk3st`='".(int)$P['pk3st']."',
		`samara`='".(int)$P['samara']."',
		`name`='".str_replace("'", "\'", $P["dname"])."',
		`lname`='".str_replace("'", "\'", $P["lname"])."',
		`sname`='".str_replace("'", "\'", $P["sname"])."',
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
		`type`='".(int)$P["type"]."', 
		`onind`='".$P["ontv"]."', 
		`spec`='".$P["spec"]."', 
		`yarss`='".$P["yarss"]."', 
		`mailrss`='".$P["mailrss"]."', 
		`tavto`='".$P["tavto"]."', 
		`tags`='".$dtags."',
		`redak`='".$P["redak"]."', 
		`gis`='".$P["gis"]."', 
		`mailtizer`='".$P["mailtizer"]."'
		WHERE (id='".(int)$id."')";
		DB("INSERT INTO `_lentalog` (`link`, `id`, `uid`, `data`, `ip`, `text`) VALUES ('".$alias."', '".$id."', '".$_SESSION['userid']."', '".time()."', '".$_SERVER['REMOTE_ADDR']."', 'Сохранение (name): ".str_replace("'", "\'", $P["dname"])."')");
		DB($q); $_SESSION["Msg"]="<div class='SuccessDiv'>Запись успешно сохранена!</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

	$types=array(0=>"Основная часть - ТЕКСТ", 1=>"Основная часть - ВИДЕО");
	
	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT * FROM `".$alias."_lenta` WHERE (`id`='".(int)$id."') LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
		
	### Заполнение данных
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);
	if ($node["stat"]==1) { $chk="checked"; }		if ($node["astat"]==1) { $chk1="checked"; }		if ($node["promo"]==1) { $chk2="checked"; }
	if ($node["onind"]==1) { $chk3="checked"; }		if ($node["spec"]==1) { $chk4="checked"; }		if ($node["yarss"]==1) { $chk5="checked"; }
	if ($node["mailrss"]==1) { $chk6="checked"; }	if ($node["tavto"]==1) { $chk7="checked"; }		if ($node["mailtizer"]==1) { $chk8="checked"; }
	if ($node["redak"]==1) { $chk9="checked"; }		if ($node["gis"]==1) { $chk10="checked"; }	
	if ($node["pkmain"]==1) { $chk11="checked"; }	if ($node["pknews"]==1) { $chk12="checked"; }	if ($node["pk3st"]==1) { $chk13="checked"; }
		
	if ($node["samara"]==1) { $chk14="checked"; }
	
	if ($node["comments"]==0) { $c1="selected"; } elseif ($node["comments"]==1) { $c2="selected"; } else { $c3="selected"; } $utags=explode(",", trim($node["tags"], ","));
	$site=array(); $data=DB("SELECT `id`, `name` FROM `".$alias."_cats` ORDER BY `rate` DESC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $site[$ar["id"]]=$ar["name"]; endfor;
	$usr=array(); $data=DB("SELECT `id`, `nick` FROM `_users` WHERE (`role`>0 || `id`='".$node["uid"]."') ORDER BY `nick` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $usr[$ar["id"]]=$ar["nick"]; endfor;
	
	/* заявка из баннерной системы */
	if ($node["bid"]!=0) { $bb=DB("SELECT `_banners_items`.*, `_banners_orders`.`id` as `zidd` FROM `_banners_items` LEFT JOIN `_banners_orders` ON `_banners_items`.`zid`=`_banners_orders`.`zid` WHERE (`_banners_items`.`id`='".(int)$node["bid"]."') LIMIT 1"); if ($bb["total"]!=1) { $bst="<div style='font-size:10px; color:red; margin-top:3px;'>Не найдено записей в баннерной системе с BID=".(int)$node["bid"]."</div>";
	} else { @mysql_data_seek($bb["result"],0); $br=@mysql_fetch_array($bb["result"]); $dr1=ToRusData($br["datafrom"]); $dr2=ToRusData($br["datato"]); $bst="<div style='font-size:11px; color:#777; margin-top:4px;'>BID = <a href='?cat=banners_edit&id=".(int)$node["bid"]."' target='_blank'>".(int)$node["bid"]."</a>; ZID = <a href='?cat=banners_editorder&id=".(int)$br["zidd"]."' target='_blank'>".(int)$br["zid"]."</a>;	DATA = ".$dr1[2]." - ".$dr2[2]."</div>"; }}
		
	$AdminText='<h2>Редактирование: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg"]."<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";	
	$AdminText.="<div class='RoundText'><table>".'<tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Заголовок материала<star>*</star></td><td class="LongInput"><textarea name="dname" id="dname" type="text">'.$node["name"].'</textarea></td><tr>
	<tr class="TRLine1"><td class="VarText">Пальмочка</td><td class="LongInput"><textarea name="lname" id="lname" type="text">'.$node["lname"].'</textarea></td><tr>
	<tr class="TRLine0"><td class="VarText">Крышечка</td><td class="LongInput"><input name="sname" type="text" value=\''.$node["sname"].'\'></td><tr>
	<tr class="TRLine1"><td class="VarText">Тип содержимого</td><td class="LongInput"><div class="sdiv"><select name="type">'.GetSelected($types, $node["type"]).'</select></div></td><tr>
	<tr class="TRLine0"><td class="VarText">Ключевые слова (keywords)<star>*</star></td><td class="LongInput"><input name="dkw" type="text" value=\''.$node["kw"].'\'></td><tr>
	<tr class="TRLine1"><td class="VarText">Описание (description)<star>*</star></td><td class="LongInput"><input name="dds" type="text" value=\''.$node["ds"].'\'></td><tr>
	<tr class="TRLine0"><td class="VarText">Категория</td><td class="LongInput"><div class="sdiv"><select name="site">'.GetSelected($site, $node["cat"]).'</select></div></td><tr>';
	
	$AdminText.='<tr class="TRLine0"><td colspan="2"><h2 style="text-align:center; font-size:16px;"><a href="/'.$alias.'/pre/'.$id.'" target="_blank">Предварительный просмотр всех видов верстки фотографии</a></h2></td></tr>';
	
	$AdminText.='<tr class="TRLine0"><td class="VarName"></td><td><a href="javascript:void(0);" onclick="ShowSets();" id="ShowSets">Показать дополнительные настройки</a></td><tr>	
	<tr class="TRLine0 ShowSets"><td class="VarName">Автор материала</td><td class="LongInput"><div class="sdiv"><select name="authid">'.GetSelected($usr, $node["uid"]).'</select></td><tr>
		<tr class="TRLine1 ShowSets"><td class="VarName">Номер BID (<a href="?cat=banners_list" target="_blank">список БС</a>)</td>
		<td class="LongInput"><input name="bid" type="text" value="'.$node["bid"].'" placeholder="Введите номер BID из баннерной системы">'.$bst.'</td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Цензор материала</td><td class="LongInput"><input name="cens" type="text" value=\''.$node["cens"].'\'></td><tr>
	<tr class="TRLine1 ShowSets"><td class="VarName">Источник материала</td><td class="LongInput"><input name="realinfo" type="text" value=\''.$node["realinfo"].'\'></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Комментарии</td><td class="LongInput">
	<div class="sdiv"><select name="comms"><option value="0" '.$c1.'>Чтение и добавление</option><option value="1" '.$c2.'>Только чтение</option><option value="2" '.$c3.'>Запретить комментарии</option></select></div></td><tr>	
	<tr class="TRLine1 ShowSets"><td class="VarName">Дата создания</td><td class="DateInput">'.GetDataSet($node["data"],"").'</td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Автопубликация</td><td class="DateInput">'.GetDataSet($node["adata"], 1).' включить таймер: <input type="checkbox" name="autoon" id="autoon" value="1" '.$chk1.'></td><tr>
	'."</table></div>";
	
	### Экспорт материала
	$AdminText.="<div class='RoundText TagsList'><table>
	<tr class='TRLine0'>
		<td width='1%'><input name='ontv' id='ontv' type='checkbox' value='1' $chk3></td><td width='20%'>В <b>«Телевизор»</b></td>
		<td width='1%'><input name='spec' id='spec' type='checkbox' value='1' $chk4></td><td width='20%'>В <b>«Спецразмещение»</b></td>
		<td width='1%'><input name='gis' id='gis' type='checkbox' value='1' $chk10></td><td width='20%'>Анонс <b>«Правая колонка»</b></b></td>
	</tr>
	<tr class='TRLine1'>
		<td width='1%'><input name='yarss' id='yarss' type='checkbox' value='1' $chk5></td><td width='20%'>Отправить в <b>RSS</b></td>
		<td width='1%'><input name='mailrss' id='mailrss' type='checkbox' value='1' $chk6></td><td width='20%'>Отправить в <b>FULL RSS</b></td>
		<td width='1%'><input name='mailtizer' id='mailtizer' type='checkbox' value='1' $chk8></td><td width='20%'>Отправить в <b>WIDGET</b></td>
	</tr>
	<tr class='TRLine0'>
		<td width='1%'><input name='pkmain' id='pkmain' type='checkbox' value='1' $chk11></td><td width='20%'>ProKazan: в телевизор</td>
		<td width='1%'><input name='pknews' id='pknews' type='checkbox' value='1' $chk12></td><td width='20%'>ProKazan: в колонку новостей</td>
		<td width='1%'><input name='pk3st' id='pk3st' type='checkbox' value='1' $chk13></td><td width='20%'>ProKazan: три новости в блоке</td>
	</tr>
	<tr class='TRLine1'>
		<td width='1%'><input name='comrs' id='comrs' type='checkbox' value='1' $chk2></td><td width='20%'>Коммерческая публикация</td>
		<td width='1%'><input name='samara' id='samara' type='checkbox' value='1' $chk14></td><td width='20%'>Запрет Самары</td>
	</tr>
	</table></div>";
	
	### Список тэгов публикцаций
	$tags=""; $data=DB("SELECT `id`, `name` FROM `_tags` ORDER BY `name` ASC"); $line=1; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
	if (in_array($ar["id"], $utags)) { $chkt="checked"; } else { $chkt=""; }$tags.="<td width='1%'><input name='tags[".$ar["id"]."]' id='tags[".$ar["id"]."]' type='checkbox' class='tags' value='1' $chkt></td>
	<td width='20%'>".$ar["name"]."</td>"; if (($i+1)%3==0) { $tags.="</tr><tr class='TRLine".($line%2)."'>"; $line++; if ($line==3) { $line=1; }} endfor;
	$AdminText.=$C5."<div class='InfoH2' align='center'>Тэги публикации</div><div class='RoundText TagsList' style='max-height:500px;'><table><tr class='TRLine0'>".$tags."</tr></table></div>";

	### Сохранение
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";

	// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_lenta' ".$chk."></td><td><b>Опубликовано</b></td></tr></table></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_photo&id=".$id."'>Основная фотография</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_report&id=".$id."'>Виджет: Фото-отчет</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_album&id=".$id."'>Виджет: Фото-альбом</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_film&id=".$id."'>Виджет: Видео-вставка</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_voting&id=".$id."'>Виджет: Голосование</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_pretext&id=".$id."'>Виджет: Постскриптум</a></div>
	<br><div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div><br><br>
	<div class='SecondMenu2'><a href='/$alias/view/$id/' target='_blank'>Просмотр на сайте</a></div></form>";
	if ($_SESSION['userrole']>2) { $AdminRight.="<div class='SecondMenu'><a href='?cat=".$alias."_log&id=".$id."'>Лог редактирования записи</a></div>"; }
	
	}}
}
$_SESSION["Msg"]="";
?>