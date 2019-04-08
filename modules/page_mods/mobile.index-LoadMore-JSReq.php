<?
session_start(); $dir=explode("/", $_SERVER['HTTP_REFERER']); $HTTPREFERER=$dir[2];
if ($HTTPREFERER==$_SERVER['SERVER_NAME']) {
	
	$GLOBAL["sitekey"]=1; $text=''; $code=0; $table="post_lenta";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	
	// полученные данные ================================================
	$R = $_REQUEST; 
	$old = (int)$R["old"];
	$lim = (int)$R["lim"];
	
	// отправляемые данные ==============================================
	$data=DB("SELECT `name`,`lname`,`pic`,`data`,`id` FROM `".$table."` WHERE (`stat`=1 && `data`<'".$old."') GROUP BY 1 ORDER BY `data` DESC LIMIT ".$lim);
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $old=$ar["data"];
		if ($ar["pic"]!="") { $pic="<img src='/userfiles/picmiddle/".$ar["pic"]."' title='".$ar["name"]."' />"; } else { $pic=""; }
		$text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaList' id='NewsLentaList-".$ar["id"]."'>".$pic."<capt><b>".GetSpanCaption($ar["name"])."</b><br>".GetSpanCaption($ar["lname"])."</capt></a>";
		if (($i+1)%5==0) { $text.=$C; }
	endfor;
	if ($data["total"]==$lim) { $code=1; } 
	
	$result["Code"]=$code; $result["text"]=$text; $result["old"]=$old;
} else { $result=array("Code"=>0, "Text"=>"--- Security alert ---", "Class"=>"ErrorDiv", "Comment"=>''); }

// отправляемые данные ==============================================
$GLOBALS['_RESULT']	= $result;

function GetSpanCaption($cap) { $cap="<span>".nl2br(trim($cap, "."))."</span>"; $cap=str_replace("<br />", "</span><br /><span>", $cap); $cap=str_replace("<span></span>", "", $cap); $cap=str_replace("<span> </span>", "", $cap); $cap=str_replace(array("\r","\n"), "", $cap);	return $cap; }