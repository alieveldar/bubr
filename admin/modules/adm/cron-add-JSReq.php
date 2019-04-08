<?
session_start();
if ($_SESSION['userrole']>2) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	//@require "../../../modules/standart/Settings.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	
	// операции =========================================================
	$q="INSERT INTO `_cron` (`name`,`link`,`runtime`) VALUES ('".$R["name"]."','".$R["link"]."','".$R["time"]."')"; DB($q); DB("UPDATE `_cron` SET `rate`='".DBL()."' WHERE (`id`='".DBL()."')");
	
	// отправляемые данные ==============================================

	$result["test"]=$q;
	$GLOBALS['_RESULT']	= $result;
}
?>