<?
	session_start(); 
	if ($_SESSION['userid']!=0) { 
	
	$GLOBAL["sitekey"]=1;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	
	// полученные данные ================================================
	
	$R = $_REQUEST;
	$id = $R["id"];
	$uid = $_SESSION['userid'];

	//=================================================================================================================================================================================================

	$UserFrom=array("vk"=>"vkontakte", "fb"=>"facebook", "ml"=>"mailru", "tw"=>"twitter", "od"=>"odnoklas", "gl"=>"google", "ya"=>"yandex"); $tab=$UserFrom[$id];
	DB("UPDATE `_users` SET ".$tab."='' WHERE (id='$uid')"); $result["Answer"]="ok";
	

	} else { $result["Answer"]="error user"; }

// отправляемые данные ==============================================
$GLOBALS["_RESULT"]=$result;
?>