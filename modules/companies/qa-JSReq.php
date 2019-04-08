<?
session_start();
if ($_SESSION['userrole']) {
	$GLOBAL["sitekey"]=1;
	@require "../standart/DataBase.php";
	@require "../standart/Settings.php";
	@require "../standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	$table=$R["tab"]; $k=explode("_", $table); $link=$k[0];
	$item=(int)$R["id"];
	

		
	// операции =========================================================
	
	if ($R["act"]=="DEL") {
		$data = DB("SELECT `pics` FROM `".$table."` WHERE (`id`='".$item."')");
		@mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
		if($ar['pics']){
			$pics = explode('|', $ar["pics"]);
			foreach($pics as $pici){
				@unlink($ROOT.'/userfiles/picnews/'.$pici);
				@unlink($ROOT.'/userfiles/picoriginal/'.$pici);
			}
		}
		DB("DELETE FROM `".$table."` WHERE (`id`='".$item."')");
	}


	
	$result["content"]="ok";
	$GLOBALS['_RESULT']	= $result;
}
?>