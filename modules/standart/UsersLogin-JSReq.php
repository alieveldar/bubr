<?
session_start(); $dir=explode("/", $_SERVER['HTTP_REFERER']); $HTTPREFERER=$dir[2];
if ($HTTPREFERER==$_SERVER['HTTP_HOST']) {
	
	$GLOBAL["sitekey"]=1;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	//@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Settings.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	
	// полученные данные ================================================
	
	$R = $_REQUEST;
	$l = Dbsel($R["login"]);
	$p = md5($R["password"]);
	
	// операции =========================================================
	
	$q=DB("SELECT id, stat, role, pass FROM `_users` WHERE (`login`='$l') LIMIT 1");
	
	if ($q["total"]==0) {
		$result["Text"]="Данный пользователь не найден";
		$result["Class"]="ErrorDiv";
		$result["Code"]=0;
	} else {
		@mysql_data_seek($q["result"],0); $user=@mysql_fetch_array($q["result"]);
		if ($user["pass"]!=$p) {
			$result["Text"]="Указан неверный пароль";
			$result["Class"]="ErrorDiv";
			$result["Code"]=0;
		} elseif ($user["stat"]!=1) {
			$result["Text"]="Данный пользователь заблокирован";
			$result["Class"]="ErrorDiv";
			$result["Code"]=0;
		} else {
			$result["Text"]="Вход выполнен, переадресация к материалу...";
			$result["Class"]="SuccessDiv";
			$result["Code"]=1;
			
			$_SESSION['userid']=$user["id"];
			$_SESSION['userrole']=$user["role"];
			$_SESSION['userfrom']="";
			
			
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