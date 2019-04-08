<?
if ($GLOBAL["sitekey"]!=1) {
	$ROOT = $_SERVER['DOCUMENT_ROOT'];
	$GLOBAL["sitekey"] = 1;
	@require_once($ROOT."/modules/standart/DataBase.php");	
}

$now = time(); $i=0; $s=0; $c=0;
$table="_banners_items";
$table2="_banners_pos";

	
### ПРОВЕРКА ПАПОК И ИХ СОЗДАНИЕ
$folders=array("statistic", "domains", "cache", "files", "files/flash", "files/mobile", "files/image"); chmod($ROOT."/advert", 0777);
foreach ($folders as $folder) { if (!is_dir($ROOT."/advert/".$folder)) { mkdir($ROOT."/advert/".$folder, 0777); $cronlog.="Создана папка &laquo;/advert/".$folder."&raquo;<br>"; }}


### ОТКЛЮЧАЕМ БАННЕРЫ С ИСТЕКШИМ СРОКОМ РАЗМЕЩЕНИЯ
DB("UPDATE `$table` SET `stat`='0' WHERE (`stat`='1' && `datato`<'".$now."')");

### УДАЛЯЕМ КЭШ РЕКЛАМЫ
$dh = opendir($ROOT.'/advert/domains'); while ($file=readdir($dh)): if ($file!="." && $file!="..") { @unlink($ROOT."/advert/domains/".$file); } endwhile; closedir($dh);
$dh = opendir($ROOT.'/advert/cache'); while ($file=readdir($dh)): if ($file!="." && $file!="..") { @unlink($ROOT."/advert/cache/".$file); } endwhile; closedir($dh);

### СОБРАННАЯ СТАТИСТИКА -> В БАЗУ
$dh = opendir($ROOT.'/advert/statistic'); while ($file=readdir($dh)): if ($file!="." && $file!="..") { $i++; $m=explode(";", @file_get_contents($ROOT."/advert/statistic/".$file)); $bid=str_replace(".dat", "", $file); $s=+$m[0];  $c=+$m[2]; 
DB("INSERT INTO `_banners_stat` (`bid`,`data`,`s`,`us`,`c`,`uc`) VALUES ('$bid', '".date("Y.m.d")."', '$m[0]', '$m[1]', '$m[2]' ,'$m[3]') ON DUPLICATE KEY UPDATE `s`=`s`+'$m[0]', `us`=`us`+'$m[1]', `c`=`c`+'$m[2]', `uc`=`uc`+'$m[3]'");
@unlink($ROOT."/advert/statistic/".$file); } endwhile; closedir($dh); $cronlog.="Обновлена статистика. Учтено баннеров: <b>$i</b>, просмотров: <b>".$s."</b>, переходов: <b>".$c."</b><br>"; 


### СОЗДАТЬ СПИСКИ БАННЕРОВ ПО ПОДДОМЕНАМ ### # id; pid; did; prior; flash; pic; mobile; link; link2; link3; w; h; outer;  text
$sp=";"; $spline="<|>"; $doms=array(0, 9999); $dom=DB("SELECT `id` FROM `_domains`"); for ($j=0; $j<$dom["total"]; $j++) { @mysql_data_seek($dom["result"], $j); $d=@mysql_fetch_array($dom["result"]); $doms[]=$d["id"]; } 

foreach($doms as $key=>$did) {
	$bans=""; $q="SELECT `".$table."`.id, `".$table."`.pid, `".$table."`.did, `".$table."`.flash, `".$table."`.pic, `".$table."`.mobile, `".$table."`.`outer`, `".$table."`.`link`, `".$table."`.`link2`, `".$table."`.`link3`, `".$table."`.`prior`, `".$table."`.`text`, `".$table."`.datafrom, `".$table2."`.`width` as `w`, `".$table2."`.`height` as `h` FROM `".$table."` LEFT JOIN `".$table2."` ON `".$table2."`.`id`=`".$table."`.`pid` WHERE (`".$table."`.`stat`='1' && `".$table."`.`datafrom`<=".$now." && `".$table."`.`did` LIKE '%,".$did.",%')";
	$data=DB($q); if ($data["total"]>0) { for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $n=@mysql_fetch_array($data["result"]); $bans.=$n["id"].$sp.$n["pid"].$sp.$n["did"].$sp.$n["prior"].$sp.$n["flash"].$sp.$n["pic"].$sp.$n["mobile"].$sp.rawurlencode($n["link"]).$sp.rawurlencode($n["link2"]).$sp.rawurlencode($n["link3"]).$sp.$n["w"].$sp.$n["h"].$sp.$n["outer"].$sp.$n["text"].$spline; }}
	@file_put_contents($ROOT."/advert/domains/domain-".$did.".dat", $bans);
} 

$cronlog.="Созданы списки баннеров. Всего баннеров: $data[total], доменов: <b>".count($bans)."</b><br>";
?>