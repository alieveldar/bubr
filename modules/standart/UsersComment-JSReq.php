<?
session_start(); $dir=explode("/", $_SERVER['HTTP_REFERER']); $HTTPREFERER=$dir[2];

if ($HTTPREFERER==$_SERVER['SERVER_NAME']) {
	
	$GLOBAL["sitekey"]=1; $error=0;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Settings.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	
	// полученные данные ================================================
	
	$R = $_REQUEST;
	
	$p = strtolower($_SESSION["CaptchaCode"]);
	$uid = (int)$_SESSION["userid"];
	$ufrom = (int)$_SESSION["userfrom"];
	
if($R["action"] == 'spam'){
			
		if($uid){
			@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/MailSend.php";
			$subject = 'Спам в комментариях'; $body = 'Посмотреть комментарий по <a href="'.$R["link"].'#comment'.(int)$R["id"].'">ссылке</a>';
			if(function_exists('MailSend') && MailSend($VARS["adminmail"], $subject, $body, $VARS["sitemail"])) $result ='ok';
			else $result ='error';
		}
		
} else {
	
	$t=$R["t"]; $t=str_replace("'", "&#039;", $t); $t=trim(strip_tags($t, "<b><i><u><q><p>"));
	
	$an = $R["an"]; if ($an!="") {
		preg_match_all('/(comment)\d+/', $R["an"], $matches);
		$toid = preg_replace('/comment/', '', $matches[0][0]); 
	}
	
	$fp=@fopen($_SERVER['DOCUMENT_ROOT']."/comments-debug.txt", "a"); @fwrite($fp, $t."\n\r"); @fclose($fp);
	
	$n = trim($R["n"]); if(!$_SESSION["userid"]) { $_SESSION["username"] = $n; } $n=preg_replace('/[\.\(\)\;\:\-]+/i','',$n);
	$c = strtolower(Dbsel(trim($R["c"])));
	$f = trim($R["f"], ";"); $f=preg_replace('/[^a-zA-Z0-9\-\;\.]+/i','',$f); $fl = explode(";", $f);
	$l = Dbsel(trim($dir[3])); $l=preg_replace('/[^a-zA-Z0-9]+/i','',$l);
	$i = (int)$dir[5];
	
	
	$UserSetsSite=explode("|", $_SESSION["UserSetsSiteS"]);
	#3 Разрешить комментарии к материалам
	#4 Разрешить комментарии от анонимов
	#5 Запрашивать у анонимов CAPTCHA
	#6 Разрешить подписи в комментариях
	#7 Разрешить вложения в комментариях материалов
	
	// проверки =========================================================
	
	if ($UserSetsSite[3]==1) {
		if ($t=="") { $error=1; $result=array("Code"=>0, "Text"=>"Введите текст комментария", "Class"=>"ErrorDiv", "Comment"=>""); }
		if ($uid==0 && $UserSetsSite[4]==0 && $error==0) { $error=1; $result=array("Code"=>0, "Text"=>"Для добавления комментария необходимо авторизоваться", "Class"=>"ErrorDiv", "Comment"=>""); }
		if (checkAttrs($t) && $error==0) { $error=1; $result=array("Code"=>0, "Text"=>"Запрещается использовать теги кроме &lt;b&gt;, &lt;i&gt;, &lt;u&gt;, &lt;p&gt;, а также прописывать в них атрибуты", "Class"=>"ErrorDiv", "Comment"=>""); }
		/* СПАМ */ $cnt=checkSpam($t); if ($cnt>=2 && $error==0) {	$error=1; $result=array("Code"=>10, "Text"=>"Спасибо за SPAM! [filter=$cnt]", "Class"=>"ErrorDiv", "Comment"=>""); DB("UPDATE `_users` SET `stat`='0' WHERE (`id`='".$uid."') LIMIT 1"); $_SESSION["userid"]=0;  $_SESSION['userrole']=0; }
		if ($l=="" && $error==0) { $error=1; $result=array("Code"=>0, "Text"=>"Не определен раздел комментария", "Class"=>"ErrorDiv", "Comment"=>""); }
		if ($i==0 && $error==0) { $error=1; $result=array("Code"=>0, "Text"=>"Не определен материал комментария", "Class"=>"ErrorDiv", "Comment"=>""); }
		if ($uid==0 && $UserSetsSite[4]==1 && $UserSetsSite[5]==1 && $c!=$p && $error==0) { $error=1; $result=array("Code"=>0, "Text"=>"Введен неверный защитный код!", "Class"=>"ErrorDiv", "Comment"=>""); }
		if ($error==0) {
			$d=DB("SELECT `comments` FROM `".$l."_lenta` WHERE (`id`='".$i."') LIMIT 1"); if ($d["total"]!=1) { $error=1; $result=array("Code"=>0, "Text"=>"Материал отсутствует на сайте", "Class"=>"ErrorDiv", "Comment"=>""); 
			} else { mysql_data_seek($d["result"],0); $ar=mysql_fetch_array($d["result"]); if($ar["comments"]!=0 && $_SESSION["userrole"]<1){ $error=1; $result=array("Code"=>0, "Text"=>"В данном материалы запрещены комментарии", "Class"=>"ErrorDiv", "Comment"=>'');}}
		}
	}
	// операции =========================================================

	if ($error==0 && $UserSetsSite[3]==1) {
		$text="";
		
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
	
		### Вставляем в базу данных
		DB("INSERT INTO `_comments` (`link`,`pid`,`uid`,`uname`,`from`,`data`,`toid`,`text`,`ip`,`referer`)
		VALUES
		('".$l."', '".$i."', '".$uid."', '".$n."', '".$ufrom."', '".time()."', '".$toid."', '".$t."', '".$GLOBAL["ip"]."', '".$_SESSION['userreferer']."');");
		$last=DBL();
		
		DB("UPDATE `".$l."_lenta` set `comcount`=`comcount`+1 WHERE (`id`='".$i."') LIMIT 1");
		DB("UPDATE `".$l."_lenta` set `update`='".time()."', `clast`='".$last."' WHERE (`id`='".$i."') LIMIT 1"); /* Для форума */

		
		### обновляем трекер
		if ($uid!=0) { DB("INSERT INTO `_tracker` (`uid`,`link`,`pid`,`data`,`stat`) VALUES ('".$uid."','".$l."','".$i."','".time()."', '0') ON DUPLICATE KEY UPDATE `data`='".time()."'"); }
		DB("UPDATE `_tracker` SET `stat`='1', `data`='".time()."' WHERE (`link`='".$l."' AND `pid`='".$i."' AND `uid`<>'$uid')");
		
		### Вложения
		if ($UserSetsSite[7]==1) { foreach($fl as $k=>$pic) { if (is_file($_SERVER['DOCUMENT_ROOT']."/userfiles/temp/".$pic)) {
		@rename($_SERVER['DOCUMENT_ROOT']."/userfiles/temp/".$pic, $_SERVER['DOCUMENT_ROOT']."/userfiles/comoriginal/".$pic);
		DoPicturePreview($pic); DB("INSERT INTO `_commentf` (`pid`,`uid`,`pic`) VALUES ('".$last."','".$uid."','".$pic."');"); }}}
		
		if ($UserSetsSite[7]==1 && $toid) { $f=DB("SELECT `pic`, `pid` FROM `_commentf` WHERE (`pid`=".$toid.") ORDER BY `id` ASC"); $fl2 = array();
		for ($j=0; $j<$f["total"]; $j++) { @mysql_data_seek($f["result"],$j); $f_=@mysql_fetch_array($f["result"]); $fl2[]=$f_["pic"]; }}
		
		### Формируем Div с комментарием
		$data=DB("SELECT `comments1`.*, `comments2`.`uid` AS `touid`, `comments2`.`text` AS `totext`, `users1`.`nick`, `users1`.`spectitle`, `users1`.`avatar`, `users1`.`signature`, `users1`.`role`, `users1`.`karma`, `users1`.`created`, `users2`.`nick` AS `tonick` FROM `_comments` AS `comments1`
		LEFT JOIN `_users` AS users1 ON `users1`.`id`=`comments1`.`uid` LEFT JOIN `_comments` AS `comments2` ON `comments2`.`id`=`comments1`.`toid` LEFT JOIN `_users` AS `users2` ON `users2`.`id`=`comments2`.`uid` WHERE (`comments1`.`id`='".(int)$last."') GROUP BY 1 LIMIT 1");
		### Вывод комментариев
		for ($j=0; $j<$data["total"]; $j++) { @mysql_data_seek($data["result"],$j); $com=@mysql_fetch_array($data["result"]); $datar=ToRusData($com["data"]); $medal=""; $cid=$com["id"];
		
		if ($com["uid"]==0) { $com["nick"]="<span id='UserIdComment-".$com["id"]."' class='UserComName'>".($com["uname"] ? $com["uname"] : "Горожанин")."</span>"; $avatar="<img src='/userfiles/avatar/no_photo.jpg'>";
		} else { $com["nick"]="<a target='_blank' href='/users/view/".$com["uid"]."/'><span id='UserIdComment-".$com["id"]."' class='UserComName'>".$com["nick"]."</span></a>";
		
		if (is_file($_SERVER['DOCUMENT_ROOT']."/".$com["avatar"]) && filesize($_SERVER['DOCUMENT_ROOT']."/".$com["avatar"])>100  && $com["avatar"]!="" && $com["avatar"]!="/") { $avatar="<a target='_blank' href='/users/view/".$com["uid"]."/'><img src='/".$com["avatar"]."'></a>";
		} else { $avatar="<a target='_blank' href='/users/view/".$com["uid"]."/'><img src='/userfiles/avatar/no_photo.jpg'></a>"; }}
		
		$answer="<span class='CommentAdmin' id='CommentAdmin-".$com["id"]."-".($com["uid"] ? $com["uid"] : str_replace(".", "", $com["ip"]))."'></span>";
		
		
		if($com["toid"]){
			$toComment = $C."<div class='quote-container'><b>В ответ на <a href='#comment".$com["toid"]."'><u>комментарий</u></a> пользователя <a href='/users/view/".$com["touid"]."'><u>".$com["tonick"]."</u></a></b><div class='C5'></div><div><div class='quote-overview short'><div class='quote'>".nl2br($com["totext"]);
			if (count($inc[$com["toid"]])>0) { $toComment.=$C10."<div class='CommentInc'>"; foreach($inc[$com["toid"]] as $k=>$pic) {
			$toComment.="<a href='/userfiles/comoriginal/".$pic."' rel='prettyPhoto[gallery]'><img src='/userfiles/compreview/".$pic."' /></a>"; } $toComment.="<div class='C'></div></div>"; }
			$toComment .= "</div></div><div class='ToggleShow'><a href='javascript:void(0)' onclick='$(\".quote-overview\", $(this).parents(\".quote-container\")).removeClass(\"short\"); $(this).parents(\".ToggleShow\").hide()'>Развернуть</a></div></div></div>".$C5;
		}


		$comment = nl2br($com["text"]); $youtubePattern = '/(http:\/\/)?((www.youtube.com)|(youtu.be))\/(\S)+/i';

		if(preg_match_all($youtubePattern, strip_tags($comment), $output)) { 
			foreach($output[0] as $url){
				if(preg_match('/youtu.be/', $url)){ $tmp_url = explode('/', $url); $video_id = $tmp_url[count($tmp_url) - 1];
				} else{ preg_match('/v=[^&]+/', $url, $matches); $video_id = str_replace('v=', '', $matches[0]); } 
				$embed = '<br /><object width="500" height="390"><param name="movie" value="http://www.youtube.com/v/'.$video_id.'&hl=en&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$video_id.'&hl=en&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="480" height="390"></embed></object>'; 
				$comment = str_replace($url, $embed, $comment); 
			}
		}

		
		$toComment = strtr($toComment, array("&#039;" => "'"));	$toComment = strtr($toComment, $smiles);
		$comment = strtr($comment, array("&#039;" => "'"));	$comment = strtr($comment, $smiles);
		 
		$text.="<a name='comment".$com["id"]."' id='comment".$com["id"]."'></a>";
		$text.="<div class='CommentItem' id='CommentItem-".$com["id"]."'>";
			$text.="<div class='LeftInfo'>".$avatar."<b>".$com["nick"]."</b>".$datar[1]."</div>";
			$text.="<div class='RightInfo'>".$answer."</div>".$C10;
			$text.=$toComment;
			$text.="<div class='view1'>".$comment."</div><div class='view2'></div>".$C10;
			/* Вложения */ if (count($inc[$cid])>0) { $text.="<div class='CommentInc'>"; foreach($inc[$cid] as $k=>$pic) { $text.="<a href='/userfiles/comoriginal/".$pic."' rel='prettyPhoto[gallery]'><img src='/userfiles/compreview/".$pic."' /></a>";	} $text.=$C."</div>".$C10; }
			/* Кнопка ответить */ $text.=$C5."<div class='CommentAnswer' id='CommentAnswer-".$com["id"]."'></div></i></b>";
		$text.=$C."</div>";
		
		} @unlink($_SERVER['DOCUMENT_ROOT']."/cache/user_comments/".$l.".".$i.".cache"); // Удаляем кеш комментариев
		$result=array("Code"=>1, "Text"=>"Ваш комментарий успешно добавлен! [$cnt]", "Class"=>"SuccessDiv", "Comment"=>$text);
	}
	
}
	
} else { $result=array("Code"=>0, "Text"=>"--- Security alert ---", "Class"=>"ErrorDiv", "Comment"=>''); }

// отправляемые данные ==============================================
$GLOBALS['_RESULT']	= $result;

function GL($t){ @file_put_contents($_SERVER['DOCUMENT_ROOT']."/modules/standart/debug-comment-add.txt", $t); }

/* делаем превью для прил. файлов */ 
function DoPicturePreview($pic) {
	$src=$_SERVER['DOCUMENT_ROOT']."/userfiles/comoriginal/".$pic; $src2=$_SERVER['DOCUMENT_ROOT']."/userfiles/compreview/".$pic;
	list($w,$h)=getimagesize($src); $k=$w/$h; if ($w>200){ $sw=200; $sh=$sw/$k; $tmp=explode(".", $pic); $c=count($tmp)-1; $ext=$tmp[$c];
	if ($ext=="jpg") { $image=imagecreatefromjpeg($src); } if ($ext=="gif") { $image=imagecreatefromgif($src); } if ($ext=="png") { $image=imagecreatefrompng($src); }
	$im=imagecreatetruecolor($sw, $sh); imagecopyresampled($im, $image, 0, 0, 0, 0, $sw, $sh, $w, $h);
	if ($ext=="jpg") { imagejpeg($im, $src2, 80); } if ($ext=="gif") { imagegif($im, $src2); } if ($ext=="png") { imagepng($im, $src2); }
	} else { @copy($src, $src2); } return true;
} 

/* проверка атрибутов тегов*/
function checkAttrs($string){
	preg_match_all('/<.*?>/', $string, $tags);
	if(!$tags) return false;
	foreach($tags[0] as $tag){
		$tag = str_replace(array('<', '>', '/', ' '), '', strtolower($tag));
		if(!in_array($tag, array('b', 'i', 'u', 'p'))) return true;
	}
}