<?
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
$table="_pages"; $pid=(int)$pid;

// СОХРАНЕНИЕ НАСТРОЕК И СОЗДАНИЕ ЗАПИСИ В _pages
	$P=$_POST; $psets=""; $Settings="";
	
	//$data=DB("SELECT `module`, `sets` FROM `_pages` WHERE (`id`='".$id."') LIMIT 1"); @mysql_data_seek($data["result"], 0); $sets=@mysql_fetch_array($data["result"]);

	if (isset($P["addbutton"])) {

		### Если модуль использует дополнительные настройки, получаем их и заносим в переменную $psets
		if (isset($P["settings"])) { $psets=implode("|", $P["settings"]); }
		
		### Основные или общие настройки, одинаковые для всех модулей
		if ($P["dsname"]=="") { $P["dsname"]=$P["dname"]; } if ($P["ddata1"]=="") { $P["ddata1"]=date("d.m.Y"); } if ((int)$P["donpage"]==0) { $P["donpage"]=30; }
		$ar=explode(".", $P["ddata1"]); $sdata=mktime($P["ddata2"], $P["ddata3"], $P["ddata4"], $ar[1], $ar[0], $ar[2]);
	
		### сохраняем основные и дополнительные настройки
		$res=DB("
		UPDATE `".$table."` SET
		`domain`='".$P["ddom"]."',
		`design`='".$P["ddes"]."',
		`stat`='".(int)$P["dlvl"]."',
		`inmap`='".$P["dmap"]."',
		`data`='".$sdata."',
		`name`='".$P["dname"]."',
		`shortname`='".$P["dsname"]."',
		`orderby`='".(int)$P["dsort"]."',
		`onpage`='".(int)$P["donpage"]."',
		`kw`='".$dkw."',
		`ds`='".$dds."',
		`isindex`='".$P["dind"]."',
		`sets`='".$psets."'
		WHERE (`id`='".$id."')");
		if ($P["dind"]=="1") { DB("UPDATE `".$table."` SET `isindex`='0' WHERE (`id`!='".$id."' && `domain`='".(int)$P["ddom"]."')"); }
		
		$_SESSION["Msg"]="<div class='SuccessDiv'>Настройки раздела сохранены</div>";
		header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}
	
	
### Получние данных по текущему элементу
$data=DB("SELECT * FROM `_pages` WHERE (`id`='".$id."') LIMIT 1"); 
if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, $table1); $GLOBAL["error"]=1; } else {
@mysql_data_seek($data["result"], 0); $sets=@mysql_fetch_array($data["result"]);

	
// ДАННЫЕ СЕЛЕКТОВ
	### Список дизайнов
	$dess["0"]="- Основной шаблон дизайна -"; $data=DB("SELECT `folder`, `name` FROM `_designs` ORDER BY `name` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i);
	$ar=@mysql_fetch_array($data["result"]); $sid=$ar["folder"]; $dess[$sid]=$ar["name"]; endfor;
	### Список доменов
	$doms[0]="- Основной домен сайта -"; $data=DB("SELECT `id`, `name` FROM `_domains` ORDER BY `name` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i);
	$ar=@mysql_fetch_array($data["result"]); $sid=$ar["id"]; $doms[$sid]=$ar["name"]; endfor;
	### Список родителей
	$data=DB("SELECT `id`, `name` , `module` FROM `_modules` ORDER BY `name` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i);
	$ar=@mysql_fetch_array($data["result"]); $sid=$ar["id"]; $pids[$sid]=$ar["name"]." (тип: ".$ar["module"].")"; endfor;
	
// ФОРМА ДОБАВЛЕНИЯ
	$AdminText='<h2>Настройки раздела &laquo;'.$sets["shortname"].'&raquo;</h2>'.$_SESSION["Msg"];
	
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";
	$AdminText.="<div class='RoundText' id='Tgg'><table>".'<tr class="TRLine0"><td style="width:25%;"></td><td style="width:75%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Название раздела<star>*</star></td><td class="LongInput"><input name="dname" id="dname" type="text" class="JsVerify2" value=\''.$sets["name"].'\'></td><tr>
	<tr class="TRLine0"><td class="VarName">Короткое название</td><td class="LongInput"><input name="dsname" id="dsname" type="text" value=\''.$sets["shortname"].'\'></td><tr>
	<tr class="TRLine0"><td class="VarName">Элементов на странице</td><td class="LongInput"><input name="donpage" type="text" value=\''.$sets["onpage"].'\'></td><tr>
	<tr class="TRLine0"><td class="VarName">Сортировка элементов</td><td class="LongInput"><div class="sdiv"><select name="dsort">'.GetSelected($ORDERN, $sets["orderby"]).'</select></div></td><tr>
	<tr class="TRLine0"><td class="VarName">Ключевые слова (keywords)</td><td class="LongInput"><input name="dkw" type="text" value=\''.$sets["kw"].'\'></td><tr>
	<tr class="TRLine0"><td class="VarName">Описание (description)</td><td class="LongInput"><input name="dds" type="text" value=\''.$sets["ds"].'\'></td><tr>
	<tr class="TRLine0"><td class="VarName">Домен раздела</td><td class="LongInput"><div class="sdiv"><select name="ddom">'.GetSelected($doms, $sets["domain"]).'</select></div></td><tr>
	<tr class="TRLine0"><td class="VarName">Дизайн раздела</td><td class="LongInput"><div class="sdiv"><select name="ddes">'.GetSelected($dess, $sets["design"]).'</select></div></td><tr>		
	<tr class="TRLine0"><td class="VarName">Дата создания</td><td class="DateInput">'.GetDataSet($sets["data"]).'</td><tr>'."</table><div class='C5'></div></div>$alertmsg<div class='C5'></div>";
	
	### Если модуль использует дополнительные настройки подгружаем файл, который за них отвечает
	### для каждого модуля можно создать файл собственных настроек, который должен быть в папке модуля и называться settings.php
	### в этом файле настроек должна быть определена переменная $Settings, в которой есть кусок кода с полями настроек все поля - элементы массива $settings[]
	if (is_file($ROOT."/admin/modules/".$sets["module"]."/settings.php")) {
		require($ROOT."/admin/modules/".$sets["module"]."/settings.php");
	} else {
		$alertmsg="<div style='color:#999; font-size:9px; text-align:center; margin-top:-10px;'>Файл дополнительных настроек: $sets[module]/settings.php на найден. Переменная ".'$Settings'." не определена. Массив ".'$settings[]'." не доступен.</div>";
	}
	
	if ($Settings!="") { $AdminText.='<h2>Дополнительные настройки раздела</h2>'."<div class='RoundText' id='Tgg'>".$Settings."</div>"; }
	
	### Кнопка сохранить настройки
	$AdminText.="<div class='C10'></div><div class='CenterText'><input type='submit' name='addbutton' id='addbutton' class='SaveButton' value='Сохранить настройки'></div>";
		

// ПРАВАЯ КОЛОНКА
	if ($sets["stat"]==1) { $p1="checked"; } if ($sets["isindex"]==1) { $p3="checked"; } if ($sets["inmap"]==1) { $p4="checked"; }
	$AdminRight="<h2>Настройки раздела</h2><div class='RoundText' id='Tgg'><table>".'
	<tr class="TRLine0"><td class="VarName">Раздел включен</td><td width="1%"><input type="checkbox" id="Inc0" name="dlvl" value="1" '.$p1.' /></td></tr>
	<tr class="TRLine0"><td class="VarName">Сделать главной</td><td><input type="checkbox" id="Inc2" name="dind" value="1" '.$p3.' /></td></tr>
	<tr class="TRLine1"><td class="VarName">В карту сайта</td><td><input type="checkbox" id="Inc3" name="dmap" value="1" '.$p4.' /></td></tr>
	</table><div class="C15"></div>Путь раздела:  <a href="/'.$sets["link"].'/" target="_blank" style="color:#069;">/'.$sets["link"].'/</a><div class="C5"></div>
	Тип модуля: <a href="?cat=adm_razdelnew" style="color:#069;">'.$sets["module"].'</a></div></form>'."<div class='C30'></div><div style='text-align:center;'><div class='LinkR'>
	<a href='javascript:void(0);' onclick='LinkBlank(\"Удалить «".$sets["name"]."»?\",\"<b>Внимание!</b> Удаление раздела приведет к полному удалению всех публикаций,<br>голосований, фотографий и комментариев, принадлежащих данному разделу.<br><br>Восстановление данных будет невозможно!\", \"?cat=adm_razdeldel&id=".$id."\")'>Удалить этот раздел сайта</a></div></div>";
	
}}
$_SESSION["Msg"]="";		
?>