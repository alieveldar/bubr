<?
if ($start=="") { $start="forums"; $dir[1]="forums"; }
$file=$link."-".$start.".".$page.".".$id; $edit="";
$folder="/userfiles/wysiwygimages/";

$TABLES=array(
	0=>$link."_forum",
	1=>$link."_cat",
	2=>$link."_lenta"
);

if ($GLOBAL["USER"]["role"]==10) {
	$cap="Раздел сайта временно закрыт";
	$text="<b>...</b>";
} else {
# ОСНОВНАЯ ЧАСТЬ ############################################################################################################################################
/* Вывод главной страницы */
if ($start=="forums") {
	$VARS["cachepages"]=1; /* Задаем спец. кэш для главной форума  */
	if (RetCache($file)=="true") { list($text, $cap)=GetCache($file, ""); } else { list($text, $cap)=GetForumForums(); SetCache($file, $text, ""); }
	$cap=$node["name"]; $edit="<div id='AdminEditItem'><a href='".$GLOBAL["mdomain"]."/admin/?cat=".$link."_list'>Редактировать форум</a></div>"; 
}
/* Вывод списка тем */
if ($start=="cat") 	{
	$VARS["cachepages"]=1; /* Задаем спец. кэш для тем форума  */
	if (RetCache($file)=="true") { list($text, $cap)=GetCache($file, $cap); } else { list($text, $cap)=GetForumCategory(); SetCache($file, $text, $cap); }
}
/* Вывод темы и комментариев */
if ($start=="view") {
	$VARS["cachepages"]=1; /* Задаем спец. кэш для темы форума  */
	$q="SELECT `name`, `stat`, `comments`, `uid`, `cid` FROM `".$TABLES[2]."` WHERE (`id`='".(int)$page."' && `stat`='1') LIMIT 1"; $data=DB($q);
	
	if ($data["total"]==1) {
		/* вывод темы */		if (RetCache($file)=="true") { list($text, $cap)=GetCache($file, 0); } else { list($text, $cap)=GetForumTheme(); SetCache($file, $text, ""); }
		/* настройки темы */	@mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $cap=$ar["name"];
		/* кнопки */			$text="<div class='ForumLink'><a href='/$link/cat/$ar[cid]'>Вернуться к темам</a></div><div class='ForumLink'><a href='/$link/'>Вернуться в форум</a></div>".$text;		
		/* Комментарии */		if ($GLOBAL["USER"]["id"]!=0) { UserTracker($link, $page); } $text.=UsersComments($link, $page, $ar["comments"], 0);
		/* кнопка редактор */	if ($GLOBAL["USER"]["role"]>=1 || $ar["uid"]==$GLOBAL["USER"]["id"]) { $text="<div class='ForumRink'><a href='/$link/edittheme/".(int)$page."'>Редактировать</a></div>".$text;
		/* кнопка удаления */	if ($GLOBAL["USER"]["role"]>=1) { $text="<div class='ForumDink'><a href='/$link/deltheme/".(int)$page."'>Удалить тему</a></div>".$text; }} 
	} else {
		$cap="Тема не найдена или закрыта"; $text=@file_get_contents($ROOT."/template/404.html");
	}
}
/* добавление темы */		if ($start=="addtheme") { list($text, $cap)=AddForumTheme(); }
/* редактирование темы */	if ($start=="edittheme") { list($text, $cap)=EditForumTheme(); }
/* удаление темы, подтв */	if ($start=="deltheme") { list($text, $cap)=DeleteForumTheme(); }
/* удаление темы OK */		if ($start=="delthemeconfirm") { list($text, $cap)=DeleteForumThemeOK(); } 
}
# ПРАВАЯ ЧАСТЬ ############################################################################################################################################
//$Page["TopContent"]=TimeLine();
$Page["TopContent"]="";

###########################################################################################################################################################
if ($GLOBAL["USER"]["role"]>9) { $Page["Content"]=$C10.$edit.$C.$text; } else { $Page["Content"]=$text; } $Page["Caption"]=$cap; 
###########################################################################################################################################################

function GetForumForums() {
	global $TABLES, $C5; $text="";
	// Пересчет статистики
	ReCountAllCategory($cat["fid"]);
	// СПИСОК КАТЕГОРИЙ (ВЕТОК)
	$q="SELECT `".$TABLES[1]."`.`id`, `".$TABLES[1]."`.`fid`, `".$TABLES[1]."`.`name`, `".$TABLES[1]."`.`update`, `".$TABLES[1]."`.`uid`, `".$TABLES[1]."`.`text`, `".$TABLES[1]."`.`tcnt`, `".$TABLES[1]."`.`comcount`, `_users`.`nick` 
	FROM `".$TABLES[1]."` LEFT JOIN `_users` ON `_users`.`id`=`".$TABLES[1]."`.`uid` WHERE (`".$TABLES[1]."`.`stat`=1) GROUP BY 1 ORDER BY `".$TABLES[1]."`.`lock` DESC, `".$TABLES[1]."`.`rate` DESC"; $data=DB($q);
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $fid=$ar["fid"]; $items[$fid][]=$ar; endfor;
	// СПИСОК ФОРУМОВ (ИНТЕРЕСОВ)
	$data=DB("SELECT `id`,`name` FROM `".$TABLES[0]."` WHERE (`stat`=1) ORDER BY `rate` DESC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); 
		$text.="<div class='WhiteBlock'><div class='ForumName'>".$ar["name"]."</div><div class='C5'></div><table class='ForumTable' cellpadding='0' cellspacing='0'>";
		$text.="<tr class='TableHeader'><td>Рубрики форума</td><td width='1%'> Темы </td><td width='1%'> Ответы </td><td width='1%'>Последнее обновление</td></tr>".ForumChild($items[$ar["id"]], $ar["id"]);
		$text.="</table></div><div class='C10'></div>";
	endfor;
	$istime=time()-60*10; $text.="<h3>Сейчас на сайте:</h3>".$C5."<div class='WhiteBlock'>"; $data=DB("SELECT `id`,`nick` FROM `_users` WHERE (`lasttime`>'".$istime."') ORDER BY `lasttime` DESC LIMIT 100"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
	$text.=" <a href='/users/view/".$ar["id"]."' style='font-size:10px; margin:1px 7px 1px 0;'><u>".$ar["nick"]."</u></a> "; endfor; $text.="</div>"; return(array($text, ""));
}

###########################################################################################################################################################

function GetForumCategory() {
	global $TABLES, $GLOBAL, $page, $ROOT, $node, $dir, $C10, $C, $C5; $text=""; $onpage=$node["onpage"]; $pg = $dir[3]?$dir[3]:1; $from=($pg-1)*$onpage;
	$q="SELECT `".$TABLES[1]."`.`id`, `".$TABLES[1]."`.`fid`, `".$TABLES[1]."`.`name`, `".$TABLES[1]."`.`add`, `".$TABLES[0]."`.`name` as `fname`, `".$TABLES[0]."`.`add` as `fadd`	FROM `".$TABLES[1]."`
	LEFT JOIN `".$TABLES[0]."` ON `".$TABLES[0]."`.`id`=`".$TABLES[1]."`.`fid` WHERE (`".$TABLES[1]."`.`stat`=1 && `".$TABLES[1]."`.`id`='".(int)$page."') GROUP BY 1 LIMIT 1"; $data=DB($q);
	if ($data["total"]!=1) { $cap="Категория не найдена"; $text=@file_get_contents($ROOT."/template/404.html"); } else { @mysql_data_seek($data["result"], 0); $cat=@mysql_fetch_array($data["result"]);
		$cap=$cat["name"]; $text="<div class='Crumbs' style='margin-top:-5px;'><a href='/".$node["link"]."'>".$node["name"]."</a> &raquo; ".$cat["fname"]." &raquo; <a href='/".$node["link"]."/cat/$page'>".$cap."</a> &raquo; Страница $pg</div>";
		$pager=Pager2($pg, $onpage, ceil($cat["tcnt"]/$onpage), $dir[0]."/".$dir[1]."/".$dir[2]."/[page]");
		if ($cat["add"]*$cat["fadd"]==1 || $GLOBAL["USER"]["role"]>1) { $add="<div class='AddForumTheme'><a href='/$dir[0]/addtheme/$cat[id]' rel='nofollow'>Создать новую тему</a></div>"; } else { $add=""; }
		if ($add!="" || $pager!="") { $text.=$C10.$add."<div class='PagerForumTheme'>".$pager."</div>".$C.$C5; } $text.="<div class='WhiteBlock'><table class='ForumTable' cellpadding='0' cellspacing='0'>";
		/* --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ----- */
		$text.="<tr class='TableHeader'><td>Темы форума</td><td width='1%'>Ответы</td><td width='1%'>Последнее обновление</td></tr>";
		$q="SELECT `".$TABLES[2]."`.`id`, `".$TABLES[2]."`.`name`, `".$TABLES[2]."`.`data`, `".$TABLES[2]."`.`update`, `".$TABLES[2]."`.`uid`, `".$TABLES[2]."`.`lock`, `".$TABLES[2]."`.`comcount`, `".$TABLES[2]."`.`clast`, `_comments`.`uid` as `updater_id`, `u1`.`nick` as `creator`, `u2`.`nick` as `updater`   
		FROM `".$TABLES[2]."` LEFT JOIN `_comments` ON `_comments`.`id`=`".$TABLES[2]."`.`clast` LEFT JOIN `_users` `u1` ON `u1`.`id`=`".$TABLES[2]."`.`uid` LEFT JOIN `_users` `u2` ON `u2`.`id`=`_comments`.`uid`
		WHERE (`".$TABLES[2]."`.`stat`=1 && `".$TABLES[2]."`.`cid`='".(int)$page."') GROUP BY 1 ORDER BY `".$TABLES[2]."`.`lock` DESC, `".$TABLES[2]."`.`update` DESC LIMIT $from, $onpage"; $data=DB($q); /* $text.=$q; */
		for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); if ($ar["lock"]==1) { $ads="LockedTheme"; $adt="Тема закреплена: ".$ar["name"]; } else { $ads=""; $adt=$ar["name"]; }
		$text.="<tr><td class='ForumItem0 $ads' title='$adt'><h2 class='SmallH2'><a href='/".$node["link"]."/view/".$ar["id"]."'>".trim($ar["name"])."</a></h2><span class='Update'>Создано: ".ForumUpdatedBy($ar["uid"], $ar["creator"], $ar["data"], 1)."</span></td>";
		$text.="<td class='Digit'><a href='/".$node["link"]."/view/".$ar["id"]."'>".(int)$ar["comcount"]."</a></td><td class='Update'>".ForumUpdatedBy($ar["updater_id"], $ar["updater"], $ar["update"])."</td></tr>"; endfor;
		/* --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ----- */		
		$text.="</table></div><div class='C10'></div>"; if ($add!="" || $pager!="") { $text.=$C10.$add."<div class='PagerForumTheme'>".$pager."</div>".$C5; }
	} return(array($text, $cap));
}

###########################################################################################################################################################

function GetForumTheme() {
	global $TABLES, $page, $ROOT, $node, $dir, $C10, $C, $C5, $UserSetsSite;
	$q="SELECT `".$TABLES[2]."`.`id`, `".$TABLES[2]."`.`fid`, `".$TABLES[2]."`.`uid`, `".$TABLES[2]."`.`cid`, `".$TABLES[2]."`.`data`, `".$TABLES[2]."`.`name`, `".$TABLES[2]."`.`text`, `".$TABLES[0]."`.`name` as `fname`, `".$TABLES[1]."`.`name` as `cname`, `_users`.`nick`, `_users`.`avatar`, `_users`.`role`, `_users`.`signature`
	FROM `".$TABLES[2]."` LEFT JOIN `".$TABLES[0]."` ON `".$TABLES[0]."`.`id`=`".$TABLES[2]."`.`fid` LEFT JOIN `".$TABLES[1]."` ON `".$TABLES[1]."`.`id`=`".$TABLES[2]."`.`cid` LEFT JOIN `_users` ON `_users`.`id`=`".$TABLES[2]."`.`uid` 
	WHERE (`".$TABLES[2]."`.`stat`=1 && `".$TABLES[2]."`.`id`='".(int)$page."') GROUP BY 1 LIMIT 1"; $data=DB($q); if ($data["total"]!=1) { $cap="Тема не найдена"; $text=@file_get_contents($ROOT."/template/404.html"); } else {
		@mysql_data_seek($data["result"], 0); $theme=@mysql_fetch_array($data["result"]); $cap=$theme["name"]; 
		$text=$C10."<div class='Crumbs' style='margin-top:-5px;'><a href='/".$node["link"]."'>".$node["name"]."</a> &raquo; ".$theme["fname"]." &raquo; <a href='/".$node["link"]."/cat/".$theme["cid"]."'>".$theme["cname"]."</a> &raquo; ".$cap."</div>";
		$datar=ToRusData($theme["data"]); $theme["text"]=str_replace("<a", "<a rel='nofollow' ", $theme["text"]); if ($theme["text"]=="") { $theme["text"]=$cap; }
		//if ($theme["role"]>0) { $medal="<img src='/template/standart/medal/".$theme["role"].".gif' title='".$GLOBAL["roles"][$theme["role"]]."'>"; }
		if ($theme["uid"]==0) { if ($theme["nick"]=="") { $theme["nick"]="<span class='UserComName'>Горожанин</span>"; } $avatar="<img src='/userfiles/avatar/no_photo.jpg'>"; } else { $theme["nick"]="<a target='_blank' href='/users/view/".$theme["uid"]."/'><span class='UserComName'>".$theme["nick"]."</span></a>"; 
		if (is_file($_SERVER['DOCUMENT_ROOT']."/".$theme["avatar"]) && filesize($_SERVER['DOCUMENT_ROOT']."/".$theme["avatar"])>100  && $theme["avatar"]!="" && $theme["avatar"]!="/") { $avatar="<a target='_blank' href='/users/view/".$theme["uid"]."/'><img src='/".$theme["avatar"]."'></a>"; } else { $avatar="<a target='_blank' href='/users/view/".$theme["uid"]."/'><img src='/userfiles/avatar/no_photo.jpg'></a>"; }}
		### Фото-альбом
		$p=DB("SELECT * FROM `_widget_pics` WHERE (`pid`='".(int)$page."' && `link`='".$node["link"]."' && `point`='theme' && `stat`=1) order by `rate` ASC"); if ($p["total"]>0) { $album="<div class='ItemAlbum'>"; for ($i=0; $i<$p["total"]; $i++): mysql_data_seek($p["result"],$i); $ar=@mysql_fetch_array($p["result"]); 
			$album.="<a href='/userfiles/wysiwygimages/".$ar["pic"]."' title='".$ar["name"]."' rel='prettyPhoto[gallery]'><img src='/userfiles/wysiwygimages/".$ar["pic"]."' title='".$ar["name"]."' alt='".$ar["name"]."'></a>"; endfor; $album.="</div>".$C; }
			$text.="<div id='UserCommentsList2' class='ForumThemeText'>";
			$text.="<div class='CommentItem'><div class='LeftItem'>".$theme["nick"]."<div class='C'></div><div class='AvatarU'>".$avatar."</div><div class='Medal'>".$medal."</div><div class='C5'></div>";
			if ($theme["spectitle"]!="") { $text.="<i>".$theme["spectitle"]."</i>"; } $text.="<div class='C5'></div></div>"; $text.="<div class='RightItem'><div class='Data'>".$datar[1]."</div><div class='view1'>".YouTubeCreate($theme["text"])."</div><div class='view2'></div><div class='C10'></div>";
			if ($album!="") { $text.=$album.$C; } if ($theme["signature"]!="" && $UserSetsSite[6]==1) { $text.="<div class='Signature'>".$theme["signature"]."</div>"; } $text.="</div>".$C."</div>";	
		$text.="</div>".$C10;
		
$text.='<div style="text-align:center;"><script>
(function(d,c){
  var up=d.createElement(\'script\'),
  s=d.getElementsByTagName(\'script\')[0],
  r=Math.floor(Math.random() * 1000000);
  var cmp = c + Math.floor(Math.random() * 10000);
  var url = window.location.href;
  window["__uptolike_widgets_settings_"+cmp] = {};
  d.write("<div id=\'"+cmp+"\' class=\'__uptlk\' data-uptlkwdgtId=\'"+r+"\'></div>");
  up.type = \'text/javascript\'; up.async = true;
  up.src = "//w.uptolike.com/widgets/v1/widgets.js?b=fb.tw.ok.vk.gp.mr.lj.my&id=40337&o=1&m=1&sf=2&ss=2&sst=1&c1=ededed&c1a=0.0&c3=ff9300&mc=1&c2=000000&c4=ffffff&c4a=1.0&mcs=1&sel=1&fol=0&c=" + cmp + "&url="+encodeURIComponent(url);
  s.parentNode.insertBefore(up, s);
})(document,"__uptlk");
</script></div>'.$C10;
		
		
	} return(array($text, $cap));
}

########################################################################################################################################################### 

function AddForumTheme() {
	global $TABLES, $page, $ROOT, $node, $dir, $C10, $C, $C5, $GLOBAL, $RealPage, $folder; $P=$_SESSION['Data'];
	if ($GLOBAL["USER"]["id"]==0) { $cap="Авторизация"; $text="Для создания новой темы необходимо авторизоваться".$C10.GetUserAuthForm(); } else {
	$q="SELECT `".$TABLES[1]."`.`id`, `".$TABLES[1]."`.`fid`, `".$TABLES[1]."`.`name`, `".$TABLES[1]."`.`add`, `".$TABLES[0]."`.`name` as `fname`, `".$TABLES[0]."`.`add` as `fadd`
	FROM `".$TABLES[1]."` LEFT JOIN `".$TABLES[0]."` ON `".$TABLES[0]."`.`id`=`".$TABLES[1]."`.`fid` WHERE (`".$TABLES[1]."`.`stat`=1 && `".$TABLES[1]."`.`id`='".(int)$page."') GROUP BY 1 LIMIT 1"; $data=DB($q);
	if ($data["total"]!=1) { $cap="Категория не найдена"; $text=@file_get_contents($ROOT."/template/404.html"); } else { @mysql_data_seek($data["result"], 0); $cat=@mysql_fetch_array($data["result"]);
	if ($cat["add"]*$cat["fadd"]!=1 && $GLOBAL["USER"]["role"]<1) { $cap="Категория закрыта"; $text="Темы в эту категорию могут добавлять только администраторы"; } else {
	/* ОБРАБОТКА ВВЕДЕННЫХ ФОРМ -- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
	if (isset($P["sendbutton"])) { $newname = trim(Dbcut(strip_tags($P['newname']))); $newtext=trim(Dbcut($P['newtext']));
		$newtext = strip_tags($newtext, "<a><b><i><s><strike><object><li><ul><ol><br><p><span><img><video><embed><param><hr><strong><h3><u><table><tr><td>"); $newtext=trim(str_replace(array("<p></p>", "<p><br></p>", "<p>&nbsp;</p>"), "", $newtext));
		if ($newname=="") { $_SESSION["Msg"]='<div class="ErrorDiv">Внимание! Поле названия темы обязательно к заполнению. Тэги игнорируются!</div>'; } else {
			$q="INSERT INTO `".$TABLES[2]."` (`cid`, `fid`, `uid`, `name`, `text`, `data`, `update`) VALUES ('".(int)$cat["id"]."', '".(int)$cat["fid"]."', '".(int)$GLOBAL["USER"]["id"]."', '".$newname."', '".$newtext."', '".time()."', '".time()."');"; DB($q); $last=DBL();	
			if ((int)$last!=0) { $q="UPDATE `".$TABLES[1]."` SET `uid`='".(int)$GLOBAL["USER"]["id"]."', `update`='".time()."', `tcnt`=`tcnt`+1 WHERE (`id`='".(int)$cat["id"]."') LIMIT 1"; DB($q);
				if ($GLOBAL["USER"]["role"]>1) { $q="UPDATE `".$TABLES[2]."` SET `uid`='".(int)$P["auththeme"]."', `stat`='".(int)$P["stattheme"]."', `comments`='".(int)$P["comtheme"]."', `lock`='".(int)$P["locktheme"]."' WHERE (`id`='".(int)$last."') LIMIT 1"; DB($q); }
				if($P["attachment"]){ if (!is_dir($ROOT.$folder.date("Ym"))) { mkdir($ROOT.$folder.date("Ym"), 0777); } foreach ($P["attachment"] as $pic) { $q="INSERT INTO `_widget_pics` (`pid`, `link`, `pic`, `stat`, `point`, `data`) values ('".$last."', '".$node["link"]."', '".date("Ym")."/".$pic."', '1' ,'theme', '".time()."')"; DB($q); @copy($ROOT."/userfiles/temp/".$pic, $ROOT.$folder.date("Ym")."/".$pic); /* echo($ROOT."/userfiles/temp/".$pic." -> ".$ROOT.$folder.date("Ym")."/".$pic); */ }}
				$q="INSERT INTO `_tracker` (`uid`,`link`,`pid`,`data`,`stat`) VALUES ('".(int)$GLOBAL["USER"]["id"]."','".$node["link"]."','".$last."','".time()."', '0')"; //DB($q); DB("UPDATE `_users` SET ='' WHERE (`id`='')"); 
				$_SESSION["Msg"]="<div class='SuccessDiv'>Ваша тема успешно добавлена! <a href='/$node[link]/view/$last/'><u>Перейти к созданной теме</u></a></div>";
			} else { $_SESSION["Msg"]='<div class="ErrorDiv">Внимание! Непредвиденная ошибка, обратитесь к администратору сайта!</div>'; }}
	SD(); }
	/* --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
	$cap="Создание новой темы"; $text="<div class='Crumbs' style='margin-top:-5px;'><a href='/$node[link]'>$node[name]</a> &raquo; $cat[fname] &raquo; <a href='/$node[link]/cat/$page'>$cat[name]</a> &raquo; Создание темы</div>";
	$text.=$C10."<div class='ForumLink'><a href='/$node[link]/cat/$page'>Вернуться в категорию</a></div>".$C."<div class='WhiteBlock'>".$_SESSION["Msg"];
	/* ВЫВОД ФОРМЫ ДОБАВЛЕНИЯ  --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
	$text.='<link media="all" href="/modules/standart/multiupload/client/uploader2.css" type="text/css" rel="stylesheet"><script type="text/javascript" src="/modules/standart/multiupload/client/uploader.js"></script>';
	$text.='<form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post" onsubmit="return JsVerify();"><table>';
	$text.='<tr class="TRLine0"><td class="VarText">Название темы<star>*</star></td><td class="LongInput"><input placeholder="Название темы" maxlength="150" name="newname" type="text" class="JsVerify" style="width:590px;"></td></tr>';
	$text.='<script src="/modules/standart/wysiwyg/editor.js"></script><script src="/modules/standart/wysiwyg/ru.js"></script><link rel="stylesheet" href="/modules/standart/wysiwyg/editor.css" />
	<script>$(document).ready(function() { var buttons=["html","|","bold","italic","deleted","|","unorderedlist","orderedlist","outdent","indent","|","image","video","link","|","alignment","|","horizontalrule","table"];
	var tags=["span", "a", "br", "p", "b", "i", "strike", "u", "img", "video", "object", "embed", "param", "blockquote", "ul", "ol", "li", "hr", "strong", "h3", "table", "tr", "td"];
	$("#newtheme").redactor({focus:true, lang:"ru", buttons:buttons, allowedTags:tags, autoresize:false, imageUpload:"/modules/standart/wysiwyg/imageupload.php" }); })</script><tr><td colspan="2"><textarea id="newtheme" name="newtext"></textarea></td></tr>';
	if ($GLOBAL["USER"]["role"]>=1) {
		 $text.='<tr class="TRLine0"><td class="VarText" valign="top" style="padding-top:5px;">Настройки</td><td>'.$C5.'
		 <input type="text" name="auththeme" value="'.$GLOBAL["USER"]["id"].'" style="width:50px; padding:4px;" /> Автор темы (ID пользователя, ваш ID='.$GLOBAL["USER"]["id"].')'.$C5.'<input type="checkbox" name="stattheme" value="1" checked /> Активная тема (видна пользователям)
		'.$C5.'<input type="checkbox" name="comtheme" value="1" /> Запретить комментарии пользователей к данной теме'.$C5.'<input type="checkbox" name="locktheme" value="1" /> Закрепить тему вверху списка тем в категории</td>';
	}
	
	
	$text.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Прикрепить фотографии</td><td class="LongInput">'.$C10.'<div id="uploader"></div>'.$C5.'<div class="Info">Вы можете загрузить фотографию в форматах jpg, gif и png</div></td></tr>';
	$text.='</table>'.$C10.$C5.'<div class="CenterText"><input type="submit" name="sendbutton" id="sendbutton" class="SaveButton" value="Создать тему"></div>';
	/* --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */	
	$text.="</div>"; }}} return(array($text, $cap));
}

###########################################################################################################################################################

function EditForumTheme() {
	global $TABLES, $page, $ROOT, $node, $dir, $C10, $C, $C5, $GLOBAL, $RealPage, $folder; $P=$_SESSION['Data']; $chreg="";
	if ($GLOBAL["USER"]["id"]==0) { $cap="Авторизация"; $text="Для редактирования темы необходимо авторизоваться".$C10.GetUserAuthForm(); } else {
	$q="SELECT `".$TABLES[2]."`.`uid`, `".$TABLES[2]."`.`cid` FROM `".$TABLES[2]."`	WHERE (`".$TABLES[2]."`.`id`='".(int)$page."') LIMIT 1";
	$data=DB($q); if ($data["total"]!=1) { $cap="Тема не найдена"; $text=@file_get_contents($ROOT."/template/404.html"); } else { @mysql_data_seek($data["result"], 0); $tmp=@mysql_fetch_array($data["result"]);
	if ($GLOBAL["USER"]["id"]==0 || ($GLOBAL["USER"]["role"]<1 AND $tmp["uid"]!=$GLOBAL["USER"]["id"])) { $cap="Доступ закрыт"; $text="Данную тему могут редактировать администраторы и автор темы!"; } else {
	/* ОБРАБОТКА ВВЕДЕННЫХ ФОРМ -- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
	if (isset($P["sendbutton"])) { $newname = trim(Dbcut(strip_tags($P['newname']))); $newtext=trim(Dbcut($P['newtext']));
		$newtext = strip_tags($newtext, "<a><b><i><s><strike><object><li><ul><ol><br><p><span><img><video><embed><param><hr><strong><h3><u><table><tr><td>"); $newtext=trim(str_replace(array("<p></p>", "<p><br></p>", "<p>&nbsp;</p>"), "", $newtext));
		if ($newname=="") { $_SESSION["Msg"]='<div class="ErrorDiv">Внимание! Поле названия темы обязательно к заполнению. Тэги игнорируются!</div>'; } else {
			$q="UPDATE `".$TABLES[2]."` SET `name`='".$newname."', `text`='".$newtext."', `update`='".time()."' WHERE (`id`='".(int)$page."');"; DB($q);
			if ($GLOBAL["USER"]["role"]>1) {
				$oldcid=$tmp["cid"]; list($newfid, $newcid)=explode("-", $P["newcat"]); /* смена категории статьи и обновление стат. обеих категорий */
				$q="UPDATE `".$TABLES[2]."` SET `uid`='".(int)$P["auththeme"]."', `fid`='".(int)$newfid."', `cid`='".(int)$newcid."', `stat`='".(int)$P["stattheme"]."', `comments`='".(int)$P["comtheme"]."', `lock`='".(int)$P["locktheme"]."' WHERE (`id`='".(int)$page."') LIMIT 1"; DB($q);
				if ($oldcid!=$newcid) { ReCountCategory($oldcid); ReCountCategory($newcid); } else { ReCountCategory($newcid); }
			}
			if($P["attachment"]){ if (!is_dir($ROOT.$folder.date("Ym"))) { mkdir($ROOT.$folder.date("Ym"), 0777); } foreach ($P["attachment"] as $pic) { $q="INSERT INTO `_widget_pics` (`pid`, `link`, `pic`, `stat`, `point`, `data`) values ('".(int)$page."', '".$node["link"]."', '".date("Ym")."/".$pic."', '1' ,'theme', '".time()."')"; DB($q); @copy($ROOT."/userfiles/temp/".$pic, $ROOT.$folder.date("Ym")."/".$pic); /* echo($ROOT."/userfiles/temp/".$pic." -> ".$ROOT.$folder.date("Ym")."/".$pic); */ }}
			$_SESSION["Msg"]="<div class='SuccessDiv'>Ваша тема успешно сохранена! <a href='/$node[link]/view/$page/'><u>Перейти к текущей теме</u></a></div>";
	} SD(); }
	/* --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
	$q="SELECT `".$TABLES[2]."`.`id`, `".$TABLES[2]."`.`uid`, `".$TABLES[2]."`.`cid`, `".$TABLES[2]."`.`fid`, `".$TABLES[2]."`.`name`, `".$TABLES[2]."`.`text`, `".$TABLES[2]."`.`stat`, `".$TABLES[2]."`.`comments`,  `".$TABLES[2]."`.`lock`, `".$TABLES[0]."`.`name` as `fname`, `".$TABLES[1]."`.`name` as `cname`
	FROM `".$TABLES[2]."` LEFT JOIN `".$TABLES[1]."` ON `".$TABLES[1]."`.`id`=`".$TABLES[2]."`.`cid` LEFT JOIN `".$TABLES[0]."` ON `".$TABLES[0]."`.`id`=`".$TABLES[2]."`.`fid` WHERE (`".$TABLES[2]."`.`id`='".(int)$page."') GROUP BY 1 LIMIT 1";
	$data=DB($q); @mysql_data_seek($data["result"], 0); $theme=@mysql_fetch_array($data["result"]); $vf=$theme["fid"]."-".$theme["cid"];
	if ($GLOBAL["USER"]["role"]>=1) { $chreg="<select name='newcat' id='newcat'>"; /* смена категории статьи */
		$q="SELECT `".$TABLES[1]."`.`id`, `".$TABLES[1]."`.`fid`, `".$TABLES[1]."`.`name` FROM `".$TABLES[1]."` WHERE (`".$TABLES[1]."`.`stat`=1) GROUP BY 1 ORDER BY `".$TABLES[1]."`.`rate` DESC"; $data=DB($q); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $fid=$ar["fid"]; $items[$fid][]=$ar; endfor;
		$q="SELECT `".$TABLES[0]."`.`id`, `".$TABLES[0]."`.`name` FROM `".$TABLES[0]."` WHERE (`".$TABLES[0]."`.`stat`=1) GROUP BY 1 ORDER BY `".$TABLES[0]."`.`rate` DESC"; $data=DB($q); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $chreg.="<option disabled class='uppertheme'>".$ar["name"]."</option>";
		foreach ($items[$ar["id"]] as $item) { $vfn=$item["fid"]."-".$item["id"]; if ($vfn==$vf) { $chreg.="<option value='".$vfn."' class='undertheme' selected style='color:red;'> •  ".$item["name"]."</option>"; } else { $chreg.="<option value='".$vfn."' class='undertheme'> •  ".$item["name"]."</option>"; }} endfor;	$chreg.="</select>"; 
	}
	$cap="Редактирование темы"; $text="<div class='Crumbs' style='margin-top:-5px;'><a href='/$node[link]'>$node[name]</a> &raquo; $theme[fname] &raquo; <a href='/$node[link]/cat/$theme[cid]'>$theme[cname]</a> &raquo; Редактирование темы</div>";
	$text.=$C10."<div class='ForumLink'><a href='/$node[link]/view/$page'>Вернуться в текущую тему</a></div><div class='ForumLink'><a href='/$node[link]/cat/$theme[cid]'>Вернуться в категорию</a></div>".$C."<div class='WhiteBlock'>".$_SESSION["Msg"];
	/* ВЫВОД ФОРМЫ РЕДАКТИРОВАНИЯ  --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
	$text.='<link media="all" href="/modules/standart/multiupload/client/uploader2.css" type="text/css" rel="stylesheet"><script type="text/javascript" src="/modules/standart/multiupload/client/uploader.js"></script>';
	$text.='<form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post" onsubmit="return JsVerify();"><table>';
	$text.='<tr class="TRLine0"><td class="VarText">Название темы<star>*</star></td><td class="LongInput"><input placeholder="Название темы" maxlength="150" name="newname" type="text" class="JsVerify" style="width:590px;" value=\''.$theme["name"].'\'></td></tr>';
	if ($GLOBAL["USER"]["role"]>=1) { if ($theme["stat"]==1) { $chk1="checked"; } if ($theme["comments"]==1) { $chk2="checked"; } if ($theme["lock"]==1) { $chk3="checked"; } 
		$text.='<tr class="TRLine0"><td class="VarText">Перенос темы</td><td class="LongInput">'.$chreg.'</td></tr><tr class="TRLine0"><td class="VarText" valign="top" style="padding-top:5px;">Настройки</td><td>'.
		$C5.'<input type="text" name="auththeme" value="'.$theme["uid"].'" style="width:50px; padding:4px;" /> Автор темы (ID пользователя, ваш ID='.$GLOBAL["USER"]["id"].')'.$C5.'
		<input type="checkbox" name="stattheme" value="1" '.$chk1.' /> Активная тема (видна пользователям)'.$C5.'<input type="checkbox" name="comtheme" value="1" '.$chk2.' /> Запретить комментарии пользователей к данной теме'.$C5.'<input type="checkbox" name="locktheme" value="1" '.$chk3.' /> Закрепить тему вверху списка тем в категории</td>'; 
	}
	$text.='<script src="/modules/standart/wysiwyg/editor.js"></script><script src="/modules/standart/wysiwyg/ru.js"></script><link rel="stylesheet" href="/modules/standart/wysiwyg/editor.css" />
	<script>$(document).ready(function() { var buttons=["html","|","bold","italic","deleted","|","unorderedlist","orderedlist","outdent","indent","|","image","video","link","|","alignment","|","horizontalrule","table"];
	var tags=["span", "a", "br", "p", "b", "i", "strike", "u", "img", "video", "object", "embed", "param", "blockquote", "ul", "ol", "li", "hr", "strong", "h3","table","td","tr"];
	$("#newtheme").redactor({focus:true, lang:"ru", buttons:buttons, allowedTags:tags, autoresize:false, imageUpload:"/modules/standart/wysiwyg/imageupload.php" }); })</script><tr><td colspan="2"><textarea id="newtheme" name="newtext">'.$theme["text"].'</textarea></td></tr>';
	/* -- прикрепленные фотографии -- */
	$tada=DB("SELECT * FROM `_widget_pics` WHERE (`link`='".$node["link"]."' AND `pid`='".(int)$page."' AND `point`='theme') ORDER BY `data` ASC");
	if ($tada["total"]>0) { for ($i=0; $i<$tada["total"]; $i++): @mysql_data_seek($data["result"], $i); $p=@mysql_fetch_array($data["result"]);
	
	
	
	
	
	endfor; }
	/* -- прикрепленные фотографии -- */
	$text.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Прикрепить фотографии</td><td class="LongInput">'.$C10.'<div id="uploader"></div>'.$C5.'<div class="Info">Вы можете загрузить фотографию в форматах jpg, gif и png</div></td></tr>';
	$text.='</table>'.$C10.$C5.'<div class="CenterText"><input type="submit" name="sendbutton" id="sendbutton" class="SaveButton" value="Сохранить изменения"></div>';
	/* --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
	$text.="</div>"; }}} return(array($text, $cap));
}
###########################################################################################################################################################
function DeleteForumTheme() { 
	global $TABLES, $page, $node, $C10, $C, $C5, $GLOBAL;
	if ($GLOBAL["USER"]["id"]==0 || $GLOBAL["USER"]["role"]<1) { $cap="Доступ закрыт"; $text="Данную тему могут удалять только администраторы"; } else { $cap="Подтвердите удаление темы"; $text=$C10;
		$data=DB("SELECT `name`, `cid` FROM `".$TABLES[2]."` WHERE (`id`='".(int)$page."') LIMIT 1"); @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
		$text.="<h3>Тема: ".$ar["name"].$C10."</h3><b>Вместе с темой так же будут удалены все комментарии и загруженные файлы</b>";
		$text.=$C10."<div class='ForumLink'><a href='/$node[link]/view/$page/'>Вернуться в тему</a></div><div class='ForumDink'><a href='/$node[link]/delthemeconfirm/$page/$ar[cid]'>Удалить тему!</a></div>".$C;
	} return(array($text, $cap));
}
###########################################################################################################################################################
function DeleteForumThemeOK() {
	global $TABLES, $page, $ROOT, $id, $node, $C10, $C, $C5, $GLOBAL;
	if ((int)$GLOBAL["USER"]["id"]==0 || $GLOBAL["USER"]["role"]<1) { $cap="Доступ закрыт"; $text="Данную тему могут удалять только администраторы"; } else { $cap="Тема удалена"; $text=$C10;
	$text.="<div class='ForumLink'><a href='/$node[link]/'>Вернуться к форуму</a></div><div class='ForumLink'><a href='/$node[link]/cat/".(int)$id."'>Вернуться в категорию</a></div>".$C;
	$ids=array(0); $data=DB("SELECT `id` FROM `_comments` WHERE (`link`='".$node["link"]."' && `pid`='".(int)$page."')"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $p=@mysql_fetch_array($data["result"]); $ids[]=$p["id"]; endfor;
	DB("DELETE FROM `_comments` WHERE (`link`='".$node["link"]."' && `pid`='".(int)$page."')"); DB("DELETE FROM `_tracker` WHERE (`link`='".$node["link"]."' && `pid`='".(int)$page."')");
	DB("DELETE FROM `_commentf` WHERE (`pid` IN (".implode(",", $ids)."))"); DB("DELETE FROM `".$TABLES[2]."` WHERE (`id`='".(int)$page."') LIMIT 1");
	ClearCache($node["link"]."-forums.0.0"); ReCountCategory((int)$id); } return(array($text, $cap));
}
###########################################################################################################################################################
function ForumChild($items, $fid) { global $VARS, $link; $text=""; foreach($items as $item) { $fid=$item["fid"]; if ($item["text"]!="") { $ptext=$item["text"]; } else { $ptext=""; }
	$text.="<tr><td class='ForumItem0'><h2><a href='/".$link."/cat/".$item["id"]."'>".trim($item["name"])."</a></h2>".$ptext."</td>";
	$text.="<td class='Digit'><a href='/".$link."/cat/".$item["id"]."'>".(int)$item["tcnt"]."</a></td><td class='Digit'><a href='/".$link."/cat/".$item["id"]."'>".(int)$item["comcount"]."</a></td>";
	$text.="<td class='Update'>".ForumUpdatedBy($item["uid"], $item["nick"], $item["update"])."</td></tr>";
} return $text; }
###########################################################################################################################################################
function ForumUpdatedBy($uid, $nick, $update, $type=0) { $text=""; 
	$p1=ToRusData(time()); $p2=ToRusData(time()-24*60*60); $t=$p1[3]; $y=$p2[3]; $d=ToRusData($update); $data=$d[1]; $data=str_replace($t, "<b>Сегодня</b>", $data); $data=str_replace($y, "<b>Вчера</b>", $data); 
	if ((int)$uid!=0 && $nick!="") { if ($type==0) { $text="<a href='/users/view/".$uid."'>".$nick."</a><br>".$data; } else { $text=$data." / <a href='/users/view/".$uid."'>".$nick."</a>"; } } else { $text=$data; }
return $text; }
###########################################################################################################################################################
function ReCountCategory($cid) {
	global $TABLES, $node; $coms=array(0);
	$q1="SELECT `id` FROM `".$TABLES[2]."` WHERE (`cid`='".(int)$cid."')"; $data=DB($q1); $q1.=" -> ".$data["total"]; $tt=$data["total"]; for ($i=0; $i<$tt; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $coms[]=$ar["id"]; endfor;	
	$q2="SELECT `id` FROM `_comments` WHERE (`link`='".$node["link"]."' AND `pid` IN (".implode(",", $coms)."))"; $data=DB($q2); $tc=$data["total"]; $q2.=" -> ".$data["total"]; 
	if ($tc==0) { $uid=""; $time=time(); } else { $q3="SELECT `uid`, `data` FROM `_comments` WHERE (`link`='".$node["link"]."' AND `pid` IN (".implode(",", $coms).")) ORDER BY `id` DESC LIMIT 1"; $data=DB($q3); $q3.=" -> ".$data["total"]; @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $uid=$ar["uid"]; $time=$ar["data"]; }
	$q4="UPDATE `".$TABLES[1]."` SET `tcnt`='".$tt."', `comcount`='".$tc."', `uid`='".$uid."', `update`='".$time."' WHERE (`id`='".(int)$cid."')"; DB($q4); $q4.=" -> ".$data["result"]; return true;
}
###########################################################################################################################################################
function ReCountAllCategory() {
	global $TABLES, $node;
	$q="UPDATE `".$TABLES[1]."` SET `".$TABLES[1]."`.`comcount`=(SELECT SUM(`".$TABLES[2]."`.`comcount`) FROM `".$TABLES[2]."` WHERE (`".$TABLES[1]."`.`id`=`".$TABLES[2]."`.`cid`) group by `".$TABLES[1]."`.`id`)"; DB($q);
	$q="UPDATE `".$TABLES[1]."` SET `".$TABLES[1]."`.`uid`=(SELECT `_comments`.`uid` FROM `_comments` WHERE (`link`='$node[link]' && `_comments`.`link`='".$node["link"]."' && `_comments`.`pid` IN (SELECT `".$TABLES[2]."`.`id` FROM `".$TABLES[2]."` WHERE `".$TABLES[1]."`.`id`=`".$TABLES[2]."`.`cid` )) ORDER BY `_comments`.`id` DESC LIMIT 1)"; DB($q);
	$q="UPDATE `".$TABLES[1]."` SET `".$TABLES[1]."`.`update`=(SELECT `_comments`.`data` FROM `_comments` WHERE (`link`='$node[link]' && `_comments`.`link`='".$node["link"]."' && `_comments`.`pid` IN (SELECT `".$TABLES[2]."`.`id` FROM `".$TABLES[2]."` WHERE `".$TABLES[1]."`.`id`=`".$TABLES[2]."`.`cid` )) ORDER BY `_comments`.`id` DESC LIMIT 1)"; DB($q);
}
###########################################################################################################################################################
function YouTubeCreate($text){
	$youtubePattern = '/(http:\/\/)?((www.youtube.com)|(youtu.be))\/(\S)+/i';
	if(preg_match_all($youtubePattern, strip_tags($text), $output)) { 
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
			$text = str_replace($url, $embed, $text); 
		}
	}
	return $text;
}
###########################################################################################################################################################
$_SESSION["Msg"]="";
?>