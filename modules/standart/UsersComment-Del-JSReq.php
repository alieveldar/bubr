<?
session_start(); $dir=explode("/", $_SERVER['HTTP_REFERER']); $HTTPREFERER=$dir[2];

if ($HTTPREFERER==$_SERVER['HTTP_HOST'] && $_SESSION["userrole"]>0) {
	
	$GLOBAL["sitekey"]=1; $error=0;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	#@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Settings.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	
	// полученные данные ================================================
	
	$R = $_REQUEST;
	$id = (int)$R["id"];
	$l = Dbsel(trim($dir[3])); $l=preg_replace('/[^a-z0-9]+/i', '', $l);
	$i = (int)$dir[5];

	### обновляем базу данных
	DB("DELETE FROM `_comments` WHERE (`id`='".$id."');");
	DB("DELETE FROM `_commentf` WHERE (`pid`='".$id."');");
	
	$d=DB("SELECT `id` FROM `_comments` WHERE (`link`='$l' && `pid`='$i')"); $t=$d["total"];
	DB("UPDATE `".$l."_lenta` set `comcount`='$t' WHERE (`id`='".$i."') LIMIT 1");
	
	@unlink($_SERVER['DOCUMENT_ROOT']."/cache/user_comments/".$l.".".$i.".cache"); // Удаляем кеш комментариев
	$result=array("Code"=>1, "Text"=>"Комментарий удален!", "Class"=>"SuccessDiv", "Comment"=>"");
	
} else { $result=array("Code"=>0, "Text"=>"--- Security alert ---", "Class"=>"ErrorDiv", "Comment"=>''); }

// отправляемые данные ==============================================
$GLOBALS['_RESULT']	= $result;
?>