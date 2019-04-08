<?
$GLOBAL=array(); $GLOBAL["sitekey"]=1; $now=time(); $ROOT=$_SERVER['DOCUMENT_ROOT']; 
@require_once($ROOT."/modules/standart/DataBase.php"); 
@require_once($ROOT."/modules/standart/Settings.php");

$crondata=DB("SELECT * FROM `_cron` WHERE (`runtime`+`lasttime`<'".$now."' && stat='1') ORDER BY `rate` DESC");
if ($crondata["total"]>0) { for ($croni=0; $croni<$crondata["total"]; $croni++): @mysql_data_seek($crondata["result"],$croni); $arcron=@mysql_fetch_array($crondata["result"]);
	$start=GetMicroTime(); $cronlog=""; $act.=$arcron["link"].", ";
	$cronlog.="Запуск крона: <b>".$arcron["link"]."</b>, время: <b>".date("H:i:s, d.m.Y")."</b><br>"; UPD($arcron["id"], $cronlog);
	if (is_file($ROOT.$arcron["link"])) {
		$cronlog.="Файл загружен: <b>".$arcron["link"]."</b>, время: <b>".date("H:i:s, d.m.Y")."</b><br><br>"; UPD($arcron["id"], $cronlog);
		#################################################################################################################
		@require_once($ROOT.$arcron["link"]);
		#################################################################################################################
		$cronlog.="<br>Файл выполнен: <b>".$arcron["link"]."</b>, время: <b>".date("H:i:s, d.m.Y")."</b><br>"; UPD($arcron["id"], $cronlog);
	} else {
		$cronlog.="Файл не найден: <b>".$arcron["link"]."</b>, время: <b>".date("H:i:s, d.m.Y")."</b><br>"; UPD($arcron["id"], $cronlog);
	}
	$end=GetMicroTime(); $cronlog.="<hr>Время выполнения: <b>".round($end-$start, 2)."</b> сек.";  UPD($arcron["id"], $cronlog); endfor;
}
echo "<hr>Cron end @ ".date("H:i:s d.m.Y")." runned: ".trim($act, ", ");

function UPD($i, $t) { DB("UPDATE `_cron` SET log='$t', lasttime='".time()."' WHERE (`id`='$i')"); }
?>