<?
session_start(); $dir=explode("/", $_SERVER['HTTP_REFERER']); $HTTPREFERER=$dir[2];
if ($HTTPREFERER==$_SERVER['SERVER_NAME']) {
	
	$GLOBAL["sitekey"]=1;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Settings.php";	
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";	
	$JsHttpRequest=new JsHttpRequest("utf-8");
	
	$R=$_REQUEST;
	$table2=$R["link"].'_photos';
	$table3=$R["link"].'_votes';
	$pid=(int)$R["pid"];
	$fid=(int)$R["fid"];
	$act=(int)$R["act"];
		
	// операции =========================================================
	if ($pid!=0 && $fid!=0) { $q="SELECT `id` FROM `$table3` WHERE (`ip`='".$GLOBAL["ip"]."' && `data`='".date("Ymd")."' && `fid`='$fid') LIMIT 1"; $data=DB($q); $result["sql"]=$q;
	if ($data["total"]==0) {
		DB("INSERT INTO `$table3` (`pid`, `fid`, `ip`, `data`, `likedislike`) VALUES ('$pid', '$fid', '".$GLOBAL["ip"]."', '".date("Ymd")."', '$act');"); $data1=DB("SELECT `id` FROM `$table3` WHERE (`fid`='$fid' && `likedislike`=1)"); $t1=$data1["total"];
		$data2=DB("SELECT `id` FROM `$table3` WHERE (`fid`='$fid' && `likedislike`=0)"); $t2=$data2["total"]; DB("UPDATE `$table2` SET `like`='$t1', `dislike`='$t2' WHERE (`id`='$fid')");
		$result["act"]="ok"; $result["like"]="Нравится: $t1"; $result["disl"]="Не нравится: $t2"; $result["ttl"]="Голосов: ".($t1+t2); 
	} ELSE { $result["act"]="daylimit"; }} else { $result["act"]="error"; } $GLOBALS['_RESULT']	= $result;		
}
?>
