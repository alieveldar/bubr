<?
session_start();
if ($_SESSION['userrole']>=2) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	@require "../../../modules/standart/Settings.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	$item=(int)$R["id"];
	$items=$R["id"];
	$table="tests_";///tests_answers
	$pic=$R["pic"];
	$file = '';

		
	// операции =========================================================
	
	if ($R["act"]=="DEL") {
		DB("DELETE FROM `".$pic."` WHERE (`id` IN (".$items."))");
	}
	
	$result["content"]="ok";
	$GLOBALS['_RESULT']	= $result;
}
?>