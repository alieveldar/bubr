<?
session_start();
if ($_SESSION['userrole']>2) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST; $table="_banners_pos";
	
	// операции =========================================================
	$q="INSERT INTO `$table` (`name`, `width`, `height`, `rotate`) VALUES ('".$R["n"]."','".$R["w"]."','".$R["h"]."','".(int)$R["r"]."')"; DB($q);
	DB("UPDATE `$table` SET `rate`='".DBL()."' WHERE (`id`='".DBL()."')");
	
	// отправляемые данные ==============================================

	$result["test"]=$q;
	$GLOBALS['_RESULT']	= $result;
}
?>