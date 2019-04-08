<?
// Тема проспмотрена если человек за ней следил
function UserTracker($link, $pid) {
	global $VARS, $GLOBAL, $UserSetsSite;
	if ($GLOBAL["USER"]["id"]!=0){ $uid=$GLOBAL["USER"]["id"]; DB("UPDATE `_tracker` SET `stat`='0' WHERE (`uid`='".(int)$uid."' && `link`='".$link."' && `pid`='".(int)$pid."')"); }
}

function UsersComments($link, $pid, $sets, $header=1) {	
	global $VARS, $GLOBAL, $RealHost, $RealPage, $UserSetsSite, $C, $C5, $C10, $C15; $file="user_comments-".$link.".".$pid;
		
	if (!isset($UserSetsSite)) { return false; } // Если не загружены настройки 
	if ($UserSetsSite[3]==0) { return false; }  // Если запрещены комментарии в настройках
	if ($sets==2 && $GLOBAL["USER"]["role"]<1) { return false; } // Если запрещены комментарии в данной статье
	
	$text='
<script type="text/javascript" src="//vk.com/js/api/openapi.js?116"></script>
<script type="text/javascript">  VK.init({apiId: 4597676, onlyWidgets: true});</script>
<div id="vkcomments"></div><script type="text/javascript">VK.Widgets.Comments("vkcomments", {limit: 20, width: "640", attach: "*"});</script>
	';
	
	return $text;
	if (RetCache($file)=="true") { list($text, $cap)=GetCache($file, 0); } else { list($text, $cap)=GetUsersComments($link, $pid); SetCache($file, $text, ""); }
	if ($header==1) { $text=$C."<a name='comments' id='comments'></a><h2>Комментарии</h2>".$C."<div id='UserCommentsList'>".$text."</div>"; }
	if ($header==0) { $text=$C."<a name='comments' id='comments'></a>".$C."<div id='UserCommentsList'>".$text."</div>"; }
	//$text.=$C10."<!--ADS-->";
/*
	if ($sets==0 && (($UserSetsSite[4]==1 && $GLOBAL["USER"]["id"]==0) || $GLOBAL["USER"]["id"]!=0)) { $text.=$C5."<a name='addcomment' id='addcomment'></a><h2>Добавить комментарий</h2>".$C."<div id='UserCommentsForm'>".GetFormComments($link, $pid)."</div>";
	$text.="<script>jQuery.each($('.CommentAnswer'), function(i, val) { var fid=$(this).attr('id'); id=fid.split('-'); $(this).html(\"<a href='javascript:void(0);' onClick='CommentAnswer(\"+id[1]+\");'>Ответить</a>\"); });</script>";}
*/
	
	if ($sets==0 || $GLOBAL["USER"]["role"]>=1) {
		 if(($UserSetsSite[4]==1 && $GLOBAL["USER"]["id"]==0) || $GLOBAL["USER"]["id"]!=0) {
		 	if ($header==1) { $text.=$C5."<a name='addcomment' id='addcomment'></a><h2>Добавить комментарий</h2>".$C."<div class='UserCommentsForm'>".GetFormComments($link, $pid)."</div>"; } 
		 	if ($header==0) { $text.=$C5."<a name='addcomment' id='addcomment'></a>".$C."<div class='UserCommentsForm'>".GetFormComments($link, $pid)."</div>"; }
		 }
		 if(($UserSetsSite[4]==1 && $GLOBAL["USER"]["id"]==0) || $GLOBAL["USER"]["id"]!=0) {
		 	$text.="<script>jQuery.each($('.CommentAnswer'), function(i, val) { var fid=$(this).attr('id'); id=fid.split('-'); $(this).html(\"<a href='javascript:void(0);' onClick='CommentAnswer(\"+id[1]+\");'>ОТВЕТИТЬ</a>\"); });</script>";
		 } else {
		 	$redirect_uri=rawurlencode("http://".$RealHost."/modules/standart/LoginSocial.php?back=http://".$RealHost."/".$RealPage);		 	
		 	$text.='<script>jQuery.each($(".CommentAnswer"), function(i, val) { var fid=$(this).attr("id"); id=fid.split("-"); $(this).html("<a href=\'javascript:void(0);\' onClick=\'UserAuthEnter(\"Авторизация\", \"'.$redirect_uri.'\");\'>ОТВЕТИТЬ</a>"); });</script>';
		 }
		
	}
	
	/* редактировать и удалить*/
	if ($GLOBAL["USER"]["role"]>1) { $text.="<script>jQuery.each($('.CommentAdmin'), function(i, val) { var fid=$(this).attr('id'); id=fid.split('-'); $(this).html(\"<span class='CommentDelete'><img src='/template/standart/loader2.gif' style='width:57px; height:14px; padding:0; margin-left:15px;' /><a href='javascript:void(0);' onClick='CommentDelete(\"+id[1]+\");' class='CommentDelAdn'>Удалить</a></span><span class='CommentEdit'><img src='/template/standart/loader2.gif' style='width:57px; height:14px; padding:0; float:right; margin-left:15px;' /><a href='javascript:void(0);' onClick='GetCommentForm(\"+id[1]+\");' class='CommentEditAdn'>Редактировать</a></span>\"); });</script>";}
	else if($GLOBAL["USER"]["id"]/* || (!$GLOBAL["USER"]["id"] && $UserSetsSite[4])*/) {  $text.="<script>jQuery.each($('.CommentAdmin'), function(i, val) { var fid=$(this).attr('id'); id=fid.split('-'); if(id[2] == ".(int)$GLOBAL["USER"]["id"]." || id[2] == ".str_replace(".", "", $GLOBAL["ip"]).") { $(this).html(\"<span class='CommentEdit'><img src='/template/standart/loader2.gif' style='width:57px; height:14px; padding:0; float:right; margin-left:15px;' /><a href='javascript:void(0);' onClick='GetCommentForm(\"+id[1]+\");' class='CommentEditAdn'>Редактировать</a></span>\");} });</script>"; }
	
	if($GLOBAL["USER"]["id"]) {
		 $text.="<script>jQuery.each($('.CommentAdmin'), function(i, val) { var fid=$(this).attr('id'); id=fid.split('-'); if(id[2] != ".(int)$GLOBAL["USER"]["id"]." && id[2] != ".str_replace(".", "", $GLOBAL["ip"]).") { $(this).parents(\".CommentItem .Data\").append(\"<div class='ThisIsSpam'><img src='/template/standart/loader2.gif' style='width:57px; height:14px; padding:0; margin:0;' /><a href='javascript:void(0);' onClick='ThisIsSpam(\"+id[1]+\");'>Это спам</a></div>\");} });</script>";
	}
	
	if ($sets==0 && $GLOBAL["USER"]["id"]==0 && $UserSetsSite[4]==0) {
		$text.=$C5."<a name='addcomment' id='addcomment'></a><h2>Авторизуйтесь для добавления комментария</h2>".$C."<div class='UserCommentsForm'>".GetUserAuthForm()."</div>"; 
	}
	$text.="<script>jQuery.each($('.quote'), function(i, val) { if($(this).height()>$(this).parents('.quote-overview.short').height()) $('.ToggleShow', $(this).parents('.quote-container')).show() });</script>";
	return($text);
}


// Текст комментариев
function GetUsersComments($link, $pid) {
	global $VARS, $GLOBAL, $USER, $UserSetsSite, $C, $C5, $C10, $C15; $text=""; $lastc=0;
	
	$smiles = array(
		":-)" => "<span class='Smile Smile1'>:-)</span>",
		";-)" => "<span class='Smile Smile2'>;-)</span>",
		":-(" => "<span class='Smile Smile3'>:-(</span>",
		":-D" => "<span class='Smile Smile4'>:-D</span>",
		":-P" => "<span class='Smile Smile5'>:-P</span>",
		"=-D" => "<span class='Smile Smile6'>=-D</span>",
		"8'-(" => "<span class='Smile Smile7'>8'-(</span>",
		">-(" => "<span class='Smile Smile8'>>-(</span>",
		";-|" => "<span class='Smile Smile9'>;-|</span>",
		"8-*" => "<span class='Smile Smile10'>8-*</span>",
		"(!)" => "<span class='Smile Smile11'>(!)</span>",
		"(?)" => "<span class='Smile Smile12'>(?)</span>",
		"8-|" => "<span class='Smile Smile13'>8-|</span>",
		"%-8" => "<span class='Smile Smile14'>%-8</span>",
		"B-|" => "<span class='Smile Smile15'>B-|</span>",
		">:>" => "<span class='Smile Smile16'>>:></span>",
	);
	
	$data=DB("SELECT `comments1`.*, `comments2`.`uid` AS `touid`, `comments2`.`text` AS `totext`, `users1`.`nick`, `users1`.`spectitle`, `users1`.`avatar`, `users1`.`signature`, `users1`.`role`, `users1`.`karma`, `users1`.`created`, `users2`.`nick` AS `tonick` FROM `_comments` AS `comments1`
	LEFT JOIN `_users` AS `users1` ON `users1`.`id`=`comments1`.`uid` LEFT JOIN `_comments` AS `comments2` ON `comments2`.`id`=`comments1`.`toid` LEFT JOIN `_users` AS `users2` ON `users2`.`id`=`comments2`.`uid` WHERE (`comments1`.`link`='".$link."' && `comments1`.`pid`='".(int)$pid."') GROUP BY 1 ORDER BY `comments1`.`data` ASC");
	if ($data["total"]==0) { return (array("<div class='Info' id='NoComments'>Нет комментариев к данной публикации</div>", "")); } $ids=array(); $inc=array();
	### ID комментариев
	for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"],$i); $com=@mysql_fetch_array($data["result"]); $ids[]=$com["id"]; }
	### Вложения для всех комментариев
	if ($UserSetsSite[7]==1) { $f=DB("SELECT `pic`, `pid` FROM `_commentf` WHERE (`pid` IN (".implode(",", $ids).")) ORDER BY `id` ASC");
	for ($j=0; $j<$f["total"]; $j++) { @mysql_data_seek($f["result"],$j); $fl=@mysql_fetch_array($f["result"]); $cid=$fl["pid"]; $inc[$cid][]=$fl["pic"]; }}
	### Вывод комментариев
	for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"],$i); $com=@mysql_fetch_array($data["result"]); $datar=ToRusData($com["data"]); $medal=""; $toComment = ""; $cid=$com["id"];
		if ($com["uid"]==0) {
			 $com["nick"]="<span id='UserIdComment-".$com["id"]."' class='UserComName'>".($com["uname"] ? $com["uname"] : "Горожанин")."</span>"; $avatar="<img src='/userfiles/avatar/no_photo.jpg'>";
		} else { $com["nick"]="<a target='_blank' href='/users/view/".$com["uid"]."/'><span id='UserIdComment-".$com["id"]."' class='UserComName'>".$com["nick"]."</span></a>";
			
		/* Якорь на последние комментарии */ if ($lastc==0 && $i>($data["total"]-4)) { $com["nick"].="<a id='endcomments' name='endcomments'></a>"; $lastc=1; }
		if (is_file($_SERVER['DOCUMENT_ROOT']."/".$com["avatar"]) && filesize($_SERVER['DOCUMENT_ROOT']."/".$com["avatar"])>100  && $com["avatar"]!="" && $com["avatar"]!="/") { $avatar="<a target='_blank' href='/users/view/".$com["uid"]."/'><img src='/".$com["avatar"]."'></a>";
		} else { $avatar="<a target='_blank' href='/users/view/".$com["uid"]."/'><img src='/userfiles/avatar/no_photo.jpg'></a>"; }}
		$answer="<span class='CommentAdmin' id='CommentAdmin-".$com["id"]."-".($com["uid"] ? $com["uid"] : str_replace(".", "", $com["ip"]))."'></span>";
		### Медальки
		if ((time()-$com["created"])>60*60*24*365) { $medal="<img src='/template/standart/medal/pochet.gif' title='Почетный гражданин'>"; } # больше года на сайте 
		if ($com["role"]>0) { $medal="<img src='/template/standart/medal/".$com["role"].".gif' title='".$GLOBAL["roles"][$com["role"]]."'>"; }
		### Комментарий
		if($com["toid"]){
			$toComment = $C."<div class='quote-container'>В ответ на <a href='#comment".$com["toid"]."'><u><b>комментарий</b></u></a> пользователя <a href='/users/view/".$com["touid"]."'><u><b>".$com["tonick"]."</b></u></a>:<div class='C5'></div><div class='WhiteBlock'><div class='quote-overview short'><div class='quote'>".nl2br($com["totext"]);
			if (count($inc[$com["toid"]])>0) { $toComment.=$C10."<div class='CommentInc'>"; foreach($inc[$com["toid"]] as $k=>$pic) {
			$toComment.="<a href='/userfiles/comoriginal/".$pic."' rel='prettyPhoto[gallery]'><img src='/userfiles/compreview/".$pic."' /></a>"; } $toComment.="<div class='C'></div></div>"; }
			$toComment .= "</div></div><div class='ToggleShow'><a href='javascript:void(0)' onclick='$(\".quote-overview\", $(this).parents(\".quote-container\")).removeClass(\"short\"); $(this).parents(\".ToggleShow\").hide()'>Развернуть</a></div></div></div>";
		}
		$comment = nl2br($com["text"]);
		$youtubePattern = '/(http:\/\/)?((www.youtube.com)|(youtu.be))\/(\S)+/i';
		if(preg_match_all($youtubePattern, strip_tags($comment), $output)) { 
			foreach($output[0] as $url){
				if(preg_match('/youtu.be/', $url)){
					$tmp_url = explode('/', $url);
					$video_id = $tmp_url[count($tmp_url) - 1];
				}
				else{
					preg_match('/v=[^&]+/', $url, $matches);
					$video_id = str_replace('v=', '', $matches[0]);
				} 
				$embed = '<br /><object width="480" height="390"><param name="movie" value="http://www.youtube.com/v/'.$video_id.'&hl=en&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$video_id.'&hl=en&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="480" height="390"></embed></object>'; 
				$comment = str_replace($url, $embed, $comment); 
			}
		}
		
		$toComment = strtr($toComment, array("&#039;" => "'"));
		$toComment = strtr($toComment, $smiles);
		$comment = strtr($comment, array("&#039;" => "'"));
		$comment = strtr($comment, $smiles);
		
		$text.="<a name='comment".$com["id"]."' id='comment".$com["id"]."'></a><div class='CommentItem' id='CommentItem-".$com["id"]."'><div class='LeftItem'>".$com["nick"]."<div class='C'></div><div class='AvatarU'>".$avatar."</div><div class='C5'></div>";
		if ($com["spectitle"]!="") { $text.="<i>".$com["spectitle"]."</i>"; } $text.="<div class='C5'></div></div>"; $text.="<div class='RightItem'><div class='Data'>".$datar[1].$answer."</div>".$toComment."<div class='view1'>".$comment."</div><div class='view2'></div><div class='C10'></div>";
		### Вложения
		if (count($inc[$cid])>0) { $text.="<div class='CommentInc'>"; foreach($inc[$cid] as $k=>$pic) {
		$text.="<a href='/userfiles/comoriginal/".$pic."' rel='prettyPhoto[gallery]'><img src='/userfiles/compreview/".$pic."' /></a>";	} $text.="<div class='C'></div></div><div class='C10'></div>"; }
		### Подпись
		if ($com["signature"]!="" && $UserSetsSite[6]==1) { $text.="<div class='Signature'>".$com["signature"]."</div>"; }
		### Ответ на комментарий
		$text.="<div class='CommentAnswer' id='CommentAnswer-".$com["id"]."'></div>";
		$text.="</div>".$C."</div>";		
	}
	return(array($text, ""));
}


// Форма комментариев
function GetFormComments($link, $pid) {
	global $VARS, $GLOBAL, $UserSetsSite, $C, $C10, $C5;
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
	$text.="<div class='CommentMsg'></div>"; $text.="<div class='UserComAvatar'>"; if ($GLOBAL["USER"]["id"]==0) { $text.="<img src='/userfiles/avatar/no_photo.jpg'>"; } else {
	if (is_file($GLOBAL["USER"]["avatar"])) { $text.="<img src='/".$GLOBAL["USER"]["avatar"]."'>"; } else { $text.="<img src='/userfiles/avatar/no_photo.jpg'>"; }}
	$text.="</div><div class='UserComTexts'>"; if ($GLOBAL["USER"]["id"]==0) { $text.="<div class='UserComNameDiv'><input class='UserComName' type='text' placeholder='Введите свое имя' value='".$_SESSION["username"]."' /><p class='or'>или авторизуйтесь</p>".GetUserAuthForm()."</div>".$C5; }
	$text.="<div class='UserComAnswer'></div><div class='UserComAnswerC'></div>".$C."<div class='UserComTextDiv'><textarea class='UserComText' placeholder='Введите текст комментария'></textarea>".$smiles."<div class='Info'>Допускаются теги &lt;b&gt;, &lt;i&gt;, &lt;u&gt;, &lt;p&gt; и ссылки на YouTube (http://youtube.com/watch?v=VIDEO_ID)</div></div>";
	if ($UserSetsSite[7]==1) { $text.='<link href="/modules/standart/multiupload/client/uploader2.css" type="text/css" rel="stylesheet"><script type="text/javascript" src="/modules/standart/multiupload/client/uploader.js"></script>';
	$text.=$C5.'<div id="uploadercom"></div><div class="Info" style="float:left; padding:10px;">Прикрепите фотографии (jpg, gif и png)</div><div id="uploadercompics" style="display:none;"></div>'.$C5.''; }
	if ($GLOBAL["USER"]["id"]==0 && $UserSetsSite[5]==1) { $text.='<div class="MiniInput"><img src="/modules/standart/captcha/Captcha.php?'.time().'" class="captchaImg" /><input name="captcha" class="UserComCaptcha" type="text"></div>'; }
	$text.=$C5."<div class='CommentSend'><input type='submit' name='sendbutton' class='SaveButton' value='Добавить комментарий' onClick=\"SendUserComment($(this).parents('.UserCommentsForm'));\"></div>";
	if ($VARS["commenttext"]!="") { $text.=$C10."<div class='Info' style='color:#666;'>".$VARS["commenttext"]."</div>"; } $text.=$C."</div>"; return($text);
}
?>