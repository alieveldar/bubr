<?
session_start();
if ($_SESSION['userrole']>1) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	//@require "../../../modules/standart/Settings.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	
	$id=(int)$R["id"];
	$stat=(int)$R["stat"];
	$table=Dbcut($R["table"]);
	$pole="stat"; 
		
	// операции =========================================================
	
	DB("UPDATE `".$table."` SET `".$pole."`='".$stat."' WHERE (`id`='".$id."') LIMIT 1");
	
	// отправляемые данные ==============================================

	$result["content"]="ok";
	$GLOBALS['_RESULT']	= $result;
}
?>