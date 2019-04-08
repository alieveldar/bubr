<?
session_start();
if ($_SESSION['userrole']>1) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	
	// операции =========================================================
	
	$q="UPDATE `".$R["alias"]."_forum` SET `name`='".$R["name"]."', `text`='".$R["text"]."', `stat`='".$R["chk1"]."', `add`='".$R["chk2"]."' WHERE (`id`='".(int)$R["id"]."') LIMIT 1"; DB($q);

	// отправляемые данные ==============================================

	$result["test"]=$q;
	$GLOBALS['_RESULT']	= $result;
}
?>