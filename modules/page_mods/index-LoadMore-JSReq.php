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
	
	
	$start1="SELECT `post_lenta`.`id`,`post_lenta`.`name`,`post_lenta`.`lname`,`post_lenta`.`pic`,`post_lenta`.`data`, `_users`.`role`
	FROM `post_lenta` LEFT JOIN `_users` ON `_users`.`id`=`post_lenta`.`uid` WHERE ";
		
	// отправляемые данные ==============================================
	$data=DB($start1." (`post_lenta`.`stat`=1 && `post_lenta`.`data`<'".$old."') GROUP BY 1 ORDER BY `post_lenta`.`data` DESC LIMIT ".$lim);
	
	
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $old=$ar["data"];
		$role=""; if ((int)$ar["role"]==0) { $role="<role>Добавлено<br>читателем</role>"; }
		if ($ar["pic"]!="") {
			if (in_array(($i+1)%5, array(4,0))) { $pic="<img src='/userfiles/picmiddle/".$ar["pic"]."' title='".$ar["name"]."' />"; }
			if (in_array(($i+1)%5, array(1,2,3))) { $pic="<img src='/userfiles/picsquare/".$ar["pic"]."' title='".$ar["name"]."' />"; }
		} else { $pic=""; }			
		$text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaList' id='NewsLentaList-".$ar["id"]."'>".$pic.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>";
		if (($i+1)%5==0) { $text.=$C; }
	endfor;
	if ($data["total"]==$lim) { $code=1; } 
	
	$result["Code"]=$code; $result["text"]=$text; $result["old"]=$old;
} else { $result=array("Code"=>0, "Text"=>"--- Security alert ---", "Class"=>"ErrorDiv", "Comment"=>''); }

// отправляемые данные ==============================================
$GLOBALS['_RESULT']	= $result;

function GetSpanCaption($cap, $ds='') {
	$cap="<span>".nl2br(trim($cap, "."))."</span>"; $cap=str_replace("<br />", "</span><br /><span>", $cap); $cap=str_replace("<span></span>", "", $cap); $cap=str_replace("<span> </span>", "", $cap); 
	if ($ds!="") { $ds="<i>".nl2br(trim($ds, "."))."</i>"; $ds=str_replace("<br />", "</i><br /><i>", $ds); $ds=str_replace("<i></i>", "", $ds); $ds=str_replace("<i> </i>", "", $ds); $ds="<br>".$ds;  }
	$cap="<capt>".$cap.$ds."</capt>"; $cap=str_replace(array("\r","\n"), "", $cap); return $cap;
}