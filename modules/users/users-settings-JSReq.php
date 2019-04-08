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
	$name=htmlspecialchars($R["name"]);
	$phone=htmlspecialchars($R["phone"]);
	$pass=$R["pass"];
	
	//=================================================================================================================================================================================================

		DB("UPDATE `_users` SET `phone`='".$phone."', `nick`='".$name."' WHERE (id='$uid')");
		if ($pass!="") { DB("UPDATE `_users` SET `pass`='".md5($pass)."' WHERE (id='$uid')"); }
		$result["Answer"]="ok";

} else { $result["Answer"]="error access"; }
// отправляемые данные ==============================================

$GLOBALS['_RESULT']=$result;
?>
