<?
session_start();
if ($_SESSION['userrole']>1) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	$items=$R["id"];
	$table="_usersmess";
		
	// операции =========================================================
	if ($R["act"]=="DEL") { DB("DELETE FROM `".$table."` WHERE (`id` IN (".$items."))"); }
	$result["content"]="ok"; $GLOBALS['_RESULT']	= $result;
}
?>