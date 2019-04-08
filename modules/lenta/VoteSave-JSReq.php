<?
session_start(); $dir=explode("/", $_SERVER['HTTP_REFERER']); $HTTPREFERER=$dir[2];
if ($HTTPREFERER==$_SERVER['SERVER_NAME']) {
	
	$GLOBAL["sitekey"]=1;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Cache.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Settings.php";	
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";	
	$JsHttpRequest=new JsHttpRequest("utf-8");
	
	if (!isset($dir[4]) || $dir[4]=="") $dir[4] = 0;	
	
	// полученные данные ================================================
	
	$R = $_REQUEST;
	$vid = $R["vid"];
	$qid = $R["qid"];
	$pid = $R["pid"];
	$link = $R["link"];
	
	$table = "_widget_voting";
	$table2 = '_widget_votes';	
	
	$file=$table."-result.".$qid;
	
	// операции =========================================================
	function getVotingResult(){
		$return = array(); $votesWords = array('голосов', 'голос', 'голоса');
		global $qid, $table, $table2;
		$file=$table."-result.".$qid; $text = '';
		if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); } else {
			$data=DB("SELECT `$table`.*, COUNT(`$table2`.`id`) as `vn` FROM `$table` LEFT JOIN `$table2` ON `$table2`.`vid`=`$table`.`id` AND `$table2`.`pid`=`$table`.`pid` AND `$table2`.`link`=`$table`.`link` WHERE (`$table`.`id`=$qid OR `$table`.`vid`=$qid) GROUP BY 1");		
			if($data["total"] >= 3){
				$max = 0; $total = 0;
				for ($i=0; $i<$data["total"]; $i++){
					@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
					if($ar["vn"] > $max) $max = $ar["vn"];
					$total += $ar["vn"];
				}
				$k = 200 / $max;
				$res = '';
				for ($i=0; $i<$data["total"]; $i++){
					@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
					if($ar['vid'] == 0) $cap=$ar["name"];
					else {
						$last_symbol = substr($ar["vn"], -1);
						if($last_symbol == 0 || $last_symbol > 4 || (substr($ar["vn"], -2) > 10 && substr($ar["vn"], -2) < 20)) $index = 0;
						else if($last_symbol == 1) $index = 1;
						else if($last_symbol > 1) $index = 2;
						$res .= '<div class="votingResult"><span class="votingResultLine" style="width:'.($ar["vn"] * $k + 10).'px">'.round($ar["vn"] / $total * 100).'%</span>'.$ar["name"].' <span class="votingResultNumber">('.$ar["vn"].' '.$votesWords[$index].')</span></div>';
					}
				}
				$text = '<div class="votingCon"><h4>'.$cap.'</h4>'.$res.'</div>';
				SetCache($file, $text, "");
			}
		}
		return $text;
	}
	
	$or=$_SESSION['userid']?"`uid`=".$_SESSION['userid']:"`ip`='".$GLOBAL["ip"]."'";
	$q=DB("SELECT `id` FROM `$table2` WHERE (".$or.") AND `pid`=$pid AND `link`='$link')");
	
	if ($q["total"]==1) {
		$result="Вы уже голосовали сегодня в данном материале";
	} else {
		DB("INSERT INTO `$table2` (`link`,`vid`,`uid`,`data`,`ip`,`pid`) VALUES ('$link', $vid, ".$_SESSION['userid'].", '".$GLOBAL["now"]."', '".$GLOBAL["ip"]."', '$pid')");
		ClearCache($file); $result['text']=getVotingResult();	
	}
}



// отправляемые данные ==============================================
$GLOBALS['_RESULT']	= $result;	
?>