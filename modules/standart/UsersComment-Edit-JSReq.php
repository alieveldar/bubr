<?
session_start(); $dir=explode("/", $_SERVER['HTTP_REFERER']); $HTTPREFERER=$dir[2];

if ($HTTPREFERER==$_SERVER['SERVER_NAME']) {
	
	$GLOBAL["sitekey"]=1; $error=0;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Cache.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Settings.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	
	// полученные данные ================================================
	
	$R = $_REQUEST;
	$uid = (int)$_SESSION["userid"];
	
	
	if($R["action"] == "form") $result=GetFormComments((int)$R["id"]);	
	else{		
		$t = $R["t"]; $t=str_replace("'", "&#039;", $t); $t=trim(strip_tags($t, "<b><i><u><q><p>"));
		//$t=str_replace(array("","","",""), array("","","",""), $t);
		
		$fp=@fopen($_SERVER['DOCUMENT_ROOT']."/comments-debug.txt", "a"); @fwrite($fp, $t."\n\r"); @fclose($fp);	
		$UserSetsSite=explode("|", $_SESSION["UserSetsSiteS"]);
		
		// проверки =========================================================
		
		if ($UserSetsSite[3]==1) {
			/* СПАМ */ $cnt=checkSpam($t); if ($cnt>=2 && $error==0) { $error=1; $result=array("Code"=>10, "Text"=>"Спасибо за SPAM! [filter=$cnt]", "Class"=>"ErrorDiv", "Comment"=>""); DB("UPDATE `_users` SET `stat`='0' WHERE (`id`='".$uid."') LIMIT 1"); $_SESSION["userid"]=0; $_SESSION['userrole']=0; }
			if ($uid==0 && $UserSetsSite[4]==0 && $error==0) { $error=1; $result=array("Code"=>0, "Text"=>"Для добавления комментария необходимо авторизоваться", "Class"=>"ErrorDiv", "Comment"=>""); }
			if ($t=="") { $error=1; $result=array("Code"=>0, "Text"=>"Введите текст комментария", "Class"=>"ErrorDiv", "Comment"=>""); }
			if (checkAttrs($t)) { $error=1; $result=array("Code"=>0, "Text"=>"Запрещается использовать теги кроме &lt;b&gt;, &lt;i&gt;, &lt;u&gt;, &lt;p&gt;", "Class"=>"ErrorDiv", "Comment"=>""); }
			
			if ($error==0) {
				$where=$_SESSION["userrole"] > 2 ? "" : "AND `uid`=".$_SESSION['userid']; $d=DB("UPDATE `_comments` SET `text`='".$t."' WHERE (`id`='".(int)$R["id"]."' $where)");
				$result=array("Code"=>1, "Text"=>"Комментарий отредактирован!", "Class"=>"SuccessDiv", "Comment"=>nl2br($t)); $data=DB("SELECT `link`, `pid` FROM `_comments` WHERE (`id`='".(int)$R["id"]."')");
				@mysql_data_seek($data["result"],0); $com=@mysql_fetch_array($data["result"]); $file="user_comments-".$com['link'].".".$com['pid']; ClearCache($file);
			}
		}		
	}
	
} else { $result=array("Code"=>0, "Text"=>"--- Security alert ---", "Class"=>"ErrorDiv", "Comment"=>''); }

// отправляемые данные ==============================================
$GLOBALS['_RESULT']	= $result;

// Форма комментариев
function GetFormComments($id) {
	global $VARS, $GLOBAL, $UserSetsSite, $C, $C10, $C5; $id=(int)$id;
	$smiles = "<div class='Smiles'><a href='javascript:void(0)' class='Smile Smile1 toggle'></a>";
	$smiles .= "<div class='SmilesGroup'>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile1' onClick='addSmile($(this))'>:-)</a>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile2' onClick='addSmile($(this))'>;-)</a>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile3' onClick='addSmile($(this))'>:-(</a>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile4' onClick='addSmile($(this))'>:-D</a>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile5' onClick='addSmile($(this))'>:-P</a>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile6' onClick='addSmile($(this))'>=-D</a>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile7' onClick='addSmile($(this))'>8'-(</a>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile8' onClick='addSmile($(this))'>>-(</a>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile9' onClick='addSmile($(this))'>;-|</a>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile10' onClick='addSmile($(this))'>8-*</a>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile11' onClick='addSmile($(this))'>(!)</a>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile12' onClick='addSmile($(this))'>(?)</a>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile13' onClick='addSmile($(this))'>8-|</a>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile14' onClick='addSmile($(this))'>%-8</a>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile15' onClick='addSmile($(this))'>B-|</a>";
	$smiles .= "<a href='javascript:void(0)' class='Smile Smile16' onClick='addSmile($(this))'>>:></a>";
	$smiles .= "</div></div>";
	
	$where=$_SESSION["userrole"] > 2 ? "" : "AND `uid`=".$_SESSION['userid'];
	$data=DB("SELECT `text` FROM `_comments` WHERE (`id`='".(int)$id."' $where)");
	if($data["total"]){
		@mysql_data_seek($data["result"],0); $com=@mysql_fetch_array($data["result"]);
		$text.=$C10."<div class='UserCommentsForm2'><div class='CommentMsg'></div><div class='UserComTextDiv'><textarea class='UserComText'>".$com['text']."</textarea>".$smiles."</div>";
		$text.=$C5."<div class='CommentSend'><input type='submit' name='sendbutton' class='SaveButton' value='Сохранить комментарий' onClick=\"EditUserComment(".$id.", $(this).parents('.UserCommentsForm2'));\" style='float:left; margin-right:10px;'><input type='button' class='SaveButton' onClick='CancelEditComment(".$id.");' value='Отмена' style='float:left; width:100px;'></div></div>";
		return array("Code"=>1, "Text"=>$text);
	}
}


function checkAttrs($string){
	preg_match_all('/<.*?>/', $string, $tags);
	if(!$tags) return false;
	foreach($tags[0] as $tag){
		$tag = str_replace(array('<', '>', '/', ' '), '', strtolower($tag));
		if(!in_array($tag, array('b', 'i', 'u', 'p'))) return true;
	}
}
?>