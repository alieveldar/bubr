<?
$table=$link."_lenta"; $table2="_widget_pics"; $table3="_widget_votes";
if ($start=="") { $start="list"; $dir[1]="list"; }
$file=$table."-".$start.".".$page.".".$id;

#############################################################################################################################################

if ($start=="view") {
	$where=$GLOBAL["USER"]["role"]==0?"AND `stat`=1":""; $data=DB("SELECT `id`, `name`, `lid`, `pic`, `text`, `stat`, `voting`, `votingend`, `votingmode`, `winnerscount`, `comments`, `domain` FROM `".$table."` WHERE (`id`='".(int)$dir[2]."' $where) LIMIT 1");
	if ($data["total"]) {
		@mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]);
	
		if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); } else { list($text, $cap)=GetConcursId($item); SetCache($file, $text, $cap); } UserTracker($link, $page); $text.=UsersComments($link, $page, $item['comments']);
		$edit="<div id='AdminEditItem'><a href='".$GLOBAL["mdomain"]."/admin/?cat=".$link."_edit&id=".(int)$dir[2]."'>Редактировать</a></div>";
		
		$where=$_SESSION['userid']?"`uid`=".$_SESSION['userid']:"`ip`='".$GLOBAL["ip"]."'";
		if($GLOBAL["now"] < $item["votingend"] && $item["votingmode"] == 1) $q=DB("SELECT `id` FROM `$table3` WHERE ($where AND `pid`=".$item['id']." AND `link`='$link' AND `data`>".$GLOBAL["tonight"].")");		
		if($GLOBAL["now"] > $item["votingend"] || ($item['voting'] == 1 && !$_SESSION['userid']) || $q["total"]) $script = '<script type="text/javascript">$("#node'.$item['id'].'.votingCon .votingButton a").remove();</script>';			
		
		if($GLOBAL["now"] < $item["votingend"] && $item['voting'] == 1 && !$_SESSION['userid']) $script = '<script type="text/javascript">$("#node'.$item['id'].'.votingCon .votingButton a").remove(); $("#node'.$item['id'].'.votingCon .Info").html("Голосовать могут только зарегистрированные пользователи").show();</script>';
		else if($GLOBAL["now"] < $item["votingend"] && $q["total"]) $script = '<script type="text/javascript">$("#node'.$item['id'].'.votingCon .votingButton a").remove(); $("#node'.$item['id'].'.votingCon .Info").html("Спасибо, ваш голос учтён!").show();</script>';
	}
	else { $text=@file_get_contents($ROOT."/template/404.html"); $cap="Материал не найден"; $Page404=1; }
}
if($start == 'list'){
	if (RetCache($file)=="true") { list($text, $cap)=GetCache($file, 0); } else { list($text, $cap)=GetLentaList(); SetCache($file, $text, ""); }	
	$Page["Content"]=$text; $cap=$node["name"];
}

if ($GLOBAL["USER"]["role"]>2) { $text=$C.$edit.$C.$text; } $Page["Content"] = $text.$script; $Page["Caption"] = $cap;

#############################################################################################################################################

function GetConcursId($item) {
	global $VARS, $GLOBAL, $dir, $RealHost, $Page, $node, $table, $table2, $table3, $link, $C, $C20, $C10, $C25;
	$cap=$item["name"]; $text = ''; $node["design"]=$item["design"]; if($item['lid']) { $text.="<div class='ItemLid'>".$item['lid']."</div>".$C10; }
	$text.=$item['text']; if($item['pic'] || $item['text']) { $text.=$C; } $path='http://'.trim($Domains[$item["domain"]].'.'.$VARS["mdomain"], '.').'/'.$link.'/view/'.$item['id'];
	
	$data=DB("SELECT `".$table2."`.*, COUNT(`".$table3."`.`id`) as `cnt` FROM `".$table2."` LEFT JOIN `".$table3."` ON `".$table3."`.`vid`=`".$table2."`.`id` WHERE (`".$table2."`.`link`='".$link."' AND `".$table2."`.`pid`=".$item["id"]." AND `".$table2."`.`stat`=1) GROUP BY 1 ORDER BY `".$table2."`.`rate` ASC");
	$items=array(); $horblock=array();$total=$data["total"]; for ($i=0; $i<$data["total"]; $i++){ @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $items[]=$ar; }
	
	$block=0; $i=1; foreach ($items as $ar) {
		$horblock[$block]["names"].='<td><strong>'.$ar["name"].'</strong></td>';
		$horblock[$block]["pics"].='<td><div class="votingImg"><a href="/userfiles/picoriginal/'.$ar["pic"].'" title=\''.$ar["name"].'\' rel="prettyPhoto[gallery]"><img title=\''.$ar["name"].'\'  src="/userfiles/picsquare/'.$ar['pic'].'" border="0" /></a></div>';
		$horblock[$block]["pics"].='<div class="votingButton"><strong>Голосов: <span class="votes">'.$ar["cnt"].'</span></strong><br />';
		$horblock[$block]["pics"].='<a href="javascript:void(0);" onclick=\'voteForm('.$ar["id"].', '.$item["id"].', "'.$link.'", "'.Hsc($cap).'", "Я голосую за: '.Hsc($ar["name"]).'", "'.$path.'", "http://'.$VARS["mdomain"].'/userfiles/picpreview/'.$ar['pic'].'")\'>Голосовать</a>';			
		$horblock[$block]["pics"].='</div></td>'; if ($i%3==0) { $block++; } $i++;
	}
	foreach ($horblock as $block) { $content.="<tr>".$block["names"]."</tr><tr>".$block["pics"]."</tr><tr><td colspan='3'>".$C20.$C10."</td></tr>"; }

	/* $pics = '<tr>'; for ($i=0; $i<$data["total"]; $i++){@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $pics.='<th><strong>'.$ar["name"].'</strong></th>'; } $pics .= '</tr><tr>';
	for ($i=0; $i<$data["total"]; $i++){
		@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
	 
		$pics.='<td><div class="votingImg"><a href="/userfiles/picoriginal/'.$ar["pic"].'" title=\''.$ar["name"].'\' rel="prettyPhoto[gallery]"><img title=\''.$ar["name"].'\'  src="/userfiles/picsquare/'.$ar['pic'].'" border="0" /></a></div>';
		$pics.='<div class="votingButton">';											
		$pics.='<strong>Голосов: <span class="votes">'.$ar["cnt"].'</span></strong>';
		$pics.='<a href="javascript:void(0);" onclick=\'voteForm('.$ar["id"].', '.$item["id"].', "'.$link.'", "'.Hsc($cap).'", "Я голосую за: '.Hsc($ar["name"]).'", "'.$path.'", "http://'.$VARS["mdomain"].'/userfiles/picpreview/'.$ar['pic'].'")\'>Голосовать</a>';			
		$pics.='</div></td>';$j++;
	} $pics .= '</tr>'; */
	
	$votingEnd = 'До окончания <span class="digits"></span><script>setTimeout(function(){votingCountdown('.$item['votingend'].', '.$item['winnerscount'].', '.$item['id'].')}, 1000);</script>';
	$text.='<div class="votingCon" id="node'.$item['id'].'"><span class="votingEnd">'.$votingEnd.'</span>'.$C10.'<div class="voting"><table>'.$content.'</table></div><div class="Info"></div></div>';
	$text.=$C20."<div class='Likes' style='text-align:center;'>".Likes(Hsc($cap), "", "http://".$RealHost.$path, Hsc(strip_tags($item['lid']))).$C."</div>".$C10;
	
	$text.='<!-- Яндекс.Директ --><div id="yandex3_ad"></div><script type="text/javascript">(function(w, d, n, s, t) {  w[n] = w[n] || [];   w[n].push(function() {  Ya.Direct.insertInto(126201, "yandex3_ad", {
 	ad_format: "direct", font_size: 0.9, font_family: "arial", type: "horizontal", border_type: "block", limit: 1, title_font_size: 1, border_radius: true,
 	site_bg_color: "FFFFFF", header_bg_color: "FFFFFF", border_color: "99CCFF", title_color: "0066CC", url_color: "006600", text_color: "000000", hover_color: "0066CC", no_sitelinks: true }); });
    t = d.getElementsByTagName("script")[0]; s = d.createElement("script"); s.src = "//an.yandex.ru/system/context.js"; s.type = "text/javascript"; s.async = true; t.parentNode.insertBefore(s, t);
	})(window, document, "yandex_context_callbacks");</script>'.$C20;
		
	return(array($text, $cap));
}

#############################################################################################################################################

function GetLentaList() {
	global $VARS, $GLOBAL, $dir, $ORDERS, $RealHost, $Page, $node, $UserSetsSite, $table, $table2, $table3, $link, $C, $C20, $C10, $C25;
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$RealHost."'>Главная</a> &raquo; ".$node["name"]."</div>";
	
	$SubDomain=(int)$SubDomain; $where=$GLOBAL["USER"]["role"]==0?"WHERE (`".$table."`.`domain`='".$SubDomain."' AND `".$table."`.`stat`=1)":"WHERE (`".$table."`.`domain`='".$SubDomain."')";
	$onpage=$node["onpage"]; $pg = $dir[2] ? $dir[2] : 1; $orderby=$ORDERS[$node["orderby"]]; $from=($pg - 1)*$onpage; $onblock=4; /* Новостей в каждом блоке */
	/*$data=DB("SELECT `".$table."`.id,  `".$table."`.name, `".$table."`.uid, `".$table."`.pic, `".$table."`.data,`".$table."`.comcount, `".$table."`.comments, `".$table."`.`voting`, `".$table."`.`votingend`, `_users`.`nick`
	FROM `".$table."` LEFT JOIN `_users` ON `".$table."`.`uid`=`_users`.`id` $where  GROUP BY 1 ".$orderby." LIMIT $from, $onpage");*/
	
	$data=DB("SELECT `".$table."`.id,  `".$table."`.name, `".$table."`.uid, `".$table."`.pic, `".$table."`.data,`".$table."`.comcount, `".$table."`.comments, `".$table."`.`voting`, `".$table."`.`votingend`, `".$table."`.`votingmode`, `".$table."`.`winnerscount`, `".$table."`.`domain` FROM `".$table."` WHERE (`".$table."`.`stat`=1) GROUP BY 1 ".$orderby." LIMIT $from, $onpage");
	
	if ($data["total"]>0) {
		for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $item=@mysql_fetch_array($data["result"]);
			$path='http://'.trim($Domains[$item["domain"]].'.'.$VARS["mdomain"], '.').'/'.$link.'/view/'.$item['id'];
			$data2=DB("SELECT `".$table2."`.*, COUNT(`".$table3."`.`id`) as `cnt` FROM `".$table2."` LEFT JOIN `".$table3."` ON `".$table3."`.`vid`=`".$table2."`.`id` WHERE (`".$table2."`.`link`='".$link."' AND `".$table2."`.`pid`=".$item["id"]." AND `".$table2."`.`stat`=1) GROUP BY 1 ORDER BY `".$table2."`.`rate`");		
			$pics = '<table><tr>';
			for ($j=0; $j<$data2["total"]; $j++){
				@mysql_data_seek($data2["result"], $j); $ar=@mysql_fetch_array($data2["result"]);		
				$pics.='<th><strong>'.$ar["name"].'</strong></th>';
			}
			$pics .= '</tr><tr>';
			for ($j=0; $j<$data2["total"]; $j++){
				@mysql_data_seek($data2["result"], $j); $ar=@mysql_fetch_array($data2["result"]);
				$pics.='<td><div class="votingImg"><a href="/userfiles/picoriginal/'.$ar["pic"].'" title=\''.$ar["name"].'\' rel="prettyPhoto[gallery]"><img title=\''.$ar["name"].'\'  src="/userfiles/picsquare/'.$ar['pic'].'" border="0" /></a></div>';
				$pics.='<div class="votingButton">';											
				$pics.='<strong>Голосов: <span class="votes">'.$ar["cnt"].'</span></strong>';
				$pics.='<a href="javascript:void(0);" onclick=\'voteForm('.$ar["id"].', '.$item["id"].', "'.$link.'", "'.Hsc($cap).'", "Я голосую за: '.Hsc($ar["name"]).'", "'.$path.'", "http://'.$VARS["mdomain"].'/userfiles/picpreview/'.$ar['pic'].'")\'>Голосовать</a>';			
				$pics.='</div></td>';
			}
			$pics .= '</tr></table>';
			
			$votingEnd = 'До окончания <span class="digits"></span><script>setTimeout(function(){votingCountdown('.$item['votingend'].', '.$item['winnerscount'].', '.$item['id'].')}, 1000);</script>';
			$text.='<div class="votingCon" id="node'.$item['id'].'"><div class="MainRazdelName"><a href="'.$path.'">'.$item['name'].'</a><span class="votingEnd">'.$votingEnd.'</span></div><div class="voting">'.$pics.'</div><div class="Info"></div></div>'.$C25;
			$where=$_SESSION['userid']?"`uid`=".$_SESSION['userid']:"`ip`='".$GLOBAL["ip"]."'";
			if($GLOBAL["now"] < $item["votingend"] && $item["votingmode"] == 1) $q=DB("SELECT `id` FROM `$table3` WHERE ($where AND `pid`=".$item['id']." AND `link`='$link' AND `data`>".$GLOBAL["tonight"].")");		
			if($GLOBAL["now"] > $item["votingend"] || ($item['voting'] == 1 && !$_SESSION['userid']) || $q["total"]) $script = '<script type="text/javascript">$("#node'.$item['id'].'.votingCon .votingButton a").remove();</script>';			
			if($GLOBAL["now"] < $item["votingend"] && $item['voting'] == 1 && !$_SESSION['userid']) $script = '<script type="text/javascript">$("#node'.$item['id'].'.votingCon .votingButton a").remove(); $("#node'.$item['id'].'.votingCon .Info").html("Голосовать могут только зарегистрированные пользователи").show();</script>';
			else if($GLOBAL["now"] < $item["votingend"] && $q["total"]) $script = '<script type="text/javascript">$("#node'.$item['id'].'.votingCon .votingButton a").remove(); $("#node'.$item['id'].'.votingCon .Info").html("Ваш голос учтён").show();</script>';
			$text.=$script;
			
			/*if ($ar["pic"]!="") { if (strpos($ar["pic"], "old")!=0) { $pic="<a href='/".$dir[0]."/view/".$ar["id"]."'><img src='".$ar["pic"]."' title='".$ar["name"]."' /></a>"; } else {
			$pic="<a href='/".$dir[0]."/view/".$ar["id"]."'><img src='/userfiles/picnews/".$ar["pic"]."' title='".$ar["name"]."' /></a>"; }}	
			if ($ar["uid"]!=0 && $ar["nick"]!="") { $auth="<a href='http://".$VARS["mdomain"]."/users/view/".$ar["uid"]."/'>".$ar["nick"]."</a>";
			} else { $auth="<a href='http://".$VARS["mdomain"]."/add/2/'>Народный корреспондент</a>"; }
			if ($UserSetsSite[3]==1 && $ar["comments"]!=2) { $coms="<a href='/".$dir[0]."/view/".$ar["id"]."#comments'>Комментарии</a>: <b>".$ar["comcount"]."</b>"; } else { $coms=""; }
			$text.="<div class='NewsLentaBlock' id='NewsLentaBlock-".$ar["id"]."'><div class='Time'><b>".$d[8]."</b></div><div class='Pic'>".$pic."</div>
			<div class='Text'><div class='Caption'><h2><a href='/".$dir[0]."/view/".$ar["id"]."'>".$ar["name"]."</a></h2></div><div class='CatAndAuth'>
			<div class='CatAuth'>Автор: ".$auth."</div><div class='Coms'>".$coms."</div></div></div>".$C."</div>";
			
			if($data["total"] > ($i + 1)){ if (($i+1)%$onblock==0) { $text.="</div>".$C10."".$C10."<div class='WhiteBlock'>"; } else { $text.=$C25; } }*/
		}
	}
	$data=DB("SELECT count(id) as `cnt` FROM `".$table."` $where"); @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
	$text.=Pager2($pg, $onpage, ceil($ar["cnt"]/$onpage), $dir[0]."/".$dir[1]."/[page]");
	return(array($text, ""));
}
?>