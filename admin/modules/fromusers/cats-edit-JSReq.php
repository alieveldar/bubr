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
	
	$q="UPDATE `".$R["table"]."` SET `name`='".$R["name"]."', `email`='".$R["email"]."' WHERE (`id`='".(int)$R["id"]."') LIMIT 1"; DB($q);

	// отправляемые данные ==============================================

	$result["test"]=$q;
	$GLOBALS['_RESULT']	= $result;
}
?>