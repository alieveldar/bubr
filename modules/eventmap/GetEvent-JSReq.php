<?
session_start(); $dir=explode("/", $_SERVER['HTTP_REFERER']); $HTTPREFERER=$dir[2];
if ($HTTPREFERER==$_SERVER['SERVER_NAME']) {
	
	$GLOBAL["sitekey"]=1;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Cache.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Settings.php";	
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";	
	$JsHttpRequest=new JsHttpRequest("utf-8");
	
	// полученные данные ================================================
	$R = $_REQUEST;
	$id = $R["id"];
		
	$table = '_widget_eventmap';
	
	$data=DB("SELECT `".$table."`.* FROM `".$table."` WHERE (`".$table."`.`id`=".$id.")");
	@mysql_data_seek($data["result"], 0); $event=@mysql_fetch_array($data["result"]);
	$text = '';
	if($event['pic']) $text .= '<div class="eventPic"><img src="/userfiles/picnews/'.$event['pic'].'"></div>';
	$text .= $event['text'];
	if($event['pid'] && $R["readmore"]) $text .= $C10.'<a href="/'.$event['link'].'/view/'.$event['pid'].'" class="readmore">Читать подробнее</a>';
	$result['name'] = $event['name'];
	$result['text'] = $text;		
}


// отправляемые данные ==============================================
$GLOBALS['_RESULT']	= $result;	
?>