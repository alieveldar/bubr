<?
### Запрашиваемый файл должен определять переменную $rsstext 

if ($GLOBAL["sitekey"]!=1) {
	$ROOT = $_SERVER['DOCUMENT_ROOT'];
	$GLOBAL["sitekey"] = 1; $now=time();
	@require_once($ROOT."/modules/standart/DataBase.php");	
}

$ma = Array("Месяц","января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");
$tables=array("post_lenta");

$rss=DB("SELECT * FROM `_rss`");
if ($rss["total"]>0) {
	for($r=0; $r<$rss["total"]; $r++) {
		$start2=GetMicroTime();
		@mysql_data_seek($rss["result"], $r); $rs=@mysql_fetch_array($rss["result"]);
		$filerss=$rs["reallink"]; $saverss=$rs["virtlink"]; $rsstext="";
		if (is_file($ROOT.$filerss)) { @include($ROOT.$filerss); UPDrss($rs["id"]); } else { $rsstext="File not found"; }
		$filek=@fopen($ROOT.$saverss, "w"); @fputs($filek, $rsstext); @fclose($filek); $kk=round(GetMicroTime()-$start2, 2);
		$cronlog.="Записан файл: ".$ROOT.$saverss." (<b>".round((mb_strlen($rsstext)/1000), 2)." Кб</b>) - время: $kk<br>";
	}
}

function UPDrss($i) { DB("UPDATE `_rss` SET `lasttime`='".time()."' WHERE (`id`='$i')");  }
?>