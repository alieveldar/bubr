<?
session_start();
if ($_SESSION['userrole']>2) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	
	// операции =========================================================
	
	$q="UPDATE `_banners_pos` SET `name`='".$R["n"]."', `width`='".$R["w"]."', `height`='".$R["h"]."', `rotate`='".$R["r"]."' WHERE (`id`='".(int)$R["id"]."') LIMIT 1"; DB($q);

	// отправляемые данные ==============================================

	$result["test"]=$q;
	$GLOBALS['_RESULT']	= $result;
}
?>