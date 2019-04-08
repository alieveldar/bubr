<?
session_start(); $dir=explode("/", $_SERVER['HTTP_REFERER']); $HTTPREFERER=$dir[2];
if ($HTTPREFERER==$_SERVER['HTTP_HOST']) {
	
	$GLOBAL["sitekey"]=1;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	//@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Settings.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	
	// полученные данные ================================================
	
	//'login':l, 'password':p, 'nick':n, 'email':e
	
	$R = $_REQUEST;
	$ll = Dbsel(trim($R["l"]));
	$nn = Dbsel($R["n"]);
	$ee = Dbsel($R["e"]);
	$pp = md5($R["p"]);
	
	// операции =========================================================
	
if ($ll!=$R["l"]) { 
		$result["Text"]="В логине запрещено использовать символы: ( ) / = ' \" & ; , .";
		$result["Class"]="ErrorDiv";
		$result["Code"]=0;
} else {
	$q=DB("SELECT id FROM `_users` WHERE (`login`='$ll') LIMIT 1");
	if ($q["total"]==0) {
		DB("INSERT INTO `_users` (`login`, `nick`, `pass`, `role`, `ip`, `mail`, `stat`, `created`, `lasttime`) VALUES ('".$ll."', '".$nn."', '".$pp."', '0', '".$_SERVER['REMOTE_ADDR']."', '".$ee."', '1', '".time()."', '".time()."')");
		$_SESSION['userid']=DBL(); $_SESSION['userfrom']=""; $_SESSION['userrole']=1; $result["Text"]="Поздравляем с регистрацией на сайте..."; $result["Class"]="SuccessDiv"; $result["Code"]=1;
	} else {
		$result["Text"]="Данный логин уже занят другим пользователем!";
		$result["Class"]="ErrorDiv";
		$result["Code"]=0;
	}
}
	
} else {
	$result["Text"]="--- Security alert ---";
	$result["Class"]="ErrorDiv";
	$result["Code"]=0;
}

// отправляемые данные ==============================================
$GLOBALS['_RESULT']	= $result;	
?>