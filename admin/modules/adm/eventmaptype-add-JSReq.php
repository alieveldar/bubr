<?
session_start();
if ($_SESSION['userrole']>2) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	@require "../../../modules/standart/JsRequest.php";
	@require "../../../modules/standart/Settings.php";
	@require $ROOT."/modules/standart/ImageResizeCrop.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST; $table="_widget_eventtype";
	$pic = $R['p'];
	
	if($pic) {
		crop($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/mapicon/".$pic);
		resize($ROOT."/userfiles/mapicon/".$pic, $ROOT."/userfiles/mapicon/".$pic, 100, 100);
	}
	
	// операции =========================================================
	$q="INSERT INTO `$table` (`name`, `pic`) VALUES ('".$R["n"]."', '".$pic."')"; DB($q);
	DB("UPDATE `$table` SET `rate`='".DBL()."' WHERE (`id`='".DBL()."')");
	
	// отправляемые данные ==============================================

	$result["test"]=$q;
	$GLOBALS['_RESULT']	= $result;
}
?>