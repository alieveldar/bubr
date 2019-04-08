<?
session_start();
if ($_SESSION['userrole']>2) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	$alias=$R["alias"];
	//'alias':alias,'chk1':chk1,'chk2':chk2,'name':name,'text':text
	
	// операции =========================================================
	$q="INSERT INTO `".$alias."_forum` (`stat`,`name`,`text`,`add`) VALUES ('".(int)$R["chk1"]."','".$R["name"]."','".$R["text"]."','".$R["chk2"]."')";
	DB($q); $rate=DBL(); DB("UPDATE `".$alias."_forum` SET `rate`='".$rate."' WHERE (id='".$rate."')");

	// отправляемые данные ==============================================
	$result["log"]=$q; $GLOBALS['_RESULT']	= $result;
}
?>