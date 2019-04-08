<?
$table = $link.'_albums'; $table2 = $link.'_photos'; $table3 = $link.'_votes'; if ($start=="" || (int)$start > 0) { $start="albums"; } $page=(int)$page; $file=$table."-".$start.".".$page.".".$id;

	
if ($start=="albums") {
	$data=DB("SELECT `name`, `sets`, `text` FROM `_pages` WHERE (`link`='".$link."' && `stat`='1') limit 1"); 
	if (!$data["total"]) { $cap="Материал не найден"; $text=@file_get_contents($ROOT."/template/404.html"); } else { @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $sets = explode('|', $ar['sets']); list($text, $cap)=GetAlbums();
		if($GLOBAL['USER']['role'] > 2) { $text .= '<script type="text/javascript">jQuery.each($(".Item"), function(i, val) { var fid=$(this).attr("id"); id=fid.split("-"); $(this).append("<div class=\'AdminPanel\'><div id=\'Act"+id[1]+"\' class=\'Act\'><a href=\'javascript:void(0);\' onclick=\'ItemDelete("+id[1]+", \"'.$dir[0].'\", \"DELALBUM\")\' title=\'Удалить альбом\'><img src=\'/template/standart/exit.png\'></a></div><div class=\'Act\'><a href=\'/'.$dir[0].'/edit/"+id[1]+"\' title=\'Настройки альбома\'><img src=\'/template/standart/edit.png\'></a></div></div>"); });</script>'; } 
	}
}

if ($start=="view") {
	$data=DB("SELECT `".$table."`.*, `_users`.`nick`, `_users`.`avatar`, (SELECT `".$table2."`.`pic` FROM `".$table2."` WHERE (`".$table2."`.`pid`=`".$table."`.`id`) ORDER BY `".$table2."`.`rate` ASC LIMIT 1) AS `photo1` FROM `".$table."` LEFT JOIN `_users` ON `_users`.`id`=`".$table."`.`uid` WHERE (`".$table."`.`id`=".$dir[2]." && `".$table."`.`stat`='1') limit 1"); 
	if (!$data["total"]) { $cap="Материал не найден"; $text=@file_get_contents($ROOT."/template/404.html"); } else { @mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]); list($text, $cap)=GetAlbum($item);
		if($item['photofromusers']) { $text .= '<script type="text/javascript">if(!$(".winner").size()) { $(".Add").html(\'<a href="/'.$dir[0].'/addphoto/'.$item['id'].'" class="AddBtnPic">Добавить фото</a>\').after(\'<div class="C15"></div>\'); }</script>'; }
	} 
}

if ($start=="add") {
	$data=DB("SELECT `name`, `sets`, `text` FROM `_pages` WHERE (`link`='".$link."' && `stat`='1') limit 1"); 
	if (!$data["total"]) { $cap="Материал не найден"; $text=@file_get_contents($ROOT."/template/404.html"); } else { @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $sets = explode('|', $ar['sets']); if($sets[2]) { list($text, $cap)=AddAlbum($sets); }}
}


if ($start=="photo") {
	$data=DB("SELECT `".$table2."`.*, `".$table."`.`concurs`, `".$table."`.`tags`, `".$table."`.`comments`, `".$table."`.`uid` AS `puid`, `".$table."`.`name` AS `pname`, `users1`.`nick` AS `pnick`, `users1`.`avatar` AS `pavatar`, `users2`.`nick`, `users2`.`avatar`, `_pages`.`sets` FROM `".$table2."` LEFT JOIN `".$table."` ON `".$table."`.`id`=`".$table2."`.`pid` LEFT JOIN `_users` AS `users1` ON `users1`.`id`=`".$table."`.`uid` LEFT JOIN `_users` AS `users2` ON `users2`.`id`=`".$table2."`.`uid` LEFT JOIN `_pages` ON `_pages`.`link`='".$dir[0]."' WHERE (`".$table2."`.`id`=".$dir[3]." && (`".$table2."`.`stat`='1' OR `".$table."`.`uid`=".$_SESSION['userid'].")) limit 1"); 
	if (!$data["total"]) { $cap="Материал не найден"; $text=@file_get_contents($ROOT."/template/404.html"); } else { @mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]); list($text, $cap)=GetPhoto($item); $text.=UsersComments($link, $page, $item["comments"]); }
}

if ($start=="addphoto") {
	$data=DB("SELECT `".$table."`.* FROM `".$table."` WHERE (`".$table."`.`id`=".$dir[2]." && (`".$table."`.`stat`='1' OR `".$table."`.`uid`=".$_SESSION['userid'].")) limit 1"); 
	if (!$data["total"]) { $cap="Материал не найден"; $text=@file_get_contents($ROOT."/template/404.html"); } else { @mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]); if($item['photofromusers']) { list($text, $cap)=AddPhoto($item); }}
}

$Page["Content"]=$edit.$text; $Page["Caption"]=$cap;	
	

function GetAlbums() {
	global $VARS, $GLOBAL, $dir, $ORDERS, $RealHost, $Page, $node, $link, $UserSetsSite, $table, $table2, $C, $C20, $C10, $C25;
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$RealHost."'>Главная</a> &raquo; ".$node["name"]."</div>";
		
	$cap=$node["name"]; $text="";
	$onpage=$node["onpage"]; $pg = $dir[1] ? $dir[1] : 1; $orderby=$ORDERS[$node["orderby"]]; $from=($pg - 1)*$onpage;
	$data=DB("SELECT `".$table."`.`id`, `".$table."`.`name`, `".$table."`.`uid`, `".$table."`.`data`, `".$table."`.`concurs`, (SELECT `".$table2."`.`pic` FROM `".$table2."` WHERE (`".$table2."`.`pid`=`".$table."`.`id`) ORDER BY `".$table2."`.`rate` ASC LIMIT 1) AS `photo1`, `".$table2."`.`pic`, `_users`.`nick`
	FROM `".$table."` LEFT JOIN `_users` ON `_users`.`id`=`".$table."`.`uid` LEFT JOIN `".$table2."` ON `".$table2."`.`main`=1 AND `".$table2."`.`pid`=`".$table."`.`id` WHERE (`".$table."`.`stat`=1)  GROUP BY 1 ".$orderby." LIMIT $from, $onpage");
	
	if($data["total"]) {
		$text .= '<div class="Add"></div>';
		$text .= '<div class="WhiteBlock">';
		$text .= '<div class="Albums">';
		for ($i=0; $i<$data["total"]; $i++) {
			@mysql_data_seek($data["result"], $i); $ar2=@mysql_fetch_array($data["result"]); $d=ToRusData($ar2["data"]);
			$text .= '<ins class="WhiteBlock Item" id="Item-'.$ar2['id'].'">';
			$text .= '<div class="Cover"><a href="/'.$link.'/view/'.$ar2['id'].'"><img src="/userfiles/picnews/'.($ar2['pic'] ? $ar2['pic'] : $ar2['photo1']).'"></a></div>';
			$text .= '<div class="Aname"><a href="/'.$link.'/view/'.$ar2['id'].'">'.$ar2['name'].'</a></div>';
			$text .= '<div class="Adate">'.$d[5].'</div><div class="Aauthor">Автор: <a href="/users/view/'.$ar2['uid'].'">'.$ar2['nick'].'</a></div>';
			if($ar2['concurs']) $text .= '<img src="/template/standart/concurs-tag.png" class="tag">';
			$text .= '</ins>';
		}
		$text .= '</div>';
		$text .= $C.'</div>';
		
		$data=DB("SELECT count(id) as `cnt` FROM `".$table."` WHERE (`stat`=1)"); @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
		$text.=Pager2($pg, $onpage, ceil($ar["cnt"]/$onpage), $link."/[page]");
		
	} else {
		$text.="<h2>Альбомов не найдено =(</h2>";
	}
	
	return(array($text, $cap));
}


function GetAlbum($item) {
	global $VARS, $GLOBAL, $ROOT, $dir, $ORDERS, $RealHost, $Page, $node, $link, $UserSetsSite, $table, $table2, $C, $C20, $C10, $C15, $C25, $page;
		
	$cap=$item["name"]; $d=ToRusData($item["data"]); $text="";
	$path='/userfiles/picnews/'.($item['pic'] ? $item['pic'] : $item['photo1']); $lid = CutText($item["text"], 100);
	$onpage=$node["onpage"]; $onpage=300; $pg = $dir[3] ? $dir[3] : 1; $orderby=$ORDERS[$node["orderby"]]; $from=($pg - 1)*$onpage;
	$data=DB("SELECT * FROM `".$table2."` WHERE (`pid`=".$dir[2]." AND`stat`=1)  GROUP BY 1 ORDER BY `rate` ASC LIMIT $from, $onpage");
		
	if($data["total"]) {
		if($item["text"]) { $text .= $item["text"].$C15; }
		$text.="<div class='Likes' style='text-align:center;'>".Likes(Hsc($cap), "", "http://".$RealHost.$path, Hsc(strip_tags($lid))).$C."</div>".$C15.'<div class="Add"></div>';
		$text.='<div class="AlbumPre"><a href="/'.$link.'/addphoto/'.$page.'"><img src="/modules/photoalbum/add.png"></a>'; for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar2=@mysql_fetch_array($data["result"]); $d=ToRusData($ar2["data"]); $text .= '<a href="#pic'.$ar2["id"].'"><img src="/userfiles/picsquare/'.$ar2['pic'].'"></a>'; } $text .= '</div>'.$C15;
		
		$text.='<div class="AlbumFull">'; for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar2=@mysql_fetch_array($data["result"]); $d=ToRusData($ar2["data"]);
			$text.='<a id="pic'.$ar2["id"].'" name="pic'.$ar2["id"].'"></a>'.$C20.'<h2>#'.($i+1)." ".$ar2["name"].'</h2><img src="/userfiles/picoriginal/'.$ar2['pic'].'">'.$C10; if ($ar2["text"]!="") { $text.=$ar2["text"].$C10; }
			$d=ToRusData($ar2["data"]);	if ($ar2["author"]!="") { $auth="Автор: ".$ar2["author"].", ".$d[5]; } else { $auth="Добавлено: ".$d[5]; }
			$dislikes="
			<div class='ttl' id='ttl".$ar2["id"]."'>Голосов: ".($ar2["like"]+$ar2["dislike"])."</div>
			<a href='javascript:void(0);' onclick='VotingLikes(\"0\", \"$ar2[id]\", \"$page\", \"$link\");' class='disl' id='disl".$ar2["id"]."'>Не нравится: $ar2[dislike]</a>
			<a href='javascript:void(0);' onclick='VotingLikes(\"1\", \"$ar2[id]\", \"$page\", \"$link\");' class='like' id='like".$ar2["id"]."'>Нравится: $ar2[like]</a>
			";
			$text.="<div class='Items'><div class='ItemAuth'>".$auth."</div><div class='ItemLikes'>".$dislikes."</div></div>";
		$text.=$C; } $text .= '</div>';
				
		$text.=$C10."<div class='Likes' style='text-align:center;'>".Likes(Hsc($cap), "", "http://".$RealHost.$path, Hsc(strip_tags($lid))).$C."</div>".$C15;
		$data=DB("SELECT count(id) as `cnt` FROM `".$table2."` WHERE (`pid`=".$dir[2]." AND `stat`=1)"); @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); 
		$text.=Pager2($pg, $onpage, ceil($ar["cnt"]/$onpage), $dir[0]."/".$dir[1]."/".$dir[2]."/[page]"); 
		$text.="<div class='Add'></div>";
	} else { $text.="<h2>Фотографий не найдено =(</h2><div class='Add'></div>"; } return(array($text, $cap));
}


function GetPhoto($item) {
	global $VARS, $GLOBAL, $ROOT, $dir, $ORDERS, $RealHost, $Page, $node, $link, $UserSetsSite, $table, $table2, $C, $C20, $C10, $C15, $C25;		
	
	$orderby=$ORDERS[$node["orderby"]];
	$text=""; $cap=$item["name"] ? $item["name"] : 'Фотография без названия'; $d=ToRusData($item["data"]); $sets = explode('|', $item["sets"]);
	$path='/userfiles/picnews/'.$item['pic']; $lid = CutText($item["text"], 100);
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$RealHost."'>Главная</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."'>".$node["name"]."</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."/view/".$item["pid"]."'>".$item["pname"]."</a>  &raquo; ".$cap."</div>";
	
	$text .= '<div class="WhiteBlock">';
	$text .= '<div class="ItemPic"><a href="/userfiles/picoriginal/'.$item["pic"].'" rel="prettyPhoto[gallery]"><img src="/userfiles/picoriginal/'.$item["pic"].'"/></a></div>';
	if($item['author']){
		if(!$item['concurs']) $text .= '<div class="PicAuth">Автор фотографии: '.$item['author'].'</div>';
		else $text .= '<div class="PicAuth"><h3 class="CenterText">Автор фотографии: '.$item['author'].'</h3></div>';
	}
	if($item["text"]) $text .= $C10.$item["text"];
	
	$text .= $C15."<div class='Likes'>".Likes(Hsc($cap), "", "http://".$RealHost.$path, Hsc(strip_tags($lid))).$C."</div>";
	
	if($item["maps"]){
		$text .= $C15.'<div style="display:none;"><span class="maps_'.$item["id"].'">'.$item["maps"].'</span><span class="maps_default">'.$VARS["maps"].'</span><span class="pic_'.$item["id"].'">'.$item["pic"].'</span></div>';
		$text .= '<script type="text/javascript" src="http://maps.api.2gis.ru/1.0"></script><div id="Map" style="width:'.$sets[0].'px; height:'.$sets[1].'px;"></div><script type="text/javascript">initMap('.$item["id"].');</script>';
	}
	
	$text .= $C15.'<h3><a href="http://'.$RealHost.'/'.$dir[0].'/view/'.$item["pid"].'">'.$item["pname"].'</a></h3>'.$C10;
	if ($item["pavatar"]=="" || !is_file($ROOT."/".$item["pavatar"]) || filesize($ROOT."/".$item["pavatar"])<100) { $avatar ="<img src='/userfiles/avatar/no_photo.jpg'>"; } else { $avatar ="<img src='/".$item["pavatar"]."'>"; }
	if(!$item['concurs']) $text .= '<div class="ItemAuth">'.$avatar.'Автор альбома: <a href="/users/view/'.$item['puid'].'">'.$item['pnick'].'</a></div>';
	$t=trim($item["tags"], ","); $tags=""; if ($t!="") { $ta=DB("SELECT * FROM `".$dir[0]."_tags` WHERE (`id` IN (".$t.")) LIMIT 3"); for ($i=0; $i<$ta["total"]; $i++): @mysql_data_seek($ta["result"],$i); $ar=@mysql_fetch_array($ta["result"]);
	$tags .="<a href='/$dir[0]/tags/$ar[id]'>$ar[name]</a>, "; endfor; $tags2=trim($tags, ", "); $tags="Тэги:".trim($tags, ", "); }
	$text .= "<div class='ItemTags'>".$tags."</div>";
				
	
	$data=DB("SELECT `id`, `name`, `pic`, `maps` FROM `".$table2."` WHERE (`pid`=".$item["pid"]." AND`stat`=1)  GROUP BY 1 ORDER BY `rate` ASC");
	if($data["total"] > 1){
		$text .= $C15.'<div class="CenterText"><h4>Все фотографии альбома<h4></div><div class="WhiteBlock Carusel"><div class="Container"><div class="Slider">';
		for ($i=0; $i<$data["total"]; $i++) {
			@mysql_data_seek($data["result"], $i); $ar2=@mysql_fetch_array($data["result"]);
			$text .= '<a href="/'.$dir[0].'/photo/view/'.$ar2['id'].'" title="'.$ar2['name'].'"'.($ar2['id'] == $item['id'] ? ' class="current"' : '').'><img src="/userfiles/picnews/'.$ar2['pic'].'"></a>';
		}
		$text .= '</div></div>';
		if($data["total"] > 9) $text .= '<a href="javascript:void(0);" class="btn l-btn"><img src="/template/standart/tleft1.png" onclick="$(\'.Carusel .Slider a:last\').prependTo($(\'.Carusel .Slider\'));"></a><a href="javascript:void(0);" class="btn r-btn"><img src="/template/standart/tright1.png" onclick="$(\'.Carusel .Slider a:first\').appendTo($(\'.Carusel .Slider\'));"></a>';
	}
	$text .= '</div>'.$C10.'<div class="Add"><a href="/'.$dir[0].'/view/'.$item['pid'].'" class="SaveButton">Вернуться в альбом</a></div></div>';
	return(array($text, $cap));
}

function AddPhoto($item) {
	global $page, $VARS, $GLOBAL, $ROOT, $dir, $ORDERS, $RealHost, $RealPage, $Page, $node, $link, $UserSetsSite, $table, $table2, $C, $C20, $C10, $C25, $C5;
		
	if (isset($_SESSION['Data']["SaveButton"])) {
		$P = $_SESSION['Data'];
		if(trim($P['name'])=='' || !$P['pic']) $msg='<div class="ErrorDiv">Ошибка! Поля не заполнены или заполнены неверно</div>';
		else {			
			$pic=$P['pic'];
			if($pic) { @require($ROOT."/modules/standart/ImageResizeCrop.php");
				foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) {			
					if (!is_dir($ROOT."/userfiles/".$path)) { mkdir($ROOT."/userfiles/".$path, 0777); }
					list($w,$h)=getimagesize($ROOT."/userfiles/temp/".$pic); list($sw, $sh)=explode("-", $size); if ($sw!=0 && $sh!=0) { $sk=$sw/$sh; }
					if($path=="picpreview") resize($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/".$path."/".$pic, $sw, $sh);
					else if($path=="picoriginal"){
						if($w > $sw) resize($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/".$path."/".$pic, $sw, $sh);
						else copy($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/".$path."/".$pic);
					} else{ $k = min($w / $sw, $h / $sh); $x = round(($w - $sw * $k) / 2); $y = round(($h - $sh * $k) / 2); crop($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/".$path."/".$pic, array($x, $y, round($sw * $k), round($sh * $k))); resize($ROOT."/userfiles/".$path."/".$pic, $ROOT."/userfiles/".$path."/".$pic, $sw, $sh);	}
				}				
			}
			$name = str_replace("'", "\'", $P['name']);	$text = str_replace("'", "\'", $P['text']);	$author = str_replace("'", "\'", $P['author']);	$stat = $item['photoapproval'] ? 0 : 1;
			$q="INSERT INTO `$table2` (`data`, `name`, `text`, `author`, `pic`, `pid`, `uid`, `maps`, `stat`) VALUES ('".time()."', '".$name."', '".$text."', '".$author."', '".$pic."', ".$item['id'].", ".$_SESSION['userid'].", '".$P['maps']."', ".$stat.")";
			DB($q); $last=DBL(); DB("UPDATE `$table2` SET `rate`='".$last."' WHERE  (id='".$last."')"); unset($P);
			if($stat) { $msg = '<div class="SuccessDiv">Фотография добавлена</div>'; } else { $msg = '<div class="SuccessDiv">Фотография добавлена, но будет отображена после одобрения администратором</div>'; }
			if($item['email']){	$subject = 'Новая фотография в вашем фотоальбоме'; $body = "Здравствуйте. В вашем фотоальбоме <a href='http://".$RealHost."/".$dir[0]."/view/".$item["id"]."'>".$item["name"]."</a> добавлена новая фотография. Посмотреть её можно по <a href='http://".$RealHost."/".$dir[0]."/photo/view/".$last."'>ссылке</a>."; if(!$stat) { $body .= "В данное время фотография не опубликована и ожидает вашего одобрения"; }	MailSend($item['email'], $subject, $body, $VARS["sitemail"]); }		
		}
		SD();		
	}
	if ($P['main']) { $chk="checked"; } $cap = 'Добавление фотографии'; if(isset($_SESSION['msg'])) { $msg = $_SESSION['msg']; unset($_SESSION['msg']); }
	$text='<link media="all" href="/modules/standart/multiupload/client/uploader2.css" type="text/css" rel="stylesheet">';
	$text.='<script type="text/javascript" src="/modules/standart/multiupload/client/uploader.js"></script><script type="text/javascript" src="http://maps.api.2gis.ru/1.0"></script>';
	$text.="« <a href='/$link/view/$page'>Вернуться в альбом</a>".$C10;
	$text.=$msg.'<form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post" onsubmit="return JsVerify();"><table>';			
	$text.="<tr><td class='VarText'>Название</td><td class='LongInput'><input name='name' type='text' value='".$P['name']."' style='width:480px;'></td></tr>";
	$text.="<tr><td class='VarText' style='vertical-align:top; padding-top:10px;'>Описание</td><td class='LongInput'><textarea name='text' style='outline:none; width:480px; height:70px;'>".$P['text']."</textarea></td></tr>";
	$text.='<tr><td class="VarText" style="vertical-align:top; padding-top:10px;">Фотография</td><td class="LongInput"><div class="uploaderCon" style="'.($P['pic'] ? 'display:none;' : '').'"><div class="uploader"></div>'.$C10.'<div class="Info" style="text-align:center;">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles">';
	$text.="<tr><td class='VarText'>Автор фотографии</td><td class='LongInput'><input name='author' type='text' value='".$P['author']."' style='outline:none; width:480px;'></td></tr>";
	if($P['pic']) $text.='<span class="imgCon"><img src="/userfiles/temp/'.$P['pic'].'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="pic" value="'.$P['pic'].'" /></span>';
	$text.='</div></td></tr></table>'.$C10.'<div class="CenterText"><input name="maps" type="hidden" class="maps_0"><input type="submit" name="SaveButton" class="SaveButton" value="Отправить"></div></form>';
	return(array($text, $cap));
}
?>