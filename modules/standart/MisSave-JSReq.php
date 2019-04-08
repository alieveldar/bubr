<?
session_start(); $dir=explode("/", $_SERVER['HTTP_REFERER']); $HTTPREFERER=$dir[2];
if ($HTTPREFERER==$_SERVER['HTTP_HOST']) {
	
	$GLOBAL["sitekey"]=1;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Settings.php";	
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/MailSend.php";	
	$JsHttpRequest=new JsHttpRequest("utf-8");	
	
	// полученные данные ================================================
	
	$R = $_REQUEST;
	$link = $R["misUrl"];
	$text = $R["misText"];	
	$comment = $R["misComment"];
	
	$table = "_mistakes";
	$data=DB("SELECT `sets` FROM `_pages` WHERE (`module`='mistakes')");
	@mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
	
	// операции =========================================================
	$result = array();
	DB("INSERT INTO `$table` (`link`,`text`,`comment`,`data`) VALUES ('$link', '$text', '$comment', '".time()."')"); $last=DBL();
	$result["Text"]='Спасибо! Ошибка будет исправлена!'; $result["Code"]=1;
	$subject='Сообщение об ошибке на сайте '.$VARS["mdomain"];
	$body='Публикация, в которой замечена ошибка: <a href="'.$link.'">ссылка</a><br>Посмотреть ошибку: <a href="http://'.$VARS["mdomain"].'/admin/?cat=adm_mistakesshow&id='.$last.'">ссылка</a>';
	
	if ($ar["sets"]!="") { MailSend($ar["sets"], $subject, $body, $VARS["sitemail"]); }
		
} else {
	$result["Text"]="--- Security alert ---";
	$result["Class"]="ErrorDiv";
	$result["Code"]=0;
}



// отправляемые данные ==============================================
$GLOBALS['_RESULT']	= $result;	
?>