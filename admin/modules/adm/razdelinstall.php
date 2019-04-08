<?
### МЕНЮ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

$table="_pages"; $pid=(int)$pid;

// СОХРАНЕНИЕ НАСТРОЕК И СОЗДАНИЕ ЗАПИСИ В _pages
	$P=$_POST;

	if (isset($P["addbutton"])) {
		if ($P["dsname"]=="") { $P["dsname"]=$P["dname"]; } if ($P["ddata1"]=="") { $P["ddata1"]=date("d.m.Y"); } if ((int)$P["donpage"]==0) { $P["donpage"]=30; }
		$ar=explode(".", $P["ddata1"]); $sdata=mktime($P["ddata2"], $P["ddata3"], $P["ddata4"], $ar[1], $ar[0], $ar[2]);
		
		### Берем настройки устанавливаемого модуля
		$data=DB("SELECT * FROM `_modules` WHERE `id`=".$P["dpid"]); @mysql_data_seek($data["result"], 0); $md=@mysql_fetch_array($data["result"]);
		
		### создаем записи 
		$res=DB("INSERT INTO `".$table."` (`uid`,`module`, `orderby`, `onpage`, `sets`, `lvl`, `main`,`pid`,`domain`,`design`,`stat`,`inmap`,`data`,`name`,`shortname`,`kw`,`ds`, `isindex`) VALUES
		('".$_SESSION['adminid']."', '".$md["module"]."', '".(int)$P["dsort"]."', '".(int)$P["donpage"]."', '".$md["sets"]."', '".$md["lvl"]."', '0','1','".$P["ddom"]."','".$P["ddes"]."','".(int)$P["dlvl"]."','".$P["dmap"]."',
		'".$sdata."', '".$P["dname"]."','".$P["dsname"]."', '".$dkw."','".$dds."', '".$P["dind"]."')");
		
		$last=DBL(); $autolink="category".$last; $P["dlink"]=str_replace('<autolink>', $autolink, $P["dlink"]);
		DB("UPDATE `".$table."` SET `rate`='".$last."', `link`='".$P["dlink"]."' WHERE  (id='".$last."')");
		if ($P["dind"]=="1") { DB("UPDATE `".$table."` SET `isindex`='0' WHERE (id!='".$last."' && `domain`='".(int)$P["ddom"]."')"); }
	
		### добавляем необходимую таблицу
		$qs=explode(";", $md["sql"]); foreach($qs as $q) { DB(str_replace("[tablename]", $P["dlink"], $q)); }
		@header("location: ?cat=".$P["dlink"]."_list"); exit();
	}
	
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
	$AdminText='<h2>Добавление нового раздела сайта</h2>'.$_SESSION["Msg"];
	
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";
	$AdminText.="<div class='RoundText' id='Tgg'><table>".'<tr class="TRLine0"><td style="width:25%;"></td><td style="width:75%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Название раздела<star>*</star></td><td class="LongInput"><input name="dname" id="dname" type="text" class="JsVerify2"></td><tr>
	<tr class="TRLine0"><td class="VarName">Короткое название</td><td class="LongInput"><input name="dsname" id="dsname" type="text"></td><tr>
	<tr class="TRLine0"><td class="VarText">Адрес раздела<star>*</star></td><td class="LongInput"><input name="dlink" id="dlink" type="text" class="JsVerify" value="<autolink>"></td><tr>
	<tr class="TRLine0"><td class="VarName">Элементов на странице</td><td class="LongInput"><input name="donpage" type="text" value="30"></td><tr>
	<tr class="TRLine0"><td class="VarName">Сортировка элементов</td><td class="LongInput"><div class="sdiv"><select name="dsort">'.GetSelected($ORDERN, 1).'</select></div></td><tr>
	<tr class="TRLine0"><td class="VarName">Ключевые слова (keywords)</td><td class="LongInput"><input name="dkw" type="text"></td><tr>
	<tr class="TRLine0"><td class="VarName">Описание (description)</td><td class="LongInput"><input name="dds" type="text"></td><tr>
	<tr class="TRLine0"><td class="VarName">Используемый модуль</td><td class="LongInput"><div class="sdiv"><select name="dpid">'.GetSelected($pids, $pid).'</select></div></td><tr>	
	<tr class="TRLine0"><td class="VarName">Домен раздела</td><td class="LongInput"><div class="sdiv"><select name="ddom">'.GetSelected($doms, $dom).'</select></div></td><tr>
	<tr class="TRLine0"><td class="VarName">Дизайн раздела</td><td class="LongInput"><div class="sdiv"><select name="ddes">'.GetSelected($dess, $desi).'</select></div></td><tr>		
	<tr class="TRLine0"><td class="VarName">Дата создания</td><td class="DateInput">'.GetDataSet().'</td><tr>'."</table><div class='C5'></div>
	<div class='C15'></div><div class='CenterText'><input type='submit' name='addbutton' id='addbutton' class='SaveButton' value='Добавить раздел'></div></div>";

	### Существующие занятые имена переменных
	$data=DB("SELECT `link` FROM `".$table."`"); $AdminText.="<script type='text/javascript'>var NotAvaliable=new Array("; for ($i=0; $i<$data["total"]; $i++):
	@mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $AdminText.="'".$ar["link"]."',"; endfor; $AdminText.="'error');</script>";

// ПРАВАЯ КОЛОНКА
	$AdminRight="<h2>Настройки раздела</h2><div class='RoundText' id='Tgg'><table>".'
	<tr class="TRLine0"><td class="VarName">Раздел включен</td><td width="1%"><input type="checkbox" id="Inc0" name="dlvl" checked value="1" /></td></tr>
	<tr class="TRLine1"><td class="VarName">Сделать главной</td><td><input type="checkbox" id="Inc2" name="dind" value="1" /></td></tr>
	<tr class="TRLine0"><td class="VarName">В карту сайта</td><td><input type="checkbox" id="Inc3" name="dmap" value="1" checked /></td></tr>
	</table></div></form>';
	
}
$_SESSION["Msg"]="";

		
?>