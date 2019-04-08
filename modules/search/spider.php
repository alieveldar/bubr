<?
if ($GLOBAL["sitekey"]!=1) {
	$ROOT = $_SERVER['DOCUMENT_ROOT'];
	$GLOBAL["sitekey"] = 1; $now=time();
	@require_once($ROOT."/modules/standart/DataBase.php");	
}

### Таблицы, по которым идет индексация
$tables=array(); $tm=array();
$r=mysql_query("SHOW TABLES"); if (mysql_num_rows($r)>0) { while($row = mysql_fetch_array($r, MYSQL_NUM)) { $table = $row[0]; if (mb_strpos($table, "_lenta")!==false) { $tables[]=$table; }}} $cronlog.="Сканирование таблиц. Поиск по таблицам: <b>".count($tables)."</b><br>"; 

if (count($tables)>0) {
	### Узнаем, где остановились в прошлый раз
	$data=DB("SELECT `sets` FROM `_pages` WHERE (`link`='search') LIMIT 1"); @mysql_data_seek($data["result"], 0); $sets=@mysql_fetch_array($data["result"]);
	list($last, $table, $attime) = explode("|", $sets["sets"]); $last=(int)$last; if ($last==0) { $last=9999999; } if ((int)$attime==0) { $attime=30; } if ($table=="") { $table=$tables[0]; }
	
	# СРОЧНЫЙ ПОРЯДОК - САМЫЕ НОВЫЕ НОВОСТИ #
	
	
	
	
	# ОБЫЧНЫЙ ПОРЯДОК ## Выбираем записи из таблицы и заносим в индексную таблицу (или обновляем) 
	$data=DB("SELECT `id`, `name`, `data`, `text` FROM `$table` WHERE (`id`<'$last' && `stat`='1') order by `id` DESC limit $attime");
	if ((int)$data["total"]==0) {
		$last=9999999; $tm=array_flip($tables); $key=$tm[$table]; $key++;
		if ($key>=count($tables)) { $key=0; DB("DELETE FROM `_search` WHERE (`stat`='0')"); DB("UPDATE `_search` SET `stat`='0'"); }
		$table=$tables[$key]; $cronlog.="Смена читаемой таблицы на <u>$table</u>.<br>";
	} else {
		while ($rec=mysql_fetch_array($data["result"])) {  
			$text=text_search_cut($rec["text"]); list($link, $len)=explode("_", $table); $last=$rec["id"];
			DB("INSERT INTO `_search` (`id`,`link`,`data`,`name`,`text`,`stat`) VALUES ('$last','$link','$rec[data]','$rec[name]','$text','1') ON DUPLICATE KEY UPDATE `name`=values(`name`), `data`=values(`data`), `text`=values(`text`), `stat`=values(`stat`)");
		}
		$cronlog.="Записей добавлено: <u>$data[total]</u> из таблицы: <u>$table</u>. Указатель: <u>$last</u>.<br>";
	}
	DB("UPDATE `_pages` SET `sets`='$last|$table|$attime' WHERE (`link`='search') LIMIT 1");
}

//echo($GLOBAL["log"]);

function text_search_cut($value) { $ar=array("|","=","<",">","=","!","(", ")",",","."); $value=trim($value); $value=str_replace('http://', '', $value); $value=strip_tags($value); $value=str_replace($ar,"",$value); return mb_strtolower($value,'UTF-8'); }
?>