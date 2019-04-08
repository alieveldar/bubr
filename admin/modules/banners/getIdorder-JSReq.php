<?
session_start();
if ($_SESSION['userrole']>1) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	@require "../../../modules/standart/Settings.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	
	$id=(int)$R["id"]; $table="_banners_orders"; $table2="_banners_pos";
	$sets=explode("|", @file_get_contents($_SERVER['DOCUMENT_ROOT']."/admin/banners-sets.dat")); $table3=$sets[0]."_items";
	
	$q="SELECT `".$table."`.*, `".$table3."`.`name` as `comp`, `".$table2."`.`name` as `place`, `".$table2."`.`width` as `w`, `".$table2."`.`height` as `h`, `_domains`.`name` as `siten` 
	FROM `".$table."` LEFT JOIN `".$table3."` ON `".$table3."`.`id`=`".$table."`.`cid` LEFT JOIN `".$table2."` ON `".$table2."`.`id`=`".$table."`.`pid` LEFT JOIN `_domains` ON `_domains`.`id`=`".$table."`.`did`
	WHERE (`zid`='".$id."' && `".$table."`.`stat`='1')"; $data=DB($q); $result["log"]=$q;
	
	if ($data["total"]==0 || $data["total"]===false) {
		$result["code"]="0";
	} else {
		$result["code"]="1"; @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
		$d1=ToRusData($ar["datafrom"]); $d2=ToRusData($ar["datato"]); if ($ar["did"]=="9999") { $ar["siten"]="- Сквозной -"; } if ($ar["did"]=="0") { $ar["siten"]="- Агрегаторная страница -"; }  
		$result["text"]="Компания: <b>$ar[comp]</b><hr>Форма: <b>$ar[place]</b><hr>Размер: <b>$ar[w]</b> на <b>$ar[h]</b><hr>Размещение: <b>$ar[siten]</b><hr>Дата: <b>$d1[5]</b> - <b>$d2[5]</b>";
	}
	
	
	$GLOBALS['_RESULT']	= $result;
}
?>