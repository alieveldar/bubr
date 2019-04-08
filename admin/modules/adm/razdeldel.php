<?
### УДАЛЕНИЕ КЭША
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	if ($_SESSION["userrole"]<4) {
		$AdminText=ATextReplace('AdmModuleDelete'); $GLOBAL["error"]=1; 
	} else {
		$tstart=GetMicroTime(); $AdminText='<h2>Удаление раздела сайта</h2>'; $tf=0; $tw=0; $er=0; $images=array(); $coms=array(0); 
		$data=DB("SELECT `link`, `module`, `name` FROM `_pages` WHERE (`id`='".(int)$id."') LIMIT 1"); @mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]); 
		if ($data["total"]!=1) { $er=1; $AdminText.="<div class='ErrorDiv'>Запрашиваемый раздел сайта не найден!</div><div class='C10'></div>"; } else {
		
		$data=DB("SELECT `source`, `name` FROM `_modules` WHERE (`module`='".$raz["module"]."') LIMIT 1"); @mysql_data_seek($data["result"], 0); $mod=@mysql_fetch_array($data["result"]); 
		if ($data["total"]!=1) { $er=1; $AdminText.="<div class='ErrorDiv'>Не найден используемый модуль раздела!</div><div class='C10'></div>"; } else {
			$tables=explode(",", str_replace(array(" ", "\r\n", "\r", "\n"), ",", str_replace("[tablename]", $raz["link"], $mod["source"]))); foreach($tables as $key=>$val){ if (empty($val)){ unset($tables[$key]); }}
			
			### Собираем картинки таблиц модуля
			foreach($tables as $table) { $tmp2=DB("SELECT `id` FROM `".$table."`"); $mtotal=$mtotal+$tmp2["total"]; $tmp=DB("SELECT `pic` FROM `".$table."` WHERE (`pic`!='')"); for ($i=0; $i<$tmp["total"]; $i++): @mysql_data_seek($tmp["result"], $i); $t=@mysql_fetch_array($tmp["result"]); $images[]=$t["pic"]; endfor; }
			### Собираем картинки комментариев
			$com=DB("SELECT `id` FROM `_comments` WHERE (`link`='".$raz["link"]."')"); for ($i=0; $i<$com["total"]; $i++): @mysql_data_seek($com["result"], $i); $t=@mysql_fetch_array($com["result"]); $coms[]=$t["id"]; endfor; 
			if (count($coms)>1) { $tmp=DB("SELECT `pic` FROM `_commentf` WHERE (`pid` IN (".implode(", ", $coms).") && `pic`!='')"); for ($i=0; $i<$tmp["total"]; $i++): @mysql_data_seek($tmp["result"], $i); $t=@mysql_fetch_array($tmp["result"]); $images[]=$t["pic"]; endfor; }
			### Собираем картинки фотоальбомов
			$map=DB("SELECT `pic` FROM `_widget_pics` WHERE (`link`='".$raz["link"]."')"); for ($i=0; $i<$map["total"]; $i++): @mysql_data_seek($map["result"], $i); $t=@mysql_fetch_array($map["result"]); if ($t["pic"]!="") { $images[]=$t["pic"]; } endfor;
			### Собираем картинки карты событий
			$map=DB("SELECT `pic`,`icon` FROM `_widget_eventmap` WHERE (`link`='".$raz["link"]."')"); for ($i=0; $i<$map["total"]; $i++): @mysql_data_seek($map["result"], $i); $t=@mysql_fetch_array($map["result"]); if ($t["pic"]!="") { $images[]=$t["pic"]; } if ($t["icon"]!="") { $images[]=$t["icon"]; } endfor;
			
			### Удаляем картинки физически
			foreach($images as $i=>$file) { foreach($GLOBAL['AutoPicPaths'] as $k=>$v) { if (is_file("../userfiles/".$k."/".$file)) { $tw+=filesize("../userfiles/".$k."/".$file); @unlink("../userfiles/".$k."/".$file); }}}
			### Удаляем из БД всех таблиц
			foreach($tables as $table) { DB("DROP TABLE `".$table."`"); }
			### Удаляем из БД  комментарии вложения и Удаляем из БД  комментарии
			DB("DELETE FROM `_commentf` WHERE (`pid` IN (".implode(", ", $coms)."))");
			DB("DELETE FROM `_comments` WHERE (`link`='".$raz["link"]."')");
			### Удаляем из папки cache
			RemoveDir("../cache/".$raz["link"]);
			### Удаляем из БД caption cache
			DB("DELETE FROM `_captions` WHERE (`page` LIKE '%".$raz["link"]."%')");
			### Удаляем из БД  _search
			DB("DELETE FROM `_search` WHERE (`link`='".$raz["link"]."')");
			### Удаляем из БД  _tracker
			DB("DELETE FROM `_tracker` WHERE (`link`='".$raz["link"]."')");
			### Удаляем из БД  карты
			DB("DELETE FROM `_widget_eventmap` WHERE (`link`='".$raz["link"]."')");
			### Удаляем из БД голосования
			$vt=DB("select `id` from `_widget_votes` where (`link`='".$raz["link"]."')");
			DB("DELETE from `_widget_votes` where (`link`='".$raz["link"]."')");
			### Удаляем из БД _pages
			DB("DELETE FROM `_pages` WHERE (`link`='".$raz["link"]."' && `module`!='')");
			
			$tend=GetMicroTime();
			$AdminText.="<div class='SuccessDiv'>Раздел сайта «<b>".$raz["name"]."</b>» успешно удален!</div><div class='C10'></div>";
			$AdminText.="<div class='InfoDiv'>Использованный модуль: <b>".$mod["name"]."</b> (".$raz["module"].")</div>";
			$AdminText.="<div class='InfoDiv'>Удаленные таблицы: <b>".implode(", ",$tables)."</b></div>";
			$AdminText.="<div class='InfoDiv'>Удаленные публикации: <b>".(int)$mtotal."</b>; Удалено комментариев: <b>".((int)count($coms)-1)."</b></div>";
			$AdminText.="<div class='InfoDiv'>Голосов удалено: <b>".(int)$vt["total"]."</b>; Cобытий на карте удалено: <b>".(int)$map["total"]."</b></div>";
			$AdminText.="<div class='InfoDiv'>Файлов удалено: <b>".count($images)."</b>; Объем файлов: <b>".round($tw/1024/1024, 3)." МБ</b></div>";
			$AdminText.="<div class='InfoDiv'>Затрачено: <b>".round($tend-$tstart, 3)." сек.</b></div><div class='C15'></div>";
			$AdminText.=AIco(45)."Вернуться: <a href='/admin/'>Стартовая страница</a>";
			$AdminText.=$C15."<div style='padding:10px; line-height:10px; font-size:9px; color:#888; border:1px dotted #777; border-radius:10px;'>".$GLOBAL["log"]."</div>";
			
		}}
	}
}

function RemoveDir($path) { global $tp, $tf, $AdminText, $tw, $er; if(file_exists($path) && is_dir($path)) { $dirHandle=opendir($path); while (false !== ($file=readdir($dirHandle))) { if ($file!='.' && $file!='..') { $tmpPath=$path.'/'.$file; chmod($tmpPath, 0777); if (is_dir($tmpPath)) { RemoveDir($tmpPath); } else { if(file_exists($tmpPath)) { $tw+=filesize($tmpPath); unlink($tmpPath); $tf++; }}}} closedir($dirHandle); if(file_exists($path)){ rmdir($path); }} else { $AdminText=ATextReplace('ClearCache-Error'); $er=1; }}
?>