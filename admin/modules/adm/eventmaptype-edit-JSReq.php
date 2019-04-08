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
	
	$R=$_REQUEST;
	$table="_widget_eventtype";
	
	// операции =========================================================
	$data=DB("SELECT `pic` FROM `$table` WHERE (`id`='".(int)$R["id"]."') LIMIT 1");
	@mysql_data_seek($data["result"], 0); $type=@mysql_fetch_array($data["result"]);
	$pic = $type['pic'];
	if($pic != $R["p"]){				
		if($pic) @unlink($ROOT."/userfiles/mapicon/".$pic);
		$pic = $R["p"];				
		if($pic) {
			crop($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/mapicon/".$pic);
			resize($ROOT."/userfiles/mapicon/".$pic, $ROOT."/userfiles/mapicon/".$pic, 100, 100);
		}
	}
	 
	$q="UPDATE `$table` SET `name`='".$R["n"]."', `pic`='".$pic."' WHERE (`id`='".(int)$R["id"]."') LIMIT 1"; DB($q);

	// отправляемые данные ==============================================

	$result["test"]=$q;
	$GLOBALS['_RESULT']	= $result;
}
?>