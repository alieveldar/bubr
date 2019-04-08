<?
session_start(); $dir=explode("/", $_SERVER['HTTP_REFERER']); $HTTPREFERER=$dir[2];
if ($HTTPREFERER==$_SERVER['SERVER_NAME']) {
	
	$GLOBAL["sitekey"]=1;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Cache.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Settings.php";	
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";	
	$JsHttpRequest=new JsHttpRequest("utf-8");
	
	$R=$_REQUEST;
	$table=$R["link"].'_albums';
	$table2=$R["link"].'_photos';
	$id=(int)$R["id"];
	

		
	// операции =========================================================
	
	if ($R["act"]=="DELPHOTO") {
		$data=DB("SELECT `".$table2."`.`pic`, `".$table2."`.`uid`, `".$table."`.`uid` AS `puid` FROM `".$table2."` LEFT JOIN `".$table."` ON `".$table."`.`id`=`".$table2."`.`pid` WHERE (`".$table2."`.`id`=".$id.") limit 1");
		@mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]);
		if($item['puid'] == $_SESSION['userid'] || $item['uid'] == $_SESSION['userid'] || $_SESSION['userrole'] > 2) {
			DB("DELETE FROM `".$table2."` WHERE (`id`='".$id."')");
			foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { @unlink($ROOT."/userfiles/".$path."/".$pic); }
		}
	}

	if ($R["act"]=="DELALBUM") {
		$data=DB("SELECT `uid` FROM `".$table."` WHERE (`id`=".$id.") limit 1");
		@mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]);
		if($item['uid'] == $_SESSION['userid'] || $_SESSION['userrole'] > 2) {
			DB("DELETE FROM `".$table."` WHERE (`id`='".$id."')");
			$data=DB("SELECT `pic` FROM `".$table2."` WHERE (`pid`=".$id.")");
			DB("DELETE FROM `".$table2."` WHERE (`pid`='".$id."')");
			for ($i=0; $i<$data["total"]; $i++) {
				@mysql_data_seek($data["result"], $i); $ar2=@mysql_fetch_array($data["result"]);
				foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { @unlink($ROOT."/userfiles/".$path."/".$ar2['pic']); }
			}			
		}
	}
	
	$result["content"]="ok";
	$GLOBALS['_RESULT']	= $result;		
}
?>
