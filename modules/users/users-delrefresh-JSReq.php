<?
session_start(); 
if ($_SESSION['userid']!=0) { 
	
	$GLOBAL["sitekey"]=1;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");

	// полученные данные ================================================
	
	$R = $_REQUEST;
	$uid = $_SESSION['userid'];
	$id=(int)$R["id"];
	$link=Dbsel($R["lin"]);
	
	//=================================================================================================================================================================================================
		
	DB("DELETE FROM `_tracker` WHERE (`pid`='$id' && `uid`='$uid' && `link`='$link') LIMIT 1"); $result["Answer"]="ok";

} else { $result["Answer"]="error access"; }
// отправляемые данные ==============================================

$GLOBALS['_RESULT']=$result;
?>
