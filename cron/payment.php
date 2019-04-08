<?
if ($GLOBAL["sitekey"]!=1) { $ROOT = $_SERVER['DOCUMENT_ROOT']; $GLOBAL["sitekey"] = 1; $now=time(); @require_once($ROOT."/modules/standart/DataBase.php"); } $stavka=0.2;

$q="SELECT `post_lenta`.`name`, `post_lenta`.`seen`, `post_lenta`.`uid`, `post_lenta`.`id`, `post_users`.`id` as `utid` FROM `post_lenta` LEFT JOIN `post_users` ON `post_lenta`.`id`=`post_users`.`lentaid` WHERE (`post_lenta`.`stat`=1 && `post_lenta`.`oplata`=0 &&`post_lenta`.`data`<'".(time()-7*60*60*24)."')"; $data=DB($q);
for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); if ((int)$ar["utid"]!=0) {
	$pay=round($ar["seen"]*$stavka); DB("UPDATE `post_lenta` SET `oplata`='1' WHERE (`id`='".$ar["id"]."')");
	DB("INSERT INTO `_userspays` (`data`,`money`,`text`,`uid`,`pid`) VALUES ('".time()."','".$pay."','Оплата <b>$ar[seen]</b> просмотров публикации «$ar[name]»','$ar[uid]','$ar[id]')");
} endfor;

echo "<hr>Перешли на оплату публикаций: ".$data["total"]."<hr>";

?>