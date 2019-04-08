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
	$table=$R["table"];
	
	// операции =========================================================
	$q="INSERT INTO `".$R["table"]."` (`name`, `email`, `stat`) VALUES ('".$R["name"]."', '".$R["email"]."', '".(int)$R["chk"]."')";
	DB($q); $rate=DBL(); DB("UPDATE `".$R["table"]."` SET `rate`='".$rate."' WHERE (id='".$rate."')");

	// отправляемые данные ==============================================

	$result["test"]=$q;
	$GLOBALS['_RESULT']	= $result;
}
?>