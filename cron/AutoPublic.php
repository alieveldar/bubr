<?
if ($GLOBAL["sitekey"]!=1) {
	$ROOT = $_SERVER['DOCUMENT_ROOT'];
	$GLOBAL["sitekey"] = 1; $now=time();
	@require_once($ROOT."/modules/standart/DataBase.php");	
}

$i=0; $now=time(); $r=mysql_query("SHOW TABLES"); if (mysql_num_rows($r)>0) { while($row = mysql_fetch_array($r, MYSQL_NUM)) { $table = $row[0]; if (mb_strpos($table, "_lenta")!==false) {
	
	$i++; $d=DB("UPDATE `$table` SET `stat`='1', `astat`='0' WHERE (`stat`='0' AND `astat`='1' AND `adata`<'".$now."')");
	$cronlog.="UPDATE `$table` SET `stat`='1', `astat`='0' WHERE (`stat`='0' AND `astat`='1' AND `adata`<'".$now."')<hr>";

}}}  $cronlog.="Обновлено таблиц: <b>$i</b><br>";
?>