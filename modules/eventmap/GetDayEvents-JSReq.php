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
	$date = explode('-', $R["date"]);
	$date_begin = mktime(0, 0, 0, $date[1], $date[0], $date[2]);
	$date_end = mktime(0, 0, 0, $date[1], $date[0] + 1, $date[2]);
		
	$table = '_widget_eventmap';
	
	$data=DB("SELECT * FROM `".$table."` WHERE (`data`>=".$date_begin." AND `data`<".$date_end.")");
	if($data['total']){
		$text = '<div class="WhiteBlock">';
		for ($i=0; $i<$data["total"]; $i++) {
			@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
			if($i) $text .= '<div class="C25"></div>';
			$text .= '<div class="NewsLentaBlock">';
			$text .= '<div class="Time"><b>'.date('d.m.Y', $ar['data']).'</b></div>';
			if($ar['pic']){
				if($ar['pid']) $text .= '<div class="Pic"><a href="/'.$ar['link'].'/view/'.$ar['pid'].'"><img src="/userfiles/picnews/'.$ar['pic'].'" title="'.$ar['name'].'"></a></div>';
				else $text .= '<div class="Pic"><img src="/userfiles/picnews/'.$ar['pic'].'" title="'.$ar['name'].'"></div>';
			}
			if($ar['pid']) $text .= '<div class="Caption"><h2><a href="/'.$ar['link'].'/view/'.$ar['pid'].'">'.$ar['name'].'</a></h2></div>';
			else $text .= '<div class="Caption"><h2>'.$ar['name'].'</h2></div>';
			$text .= '<div class="C"></div></div>';
		}
		$text .= '</div>';	
	}
	else $text = 'В этот день нет событий';
	
	$result['text'] = $text;		
}


// отправляемые данные ==============================================
$GLOBALS['_RESULT']	= $result;	
?>