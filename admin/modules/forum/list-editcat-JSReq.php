<?
session_start();
if ($_SESSION['userrole']>1) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	//'alias':alias,'id':id,'fid':fid,'chk1':chk1,'chk2':chk2,'chk3':chk3,'name':name,'text':text,'nfid':nfid
	
	// операции =========================================================
	
	$q="UPDATE `".$R["alias"]."_cat` SET `fid`='".$R["nfid"]."',`name`='".$R["name"]."', `text`='".$R["text"]."', `stat`='".$R["chk1"]."', `add`='".$R["chk2"]."', `lock`='".$R["chk3"]."' WHERE (`id`='".(int)$R["id"]."' && `fid`='".(int)$R["fid"]."') LIMIT 1"; DB($q);

	// отправляемые данные ==============================================

	$result["test"]=$q;
	$GLOBALS['_RESULT']	= $result;
}
?>