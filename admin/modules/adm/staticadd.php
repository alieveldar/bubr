<?
### МЕНЮ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

$table="_pages"; $pid=(int)$pid;
### Родителем по умолчанию назначается "карта сайта"
### Если pid взят из $_GET  - получаем по умолчанию дизайн и домен
if ($pid==0) { $pid=1; $dom=0; $des="0"; $prelink="<autolink>"; } else {
	$data=DB("SELECT `domain`, `design`, `link` FROM `_pages` WHERE (id='".$pid."') LIMIT 1"); @mysql_data_seek($data["result"],0);
	$ar=@mysql_fetch_array($data["result"]); $dom=$ar["domain"]; $desi=$ar["design"]; $prelink=$ar["link"]."/<autolink>";
}

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;

	if (isset($P["addbutton"])) {
		if ($P["dsname"]=="") { $P["dsname"]=$P["dname"]; } if ($P["ddata1"]=="") { $P["ddata1"]=date("d.m.Y"); } $dtags=","; foreach ($P["tags"] as $k=>$v) { $dtags.=$k.","; }
		$ar=explode(".", $P["ddata1"]); $sdata=mktime($P["ddata2"], $P["ddata3"], $P["ddata4"], $ar[1], $ar[0], $ar[2]); 
		
		$res=DB("INSERT INTO `".$table."` (`uid`,`main`,`pid`,`domain`,`design`,`stat`,`inmap`,`data`,`name`,`shortname`,`text`,`tags`,`kw`,`ds`,`sets`) VALUES ('".$_SESSION['adminid']."', '0','".
		(int)$P["dpid"]."','".(int)$P["ddom"]."','".$P["ddes"]."','".(int)$P["dlvl"]."','".$P["dmap"]."', '".$sdata."', '".$P["dname"]."','".$P["dsname"]."', '".$P["PostText"]."', '".$dtags."','".$dkw."','".$dds."', '".$P["dcom"]."')");
		
		$last=DBL(); DB("UPDATE `".$table."` SET `rate`='".$last."' WHERE  (id='".$last."')");
		$autolink="page".$last; $P["dlink"]=str_replace('<autolink>', $autolink, $P["dlink"]); DB("UPDATE `".$table."` SET `link`='".$P["dlink"]."' WHERE  (id='".$last."')");
		if ($P["dind"]=="1") { DB("UPDATE `".$table."` SET `isindex`='0' WHERE (`domain`='".(int)$P["ddom"]."')"); DB("UPDATE `".$table."` SET `isindex`='1' WHERE  (id='".$last."')"); }
		$_SESSION["Msg"]="<div class='SuccessDiv'>Cтраница добавлена! <a href='/".$P["dlink"]."/' target='_blank'>Просмотр страницы</a></div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}
	
// ДАННЫЕ СЕЛЕКТОВ
	### Список дизайнов
	$dess["0"]="- Основной шаблон дизайна -"; $data=DB("SELECT `folder`, `name` FROM `_designs` ORDER BY `name` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i);
	$ar=@mysql_fetch_array($data["result"]); $sid=$ar["folder"]; $dess[$sid]=$ar["name"]; endfor;
	### Список доменов
	$doms[0]="- Основной домен сайта -"; $data=DB("SELECT `id`, `name` FROM `_domains` ORDER BY `name` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i);
	$ar=@mysql_fetch_array($data["result"]); $sid=$ar["id"]; $doms[$sid]=$ar["name"]; endfor;
	### Список родителей
	$pids[1]="- Корень сайта -"; $data=DB("SELECT `id`, `name` FROM `_pages` WHERE (main='0' && module='') ORDER BY `name` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i);
	$ar=@mysql_fetch_array($data["result"]); $sid=$ar["id"]; $pids[$sid]=$ar["name"]; endfor;
	
// ФОРМА ДОБАВЛЕНИЯ
	$AdminText='<h2>Добавление статичной страницы</h2>'.$_SESSION["Msg"];
	
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";
	$AdminText.="<div class='RoundText' id='Tgg'><table>".'<tr class="TRLine0"><td style="width:25%;"></td><td style="width:75%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Заголовок страницы<star>*</star></td><td class="LongInput"><input name="dname" id="dname" type="text" class="JsVerify2"></td><tr>
	<tr class="TRLine0"><td class="VarName"></td><td><a href="javascript:void(0);" onclick="ShowSets();" id="ShowSets">Показать дополнительные настройки</a></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Короткий заголовок</td><td class="LongInput"><input name="dsname" id="dsname" type="text"></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Адрес страницы</td><td class="LongInput"><input name="dlink" id="dlink" type="text" class="JsVerify" value="'.$prelink.'"></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Ключевые слова (keywords)</td><td class="LongInput"><input name="dkw" type="text"></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Описание (description)</td><td class="LongInput"><input name="dds" type="text"></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Родитель страницы</td><td class="LongInput"><div class="sdiv"><select name="dpid">'.GetSelected($pids, $pid).'</select></div></td><tr>	
	<tr class="TRLine0 ShowSets"><td class="VarName">Домен страницы</td><td class="LongInput"><div class="sdiv"><select name="ddom">'.GetSelected($doms, $dom).'</select></div></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Дизайн страницы</td><td class="LongInput"><div class="sdiv"><select name="ddes">'.GetSelected($dess, $desi).'</select></div></td><tr>
	<tr class="TRLine0 ShowSets"><td class="VarName">Дата создания</td><td class="DateInput">'.GetDataSet().'</td><tr>'."</table><div class='C5'></div>
	<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/filemanager/ajex.js'></script>
	<textarea name='PostText' id='textedit' style='outline:none;'>".$ar["text"]."</textarea><script type='text/javascript'>var editor=CKEDITOR.replace('textedit'); AjexFileManager.init({ returnTo: 'ckeditor', editor: editor});</script>
	<div class='C15'></div><div class='CenterText'><input type='submit' name='addbutton' id='addbutton' class='SaveButton' value='Добавить страницу'></div></div>";

	### Существующие занятые имена переменных
	$data=DB("SELECT `link` FROM `".$table."`"); $AdminText.="<script type='text/javascript'>var NotAvaliable=new Array("; for ($i=0; $i<$data["total"]; $i++):
	@mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $AdminText.="'".$ar["link"]."',"; endfor; $AdminText.="'error');</script>";
	### Список тэгов публикцаций
	$tags=""; $data=DB("SELECT `id`, `name` FROM `_tags` ORDER BY `name` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); 
	$tags.="<input name='tags[".$ar["id"]."]' id='tags[".$ar["id"]."]' type='checkbox' class='tags' value='1'> ".$ar["name"].$C5; endfor;

// ПРАВАЯ КОЛОНКА
	$AdminRight="<h2>Настройки страницы</h2><div class='RoundText' id='Tgg'><table>".'
	<tr class="TRLine0"><td class="VarName">Опубликовано</td><td width="1%"><input type="checkbox" id="Inc0" name="dlvl" checked value="1" /></td></tr>
	<tr class="TRLine1"><td class="VarName">Комментирование</td><td width="1%"><input type="checkbox" id="Inc1" name="dcom" value="1" /></td></tr>
	<tr class="TRLine0"><td class="VarName">Сделать главной</td><td><input type="checkbox" id="Inc2" name="dind" value="1" /></td></tr>
	<tr class="TRLine1"><td class="VarName">В карту сайта</td><td><input type="checkbox" id="Inc3" name="dmap" value="1" checked /></td></tr>
	</table></div><h2>Тэги публикации</h2><div class="ScrollText" id="Tgg2">'.$tags.'</div></form>';
	
}
$_SESSION["Msg"]="";

		
?>