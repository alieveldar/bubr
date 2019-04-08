<?
session_start();
if ($_SESSION['userrole']>2) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	@require "../../../modules/standart/Settings.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	$table=$R["tab"]; $k=explode("_", $table); $link=$k[0];
	$item=(int)$R["id"];
	$ord=(int)$R["ord"];
	

		
	// операции =========================================================
	
	if ($R["act"]=="DEL") {
		DB("DELETE FROM `".$table."` WHERE (`id`='".$item."')");
	}
	
	
	$result["content"]="ok";
	$GLOBALS['_RESULT']	= $result;
}
?>