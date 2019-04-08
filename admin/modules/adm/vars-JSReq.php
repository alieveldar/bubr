<?
session_start();
if ($_SESSION['userrole']>2) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	@require "../../../modules/standart/Settings.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================

	$id	= $_REQUEST['id'];

	// операции ================================================

	DB("DELETE FROM `_settings` WHERE (`stat`='0' && `id`='$id')");

	// отправляемые данные ================================================

	$result["test"]=1;
	$GLOBALS['_RESULT']	= $result;
}
?>