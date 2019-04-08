<?

$table=$link."_lenta";
$table2="_widget_pics";
$table3="_widget_votes";
$table4="_widget_video";
$table5="_widget_voting";
$table6="_widget_contacts";
$table7="_widget_eventmap";

if ($start=="") { $start="list"; $dir[1]="list"; }
$file=$table."-mobile".$start.".".$page.".".$id;


#############################################################################################################################################
### Вывод списка новостей общий
if ($start=="list") {
	 @header("location: /"); exit();	
	 #if (RetCache($file)=="true") { list($text, $cap)=GetCache($file, 0); } else { list($text, $cap)=GetLentaList(); SetCache($file, $text, ""); } $Page["Content"]=$text; $Page["Caption"]=$node["name"];
}

#############################################################################################################################################
### Вывод списка новостей в категории
if ($start=="cat") {
	if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); } else { list($text, $cap)=GetLentaCat(); SetCache($file, $text, $cap); }
	$Page["Content"]=$text; $Page["Caption"]=$cap; $GLOBAL["design"]="mainpage";
}

#############################################################################################################################################
### Вывод новости

if ($start=="view") {
	$where=$GLOBAL["USER"]["role"]==0?"&& `stat`=1":"";
	$data=DB("SELECT `comments`, `stat` FROM `".$table."` WHERE (`id`='".(int)$dir[2]."' ".$where.") LIMIT 1");
	if ($data["total"]==1) {
		/*  --- УНИКАЛЬНЫЕ ПРОСМОТРЫ --- */ UniqueSeens((int)$dir[2]); /*  --- УНИКАЛЬНЫЕ ПРОСМОТРЫ --- */ 
		@mysql_data_seek($data["result"], 0); $new=@mysql_fetch_array($data["result"]); 
		if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); $cachestat="Взято из кэша"; } else { list($text, $cap)=GetLentaId(); SetCache($file, $text, $cap); $cachestat="Прямой вывод"; }
		/* UserTracker($link, $page); */ 
		
		$text.=$C15.UsersComments($link, $page, $new["comments"])."<!-- COMS -->";
		
		if ($GLOBAL["USER"]["role"]>1) {
			if ($new["stat"]==1) { $statbar="Опубликовано для всех"; } else { $statbar="Не опубликовано, видно только редакторам"; }
			$text=$C10."<div id='AdminEditItem'><a href='".$GLOBAL["mdomain"]."/admin/?cat=".$link."_edit&id=".(int)$dir[2]."'>Редактировать</a>  <span>".$statbar." </span>  <span>".$cachestat." </span></div>".$C15.$C15.$text;
		}
		$Page["Content"]=$text; $Page["Title"]=$cap; $Page["Caption"]=$cap;
	} else { $cap="Материал не найден"; $text=@file_get_contents($ROOT."/template/404.html"); $Page["Content"]=$text; $Page["Caption"]=$cap; }
}
###########################################################################################################################################


### ЛЕНТА НОВОСТЕЙ ########################################################################################################################

function GetLentaList() {
	global $VARS, $GLOBAL, $dir, $ORDERS, $RealHost, $Page, $node, $UserSetsSite, $table, $table2, $table3, $table4, $table5, $C, $C20, $C10, $C25;
	$onpage=$node["onpage"]; $pg = $dir[2] ? $dir[2] : 1; $orderby=$ORDERS[$node["orderby"]]; $from=($pg - 1)*$onpage; $onblock=4; /* Новостей в каждом блоке */
	$data=DB("SELECT `".$table."`.id, `".$table."`.cat, `".$table."`.name, `".$table."`.uid, `".$table."`.pic, `".$table."`.data,`".$table."`.comcount, `".$table."`.comments, `".$dir[0]."_cats`.`name` as `ncat`, `_users`.`nick`
	FROM `".$table."` LEFT JOIN `_users` ON `".$table."`.`uid`=`_users`.`id` LEFT JOIN `".$dir[0]."_cats` ON `".$dir[0]."_cats`.`id`=`".$table."`.`cat` WHERE (`".$table."`.`stat`=1)  GROUP BY 1 ".$orderby." LIMIT $from, $onpage");
	for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]); $pic="";
		if ($ar["pic"]!="") { if (strpos($ar["pic"], "old")!=0) { /*Старый вид картинок*/ $pic="<img src='".$ar["pic"]."' title='".$ar["name"]."' />"; } else { /*Новый вид картинок*/ $pic="<img src='/userfiles/pictavto/".$ar["pic"]."' title='".$ar["name"]."' />"; }}
		if ($ar["uid"]!=0 && $ar["nick"]!="") { $auth="<a href='http://".$VARS["mdomain"]."/users/view/".$ar["uid"]."/'>".$ar["nick"]."</a>"; } else { $auth="<a href='http://".$VARS["mdomain"]."/add/2/'>Народный корреспондент</a>"; }
		if ($UserSetsSite[3]==1 && $ar["comments"]!=2) { $coms="<div class='CommentBox'><a href='/".$dir[0]."/view/".$ar["id"]."#comments'>".$ar["comcount"]."</a></div>"; } else { $coms=""; }
		$text.="<div class='NewsLentaList' id='NewsLentaList-".$ar["id"]."'><a href='/".$dir[0]."/view/".$ar["id"]."'>".$pic."</a><h2><a href='/".$dir[0]."/view/".$ar["id"]."'>".$ar["name"]."</a></h2>".$C."
		<div class='Info'><div class='Other'>".Replace_Data_Days($d[4]).",  <a href='/".$dir[0]."/cat/".$ar["cat"]."'>".$ar["ncat"]."</a>,  Автор: ".$auth."</div>".$coms."</div></div>";
		if($data["total"]>($i+1)){ if (($i+1)%$onblock==0) { $text.=$C25."<div class='banner2' style='margin-left:10px;' id='Banner-6-".(floor($i/$onblock)+1)."'></div>".$C; } else { $text.=$C25; }}
	}
	$data=DB("SELECT count(id) as `cnt` FROM `".$table."`"); @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $text.=Pager2($pg, $onpage, ceil($ar["cnt"]/$onpage), $dir[0]."/".$dir[1]."/[page]");
	return(array($text, ""));
}
##### КАТЕГОРИЯ НОВОСТЕЙ ########################################################################################################################################

function GetLentaCat() {
	global $VARS, $GLOBAL, $dir, $ORDERS, $RealHost, $Page, $node, $UserSetsSite, $table, $table2, $table3, $table4, $table5, $C, $C20, $C10, $C25;
	$onpage=$node["onpage"]; $pg = $dir[3] ? $dir[3] : 1; $orderby=$ORDERS[$node["orderby"]]; $from=($pg - 1)*$onpage; $onblock=4; /* Новостей в каждом блоке */
	#$data=DB("SELECT `".$table."`.name, `".$table."`.uid, `".$table."`.cat, `".$table."`.pic, `".$table."`.data, `".$table."`.id, `".$table."`.comcount, `".$table."`.comments, `".$dir[0]."_cats`.`name` as `ncat`, `_users`.`nick`
	#FROM `".$table."`	LEFT JOIN `_users` ON `".$table."`.`uid`=`_users`.`id` LEFT JOIN `".$dir[0]."_cats` ON `".$dir[0]."_cats`.`id`=`".$table."`.`cat` WHERE (`".$table."`.`cat`='".(int)$dir[2]."' && `".$table."`.`stat`=1) GROUP BY 1 ".$orderby." LIMIT $from, $onpage");
	#if ($ar["uid"]!=0 && $ar["nick"]!="") { $auth="<a href='http://".$VARS["mdomain"]."/users/view/".$ar["uid"]."/'>".$ar["nick"]."</a>"; } else { $auth="<a href='http://".$VARS["mdomain"]."/add/2/'>Народный корреспондент</a>"; }
	#if ($UserSetsSite[3]==1 && $ar["comments"]!=2) { $coms="<div class='CommentBox'><a href='/".$dir[0]."/view/".$ar["id"]."#comments'>".$ar["comcount"]."</a></div>"; } else { $coms=""; }	
	$data=DB("SELECT `".$table."`.name,`".$table."`.lname, `".$table."`.uid, `".$table."`.cat, `".$table."`.pic, `".$table."`.data, `".$table."`.id, `".$dir[0]."_cats`.`name` as `ncat` FROM `".$table."` LEFT JOIN `".$dir[0]."_cats` ON `".$dir[0]."_cats`.`id`=`".$table."`.`cat` WHERE (`".$table."`.`cat`='".(int)$dir[2]."' && `".$table."`.`stat`=1) GROUP BY 1 ".$orderby." LIMIT $from, $onpage");
	$text.="<div class='NewsLentaLists'>"; for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]); $pic="";
		if ($ar["pic"]!="") { $pic="<img src='/userfiles/picmiddle/".$ar["pic"]."' title='".$ar["name"]."' />"; }
		$text.="<a href='/".$dir[0]."/view/".$ar["id"]."' class='NewsLentaList' id='NewsLentaList-".$ar["id"]."'>".$pic."<span>".nl2br($ar["name"]."<br>".$ar["lname"])."</span></a>";
		if (($i+1)%5==0) { $text.=$C; }
	} $text.="</div>".$C;
	$ncat=$ar["ncat"]; $data=DB("SELECT count(id) as `cnt` FROM `".$table."` WHERE (`cat`='".(int)$dir[2]."')"); @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
	$text.=Pager2($pg, $onpage, ceil($ar["cnt"]/$onpage), $dir[0]."/".$dir[1]."/".$dir[2]."/[page]"); return(array($text, $ncat));
}



####### ВЫВОД СОДЕРЖАНИЯ НОВОСТИ ######################################################################################################################################
function GetLentaId() {
	global $VARS, $GLOBAL, $dir, $RealHost, $Page, $node, $table, $table2, $table3, $table4, $table5, $table6, $table7, $link, $C, $C5, $C10, $C15, $C20, $ROOT, $forums; 
	
	### Основной запрос
	$data=DB("SELECT `".$table."`.*, `".$dir[0]."_cats`.`name` as `ncat`, `_users`.`nick`, `_users`.`avatar`, `$table5`.`id` as `vvid` FROM `".$table."`
	LEFT JOIN `_users` ON `".$table."`.`uid`=`_users`.`id` LEFT JOIN `$table5` ON `$table5`.`pid`=`$table`.`id` AND `$table5`.`link`='".$dir[0]."' AND `$table5`.`vid`='0' AND `$table5`.`stat`=1	
	LEFT JOIN `".$dir[0]."_cats` ON `".$dir[0]."_cats`.`id`=`".$table."`.`cat` WHERE (`".$table."`.`id`='".(int)$dir[2]."') GROUP BY 1 LIMIT 1"); @mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]);
	
	$Page["Description"]=$item["ds"]; $Page["KeyWords"]=$item["kw"]; $cap=$item["name"]."<span>".str_replace(array("\r","\n"),"", $item["lname"])."</span>";
	
	### Фотография
	if ($item["type"]==0) {
		if ($item["pic"]!="") { $pic="<div class='PicItem' title='$cap'>";  $path='/userfiles/picmiddle/'.$item["pic"]; $pic.="<img src='".$path."' title='$cap' alt='$cap' />"; $pic.="</div>".$C20; }
	} else {
		$p=DB("SELECT * FROM `".$table4."` WHERE (`pid`='".(int)$dir[2]."' && `link`='".$dir[0]."') LIMIT 1"); if ($p["total"]>0) { $pic="";
		for ($i=0; $i<$p["total"]; $i++): mysql_data_seek($p["result"],$i); $ar=@mysql_fetch_array($p["result"]); if ($ar["text"]!="") { if ($ar["name"]!="") { $video.="<h2>".$ar["name"]."</h2>"; } 
		$vid=GetMobileVideo($ar["text"]); $pic.=$C15.$vid.$C15; } endfor; }
	}
	
	### Претекст текст
	if ($item["lid"]!="") { $lid="<div class='ItemLid'><div class='Br'></div>".$C10.$item["lid"].$C10."<div class='Br'></div></div>".$C15; }
	
$banyandex='<div id="yandex_rtb_R-A-334678-4"></div>
<script type="text/javascript">
    (function(w, d, n, s, t) {
        w[n] = w[n] || [];
        w[n].push(function() {
            Ya.Context.AdvManager.render({
                blockId: "R-A-334678-4",
                renderTo: "yandex_rtb_R-A-334678-4",
                async: true
            });
        });
        t = d.getElementsByTagName("script")[0];
        s = d.createElement("script");
        s.type = "text/javascript";
        s.src = "//an.yandex.ru/system/context.js";
        s.async = true;
        t.parentNode.insertBefore(s, t);
    })(this, this.document, "yandexContextAsyncCallbacks");
</script></div>';


$banyandex2='<div id="yandex_rtb_R-A-334678-5"></div>
<script type="text/javascript">
    (function(w, d, n, s, t) {
        w[n] = w[n] || [];
        w[n].push(function() {
            Ya.Context.AdvManager.render({
                blockId: "R-A-334678-5",
                renderTo: "yandex_rtb_R-A-334678-5",
                async: true
            });
        });
        t = d.getElementsByTagName("script")[0];
        s = d.createElement("script");
        s.type = "text/javascript";
        s.src = "//an.yandex.ru/system/context.js";
        s.async = true;
        t.parentNode.insertBefore(s, t);
    })(this, this.document, "yandexContextAsyncCallbacks");
</script></div>';
	
	### Основной текст
	$maintext=CutEmptyTags($item["text"]).$C15;
	 
	### Фото-отчет
	$p=DB("SELECT * FROM `".$table2."` WHERE (`pid`='".(int)$dir[2]."' && `link`='".$dir[0]."' && `point`='report' && `stat`=1) order by `rate` ASC"); $report='';
	if ($p["total"]>0) { for ($i=0; $i<$p["total"]; $i++): mysql_data_seek($p["result"],$i); $ar=@mysql_fetch_array($p["result"]); 
		$report.="<div><h2>".$ar["name"]."</h2><img src='/userfiles/picpreview/".$ar["pic"]."' title='".$ar["name"]."' alt='".$ar["name"]."' class='ReportPicBig'>"; $report.=$ar["text"]."</div>".$C15; 
	 endfor; $report.=$C15; }
	
	### Маркер-отчет
	$p=DB("SELECT * FROM `".$table2."` WHERE (`pid`='".(int)$dir[2]."' && `link`='".$dir[0]."' && `point`='marker' && `stat`=1) order by `rate` ASC"); $marker='';
	if ($p["total"]>0) { for ($i=0; $i<$p["total"]; $i++): mysql_data_seek($p["result"],$i); $ar=@mysql_fetch_array($p["result"]); 
			$marker.="<div class='Pointer'>".($i+1)."</div><div class='PointerText'>";
				if ($ar["name"]) { $marker.="<h2>".$ar["name"]."</h2>".$C5; }
				if ($ar["text"]) { $marker.=$ar["text"]; } 
			$marker.="</div>".$C;
			$marker.="<img src='/userfiles/picoriginal/".$ar["pic"]."' title='".$ar["name"]."' alt='".$ar["name"]."' class='markerPicBig'>";
	$marker.=$C15; endfor; $marker.=$C15; }
	
	### Фото-альбом
	#$p=DB("SELECT * FROM `".$table2."` WHERE (`pid`='".(int)$dir[2]."' && `link`='".$dir[0]."' && `point`='album' && `stat`=1) order by `rate` ASC");
	#if ($p["total"]>0) { $album="<h2>Фотоальбом:</h2>$C10<div class='ItemAlbum'>"; for ($i=0; $i<$p["total"]; $i++): mysql_data_seek($p["result"],$i); $ar=@mysql_fetch_array($p["result"]); $album.="<a href='/userfiles/picoriginal/".$ar["pic"]."' title='".$ar["name"]."' rel='prettyPhoto[gallery]'><img src='/userfiles/picsquare/".$ar["pic"]."' title='".$ar["name"]."' alt='".$ar["name"]."'></a>"; endfor; $album.="</div>".$C; }
	
	### Голосование
	#if ((int)$item["vvid"]!=0) { $voting=$C5."<div id='ItemVotingDiv'></div><script>GetItemVoting(".(int)$item["vvid"].");</script>".$C5; }
	
	### Видео
	if ($item["type"]==0) { $p=DB("SELECT * FROM `".$table4."` WHERE (`pid`='".(int)$dir[2]."' && `link`='".$dir[0]."') LIMIT 1"); if ($p["total"]>0) { $video="";
	for ($i=0; $i<$p["total"]; $i++): mysql_data_seek($p["result"],$i); $ar=@mysql_fetch_array($p["result"]); if ($ar["text"]!="") { if ($ar["name"]!="") { $video.="<h2>".$ar["name"]."</h2>"; } 
	$vid=GetMobileVideo($ar["text"]); $video.=$C15.$vid.$C15; } endfor; }}
	
	### Тэги
	$t=trim($item["tags"], ","); $tags=""; if ($t!="") { $ta=DB("SELECT * FROM `_tags` WHERE (`id` IN (".$t.")) LIMIT 3"); for ($i=0; $i<$ta["total"]; $i++): @mysql_data_seek($ta["result"],$i); $ar=@mysql_fetch_array($ta["result"]);
	$tags.="<a href='/tags/$ar[id]'>$ar[name]</a>, "; endfor; $tags2=trim($tags, ", "); $tags="Тэги: ".trim($tags, ", "); } $mixblock.="<div class='ItemTags'>".$tags."</div>".$C10;
	
	### Аватар автора, Автор и дата
	###if ($item["avatar"]=="" || !is_file($ROOT."/".$item["avatar"]) || filesize($ROOT."/".$item["avatar"])<100) { $avatar="<img src='/userfiles/avatar/no_photo.jpg'>"; } else { $avatar="<img src='/".$item["avatar"]."'>"; }
	$d=ToRusData($item["data"]); if ($item["uid"]!=0 && $item["nick"]!="") { $auth="<a href='http://".$VARS["mdomain"]."/users/view/".$item["uid"]."/'>".$item["nick"]."</a>, ".$d[1]; } else { $auth="Народный корреспондент, ".$d[1]; }
	$mixblock.="<div class='ItemAuth'>".$auth."<!--<br />Если Вы нашли ошибку, <u>выделите фразу с ошибкой</u> и нажмите Ctrl+Enter--></div>";

	### Лайки 
	$likes=$C10."<div class='Likes' style='text-align:center;'>".Likes(Hsc($cap), "", "http://".$RealHost.$path, Hsc(strip_tags($lid))).$C."</div>".$C10;
	
	//if ($item["pay"]!="") { $mixblock.=$C20."<div class='PayBlock'>".$item["pay"]."</div>".$C; }
	### Платные ссылки
	//if ($item["adv"]!="") { $mixblock.=$C10."<div class='CBG'></div>".$C5."<div class='AdvBlock'>".$item["adv"]."</div>".$C; }
	### Новости по таким же тэгам
	//$dtags=''; $art=explode(",", $item["tags"]); if (count($art)>2 && mb_strlen(strip_tags($maintext))>1000 && $item["promo"]!="1") {	$limit=(round(mb_strlen(strip_tags($maintext))/900)); $dtags=GetRelevantNews($art, $limit, $tags2); }
	### Заключительный текст
	if ($item["endtext"]!="") { $endtext=$C20."<div class='EndText'>".$item["endtext"]."</div>".$C10; }
	
	$text=$pic.$ban."<div class='ArticleContent'>".$lid.$banyandex.$dtags.$maintext.$marker.$report.$video.$endtext.$banyandex2.$voting.$album.$event.$contacts.$frm.$likes.$mixblock."</div>";
	return(array($text, $cap));
}


###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ######
###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### 
###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ######   

function UniqueSeens($pid) {
	$data=strtotime(date("Y-m-d")); DB("INSERT INTO `post_seen` (`pid`,`data`,`ip`) VALUES ('".(int)$pid."','".$data."','".$_SERVER["REMOTE_ADDR"]."') ON DUPLICATE KEY UPDATE `seen`=`seen`+1");
	$data=DB("SELECT `pid` FROM `post_seen` WHERE (`pid`='".$pid."')"); DB("UPDATE `post_lenta` SET `seen`='".$data["total"]."' WHERE (`id`='".$pid."')"); return($data["total"]);
}

?>