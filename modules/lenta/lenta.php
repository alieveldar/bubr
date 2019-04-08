<?

$table=$link."_lenta";
$table2="_widget_pics";
$table3="_widget_votes";
$table4="_widget_video";
$table5="_widget_voting";
$table6="_widget_contacts";
$table7="_widget_eventmap";

if ($start=="") { $start="list"; $dir[1]="list"; }
$file=$table."-".$start.".".$page.".".$id;


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
		@mysql_data_seek($data["result"], 0); $new=@mysql_fetch_array($data["result"]);
		/*  --- УНИКАЛЬНЫЕ ПРОСМОТРЫ --- */ UniqueSeens((int)$dir[2]); /*  --- УНИКАЛЬНЫЕ ПРОСМОТРЫ --- */ 
		if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); $cachestat="Взято из кэша"; } else { list($text, $cap)=GetLentaId(); SetCache($file, $text, $cap); $cachestat="Прямой вывод"; }
		/* UserTracker($link, $page); */ $text=str_replace("<!--C-->", UsersComments($link, $page, $new["comments"]).$C15, $text);
		if ($GLOBAL["USER"]["role"]>1) {
			if ($new["stat"]==1) { $statbar="Опубликовано для всех"; } else { $statbar="Не опубликовано, видно только редакторам"; }
			$text=$C10."<div id='AdminEditItem'><a href='".$GLOBAL["mdomain"]."/admin/?cat=".$link."_edit&id=".(int)$dir[2]."'>Редактировать</a>  <span>".$statbar." </span>  <span>".$cachestat." </span></div>".$C15.$C15.$text;
		}
		$Page["Content"]=$text; $Page["Title"]=$cap; $Page["Caption"]=$cap;
	} else { $cap="Материал не найден"; $text=@file_get_contents($ROOT."/template/404.html"); $Page["Content"]=$text; $Page["Caption"]=$cap; }
}
###########################################################################################################################################
### Вывод превью фоток
if ($start=="pre") { list($text, $cap)=GetLentaPreView(); $Page["Content"]=$text; $Page["Caption"]=$cap; $GLOBAL["design"]="mainpage"; }

### ЛЕНТА НОВОСТЕЙ ########################################################################################################################

function GetLentaList() {
	global $VARS, $GLOBAL, $dir, $ORDERS, $RealHost, $Page, $node, $UserSetsSite, $table, $table2, $table3, $table4, $table5, $C, $C20, $C10, $C25;
	$onpage=$node["onpage"]; $pg = $dir[2] ? $dir[2] : 1; $orderby=$ORDERS[$node["orderby"]]; $from=($pg - 1)*$onpage; $onblock=4; /* Новостей в каждом блоке */
	$data=DB("SELECT `".$table."`.id, `".$table."`.cat, `".$table."`.name, `".$table."`.lname, `".$table."`.sname, `".$table."`.uid, `".$table."`.pic, `".$table."`.data,`".$table."`.comcount, `".$table."`.comments, `".$dir[0]."_cats`.`name` as `ncat`, `_users`.`nick`
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
	$data=DB("SELECT `".$table."`.name, `".$table."`.lname, `".$table."`.sname, `".$table."`.uid, `".$table."`.cat, `".$table."`.pic, `".$table."`.data, `".$table."`.id, `".$dir[0]."_cats`.`name` as `ncat` FROM `".$table."` LEFT JOIN `".$dir[0]."_cats` ON `".$dir[0]."_cats`.`id`=`".$table."`.`cat` WHERE (`".$table."`.`cat`='".(int)$dir[2]."' && `".$table."`.`stat`=1) GROUP BY 1 ".$orderby." LIMIT $from, $onpage");
	$text.="<div class='NewsLentaLists'>"; for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]); $pic="";
		
		if ($ar["pic"]!="") {
			if (in_array(($i+1)%5, array(1,2))) { $pic="<img src='/userfiles/picmiddle/".$ar["pic"]."' title='".$ar["name"]."' />"; }
			if (in_array(($i+1)%5, array(0,4,3))) { $pic="<img src='/userfiles/picsquare/".$ar["pic"]."' title='".$ar["name"]."' />"; }
		} else { $pic=""; }	
		
		if ($ar["sname"]!="") {$kryshka="<b>".$ar["sname"]."</b>"; } else { $kryshka="";}
		
		$text.="<a href='/".$dir[0]."/view/".$ar["id"]."' class='NewsLentaList' id='NewsLentaList-".$ar["id"]."'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"])."</a>";
		if (($i+1)%5==0) { $text.=$C; }
	} $text.="</div>".$C;
	$ncat=$ar["ncat"]; $data=DB("SELECT count(id) as `cnt` FROM `".$table."` WHERE (`cat`='".(int)$dir[2]."')"); @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
	$text.=Pager2($pg, $onpage, ceil($ar["cnt"]/$onpage), $dir[0]."/".$dir[1]."/".$dir[2]."/[page]"); return(array($text, $ncat));
}


#############################################################################################################################################
function GetRelevantNews($art, $limit, $tags2) {
	global $dir, $table, $C15, $C, $C20; $tab=$table; $r=rand(0, 4); $tables=array("post_lenta");
	/* foreach($tables as $table) { $tmp=explode("_", $table); $link=$tmp[0]; $q1.="(SELECT `$table`.`id`, `$table`.`name`, `$table`.`data`, `$table`.`pic`, `_pages`.`domain`, `_pages`.`link` FROM `$table` WHERE (`$table`.`stat`='1') GROUP BY 1) UNION "; }
	$q=trim($q1, "UNION ")." ORDER BY `data` DESC LIMIT 6"; $data=DB($q); var_dump($q); for($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $tv[]=$ar; } $new=$tv[$r];
	if ($new["name"]!="" && $new["name"]) { $d=ToRusData($new["data"]); $dtags.="<a href='/$new[link]/view/$new[id]/' title='".$new["name"]."'><img src='/userfiles/picintv/".$new["pic"]."' style='width:200px; height:110px; border:none; border-radius:5px; margin-bottom:7px;' title='".$new["name"]."' alt='".$new["name"]."' /></a>";
	$dtags.="<a href='/$new[link]/view/$new[id]/' title='".$new["name"]."'>".$new["name"]."</a><br><b>".$d[4]."</b><div class='C'></div><div class='CB'></div>"; } */
	/* новости по тэгам */ $q=""; foreach ($art as $k=>$v) { if ($v!='') { $q.="`tags` LIKE '%,".$v.",%' OR "; }} $ggl=0; 
	$qr="SELECT `pic`,`name`,`id` FROM `".$tab."` WHERE ((".trim($q, "OR ").") AND (`id`!='".(int)$dir[2]."') AND (`stat`='1')) ORDER BY RAND() LIMIT ".$limit; $data2=DB($qr);
	if ($data2["total"]>0) { $dtags=$C15.$C15.'<h1>Вам понравится:</h1><div class="Dtags">'; for ($i=0; $i<$data2["total"]; $i++): @mysql_data_seek($data2["result"],$i); $ar=@mysql_fetch_array($data2["result"]);
	$d=ToRusData($ar["data"]); $dtags.="<div class='TagsRelevant'><a href='/$dir[0]/view/$ar[id]/' title='".$ar["name"]."'><img src='/userfiles/picsquare/$ar[pic]'>".$ar["name"]."</a></div>";
	if (($i+1)%3==0) {
		$dtags.=$C; $ggl++;
		if ($ggl==1) { $dtags.='<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script><!-- Bubr.ru - 468x60 --><ins class="adsbygoogle" style="width:468px;height:60px;margin:0 auto 27px auto; display:block;" data-ad-client="ca-pub-2073806235209608" data-ad-slot="4885994214"></ins><script>(adsbygoogle = window.adsbygoogle || []).push({});</script>'; }
	}
	endfor; $dtags.='</div>'.$C; } return $dtags;
}


####### ВЫВОД СОДЕРЖАНИЯ НОВОСТИ ######################################################################################################################################
function GetLentaId() {
	global $VARS, $GLOBAL, $dir, $RealHost, $Page, $node, $table, $table2, $table3, $table4, $table5, $table6, $table7, $link, $C, $C5, $C10, $C15, $C20, $ROOT, $forums; 
	
	### Основной запрос
	$data=DB("SELECT `".$table."`.*, `".$dir[0]."_cats`.`name` as `ncat`, `_users`.`nick`, `_users`.`role`, `_users`.`avatar`, `$table5`.`id` as `vvid`
	FROM `".$table."` LEFT JOIN `_users` ON `".$table."`.`uid`=`_users`.`id`
	LEFT JOIN `$table5` ON `$table5`.`pid`=`$table`.`id` AND `$table5`.`link`='".$dir[0]."' AND `$table5`.`vid`='0' AND `$table5`.`stat`=1	
	LEFT JOIN `".$dir[0]."_cats` ON `".$dir[0]."_cats`.`id`=`".$table."`.`cat` WHERE (`".$table."`.`id`='".(int)$dir[2]."') GROUP BY 1 LIMIT 1");
	@mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]); $Page["Description"]=$item["ds"]; $Page["KeyWords"]=$item["kw"];
	$cap=$item["name"]."<span>".str_replace(array("\r","\n")," ", $item["lname"])."</span>"; $normalcap=trim($item["name"]).". ".str_replace(array("\r","\n")," ", $item["lname"]);
	
	
		
	####################################################################################
	//LEFT JOIN `_userspays` ON `".$table."`.`id`=`_userspays`.`pid`
	//, SUM(`_userspays`.`money`) as `money`
	####################################################################################
	
	### Фотография или видео - зависит от типа
	if ($item["type"]==0) {
		if ($item["pic"]!="") {
			$ar["author"]=""; $ar["authorlink"]=$item["picauth"];
			if ($ar["author"]!="" || $ar["authorlink"]!="") {
				$ar["author"]=str_replace("http://", '', trim($ar["author"], "./")); $ar["authorlink"]=str_replace("http://", '', trim($ar["authorlink"], "./")); if ($ar["author"]=="") { $tmp1=explode("/", $ar["authorlink"]); $tmp2=explode("?", $tmp1[0]); $ar["author"]=$tmp2[0]; }
				$imgauth=$ar["author"]; if ($ar["authorlink"]!="" && strpos($item["picauth"], "http:")!==false) { $imgauth="<a href='http://".$ar["authorlink"]."' target='_blank' rel='nofollow'>".$imgauth."</a>"; } $imgauth="<auth><span>Фото: ".$imgauth."</span></auth>";
			}
			$pic="<div class='PicItem' title='$cap'>";  $path='/userfiles/picoriginal/'.$item["pic"]; $pic.="<img src='".$path."' title='$cap' alt='$cap' />";
			if ($item["cens"]!="") { $pic.="<div class='Cens'>".$item["cens"]."</div>"; } if ($item["picauth"]!="") { $pic.="<div class='PicAuth'>".$imgauth."</div>"; } $pic.="</div>".$C20;
		}
		### Претекст текст
		if ($item["lid"]!="") { $lid="<div class='ItemLid'><div class='Br'></div>".$C10.$item["lid"].$C10."<div class='Br'></div></div>".$C15; }
	} else {
		$path='/userfiles/picoriginal/'.$item["pic"];
		### Претекст текст
		$lid=''; if ($item["lid"]!="") { $pic="<div class='ItemLid'><div class='Br'></div>".$C10.$item["lid"].$C10."<div class='Br'></div></div>".$C15; }	
		$p=DB("SELECT * FROM `".$table4."` WHERE (`pid`='".(int)$dir[2]."' && `link`='".$dir[0]."') LIMIT 1"); if ($p["total"]>0) { for ($i=0; $i<$p["total"]; $i++): mysql_data_seek($p["result"],$i); $ar=@mysql_fetch_array($p["result"]); if ($ar["text"]!="") { if ($ar["name"]!="") { $video.="<h2>".$ar["name"]."</h2>"; } $vid=GetNormalVideo($ar["text"]); $pic.=$C15.$vid.$C15; } endfor; }
	} 	

$banyandex='<div id="yandex_rtb_R-A-334678-2"></div>
<script type="text/javascript">
    (function(w, d, n, s, t) {
        w[n] = w[n] || [];
        w[n].push(function() {
            Ya.Context.AdvManager.render({
                blockId: "R-A-334678-2",
                renderTo: "yandex_rtb_R-A-334678-2",
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
</script> </div>';
	
	$ban3='<div id="Banner-3-1"></div>';	
	
	### Основной текст
	$maintext=CutEmptyTags($item["text"]).$C15;
	 
	### Фото-отчет
	$p=DB("SELECT * FROM `".$table2."` WHERE (`pid`='".(int)$dir[2]."' && `link`='".$dir[0]."' && `point`='report' && `stat`=1) order by `rate` ASC"); $report=''; $marker=1; 
	if ($p["total"]>0) { for ($i=0; $i<$p["total"]; $i++): mysql_data_seek($p["result"],$i); $ar=@mysql_fetch_array($p["result"]);
	$alttitle=$item["name"]; $imgauth=""; if ($ar["name"]!="") { $alttitle=$ar["name"]; } 
		
		if ($ar["author"]!="" || $ar["authorlink"]!="") {
			$ar["author"]=str_replace("http://", '', trim($ar["author"], "./")); $ar["authorlink"]=str_replace("http://", '', trim($ar["authorlink"], "./")); if ($ar["author"]=="") { $tmp1=explode("/", $ar["authorlink"]); $tmp2=explode("?", $tmp1[0]); $ar["author"]=$tmp2[0]; }
			$imgauth=$ar["author"]; if ($ar["authorlink"]!="") { $imgauth="<a href='http://".$ar["authorlink"]."' target='_blank' rel='nofollow'>".$imgauth."</a>"; } $imgauth="<auth><span>Фото: ".$imgauth."</span></auth>";
		} 
		
		if ($ar["sets"]==0) { $img="<div class='ReportPicSmall'><a href='/userfiles/picoriginal/".$ar["pic"]."' title='".$alttitle."' rel='prettyPhoto[gallery]'><img src='/userfiles/picpreview/".$ar["pic"]."' title='".$alttitle."' alt='".$alttitle."'></a>".$imgauth."</div>";
		} else { $img="<div class='ReportPicBig'><a href='/userfiles/picoriginal/".$ar["pic"]."' title='".$alttitle."' rel='prettyPhoto[gallery]'><img src='/userfiles/picoriginal/".$ar["pic"]."' title='".$alttitle."' alt='".$alttitle."'></a>".$imgauth."</div>"; }
		
	 	if ($ar["mark"]==1) { # МАРКЕР ФОТО	
			$report.="<div class='Pointer'>".($marker)."</div><div class='PointerText'>"; if ($ar["name"]!="" && $ar["showname"]!=0) { $report.="<h2>".$ar["name"]."</h2>"; }
			if ($ar["sets"]==0) { $report.=$img; } if ($ar["text"]) { $report.=$ar["text"]; } $report.="</div>".$C;	if ($ar["sets"]==1) { $report.=$img; } $marker++;
	 	} else { # ФОТО ОТЧЕТ
	 	 	if ($ar["name"]!="" && $ar["showname"]!=0) { $report.="<h2>".$ar["name"]."</h2>"; } $report.=$img; if ($ar["text"]!="") { $report.=$ar["text"]; } 
		}
		$report.=$C15;
	endfor; }

	### Фото-альбом
	$p=DB("SELECT * FROM `".$table2."` WHERE (`pid`='".(int)$dir[2]."' && `link`='".$dir[0]."' && `point`='album' && `stat`=1) order by `rate` ASC");
	if ($p["total"]>0) { $album="<h2>Фотоальбом:</h2>$C10<div class='ItemAlbum'>"; for ($i=0; $i<$p["total"]; $i++): mysql_data_seek($p["result"],$i); $ar=@mysql_fetch_array($p["result"]); $album.="<a href='/userfiles/picoriginal/".$ar["pic"]."' title='".$ar["name"]."' rel='prettyPhoto[gallery]'><img src='/userfiles/picsquare/".$ar["pic"]."' title='".$ar["name"]."' alt='".$ar["name"]."'></a>"; endfor; $album.="</div>".$C; }
	
	### Голосование
	if ((int)$item["vvid"]!=0) { $voting=$C5."<div id='ItemVotingDiv'></div><script>GetItemVoting(".(int)$item["vvid"].");</script>".$C5; }
	
	### Видео
	if ($item["type"]==0) { $p=DB("SELECT * FROM `".$table4."` WHERE (`pid`='".(int)$dir[2]."' && `link`='".$dir[0]."') LIMIT 1"); if ($p["total"]>0) { $video=""; for ($i=0; $i<$p["total"]; $i++): mysql_data_seek($p["result"],$i); $ar=@mysql_fetch_array($p["result"]); if ($ar["text"]!="") { if ($ar["name"]!="") { $video.="<h2>".$ar["name"]."</h2>"; } $vid=GetNormalVideo($ar["text"]); $video.=$C15.$vid.$C15; } endfor; }}
	
	### Тэги
	$t=trim($item["tags"], ","); $tags=""; if ($t!="") { $ta=DB("SELECT * FROM `_tags` WHERE (`id` IN (".$t.")) LIMIT 3"); for ($i=0; $i<$ta["total"]; $i++): @mysql_data_seek($ta["result"],$i); $ar=@mysql_fetch_array($ta["result"]);
	$tags.="<a href='/tags/$ar[id]'>$ar[name]</a>, "; endfor; $tags2=trim($tags, ", "); $tags="Тэги: ".trim($tags, ", "); } $mixblock.="<div class='ItemTags'>".$tags."</div>".$C10;
	
	### Аватар автора, Автор и дата
	if ($item["role"]>0) {
		if ($item["avatar"]=="" || !is_file($ROOT."/".$item["avatar"]) || filesize($ROOT."/".$item["avatar"])<100) { $avatar="<img src='/userfiles/avatar/no_photo.jpg'>"; } else { $avatar="<img src='/".$item["avatar"]."'>"; }
		$d=ToRusData($item["data"]); if ($item["uid"]!=0 && $item["nick"]!="") { $auth=$avatar."Автор: <a href='http://".$VARS["mdomain"]."/users/view/".$item["uid"]."/'>".$item["nick"]."</a>, ".$d[1]; } else { $auth="<img src='/userfiles/avatar/no_photo.jpg' />Автор: Народный корреспондент, ".$d[1]; }
		$mixblock.="<div class='ItemAuth'>".$auth."</div>";
	} else {
		$q="SELECT COUNT(`seen`) as `cnt` FROM `post_seen` WHERE (`pid`='".(int)$dir[2]."' && `data`<'".($item["data"]+7*60*60*24)."') LIMIT 1";
		//echo $q;
		$data2=DB($q);
		 @mysql_data_seek($data2["result"], 0); $ar2=@mysql_fetch_array($data2["result"]); $money=round($ar2["cnt"]*0.2);
		
		if ($item["avatar"]=="" || !is_file($ROOT."/".$item["avatar"]) || filesize($ROOT."/".$item["avatar"])<100) { $avatar="<img src='/userfiles/avatar/no_photo.jpg'>"; } else { $avatar="<img src='/".$item["avatar"]."'>"; }
		$d=ToRusData($item["data"]); $auth=$avatar."Пост добавлен читателем ".$d[1]."<br>Автор поста  <a href='http://".$VARS["mdomain"]."/users/view/".$item["uid"]."/'>".$item["nick"]."</a> получает ".($money+300)." руб.<br><b><a href='/users/add'>Как добавить пост и заработать</a>?</b>";
		$mixblock.="<div class='ItemAuth2'>".$auth."</div>";
	}
	
	### Лайки 
	$likes=$C10."<div class='Likes' style='text-align:center;'>Нравится эта статья? Сохрани её себе на стену:<br>".Likes($normalcap, "", "http://".$RealHost.$path, Hsc(strip_tags($lid))).$C."</div>".$C10;
	
	//if ($item["pay"]!="") { $mixblock.=$C20."<div class='PayBlock'>".$item["pay"]."</div>".$C; }
	//if ($item["adv"]!="") { $mixblock.=$C10."<div class='CBG'></div>".$C5."<div class='AdvBlock'>".$item["adv"]."</div>".$C; }
		
	### Новости по таким же тэгам
	$dtags=''; $art=explode(",", trim($item["tags"],",")); if (count($art)>0) { $limit=6; $dtags=GetRelevantNews($art, $limit, $tags2); }
	
	### Заключительный текст
	if ($item["endtext"]!="") { $endtext="<div class='ItemLid'><div class='Br'></div>".$C10.$item["endtext"].$C10."<div class='Br'></div></div>".$C5; }
	
	$text="<h1>".$cap."</h1>".$pic.$ban."<div class='ArticleContent'>".$lid.$banyandex.$ban3.$maintext."<!--TEST-->".$report.$video.$voting.$album.$event.$contacts.$frm.$endtext.$likes."<!--C-->".$mixblock.$dtags."</div>";
	return(array($text, $normalcap));
}

###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ######

function GetLentaPreView() {
	global $VARS, $GLOBAL, $dir, $RealHost, $Page, $node, $table, $table2, $table3, $table4, $table5, $table6, $table7, $link, $C, $C5, $C10, $C15, $C20, $ROOT, $C25; 
	$data=DB("SELECT `".$table."`.`name`, `".$table."`.`lname`, `".$table."`.`sname`, `".$table."`.`pic` FROM `".$table."` WHERE (`".$table."`.`id`='".(int)$dir[2]."') LIMIT 1");
	@mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]);  $ar=$item; $d=ToRusData($ar["data"]);
	
	$cap=$item["name"]."<span>".str_replace(array("\r","\n"),"", $item["lname"])."</span>";
	
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	
	$pic="<img src='/userfiles/picbig/".$ar["pic"]."' title='".$ar["name"]."' />"; if ($ar["sname"]!="") { $kryshka="<b>".$ar["sname"]."</b>"; } else { $kryshka="";}
	$text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaMainPic' id='NewsLentaList-".$ar["id"]."'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>".$C; $text.=$C25;	
	
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	
	$text.="<div class='IndexLentaSpecPics' style='float:left; margin-right:100px;'>"; if ($ar["sname"]!="") {$kryshka="<b>".$ar["sname"]."</b>"; } else { $kryshka="";}
	$pic="<img src='/userfiles/picmiddle/".$ar["pic"]."' title='".$ar["name"]."' />"; $text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaSpecPic' id='NewsLentaList-".$ar["id"]."'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a></div>";	
	
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---	
	
	$text.="<div class='IndexLentaLists' style='float:left;'>"; $pic="<img src='/userfiles/picsquare/".$ar["pic"]."' title='".$ar["name"]."' />"; if ($ar["sname"]!="") {$kryshka="<b>".$ar["sname"]."</b>"; } else { $kryshka="";}
	$text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaList' id='NewsOneList-".$ar["id"]."'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>"; $text.="</div>".$C25;
			
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	
	$text.="<div style='width:300px;'><a href='/post/view/$ar[id]' class='RightItemBlock'><img src='/userfiles/picright/$ar[pic]' /><span>".nl2br($ar["name"])."<br><i>".nl2br($ar["lname"])."</i></span></a></div>".$C25;
		
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	
	
	
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
		
		
		
		
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---	
	return(array($text, $cap));
}

###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### 
###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ###### ######   

function UniqueSeens($pid) {
	$data=strtotime(date("Y-m-d")); DB("INSERT INTO `post_seen` (`pid`,`data`,`ip`) VALUES ('".(int)$pid."','".$data."','".$_SERVER["REMOTE_ADDR"]."') ON DUPLICATE KEY UPDATE `seen`=`seen`+1");
	$data=DB("SELECT `pid` FROM `post_seen` WHERE (`pid`='".$pid."')"); DB("UPDATE `post_lenta` SET `seen`='".$data["total"]."' WHERE (`id`='".$pid."')"); return($data["total"]);
}
?>