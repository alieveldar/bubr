<?
$table=$link."_lenta"; $table2="_widget_pics"; $table3="_widget_votes";
if ($start=="") { $start="list"; $dir[1]="list"; }
$file=$table."-".$start.".".$page.".".$id;
$VARS["cachepages"] = 0;

#############################################################################################################################################

if ($start=="view") {
	$where=$GLOBAL["USER"]["role"]==0?"AND `$table`.`stat`=1":"";
	$data=DB("SELECT `$table`.`id`, `$table`.`domain`, `$table`.`name`, `$table`.`text`, `$table`.`comments`, `$table`.`votingend`, `$table`.`pic`, `$table`.`elemsstyle`, `$table`.`winnerscount`, `_pages`.`design`
	FROM `".$table."` LEFT JOIN `_pages` ON `_pages`.`domain`=`$table`.`domain` WHERE (`$table`.`id`='".(int)$dir[2]."' $where) LIMIT 1");
	
	if ($data["total"]) {
		@mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]);
	
		if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); } else { list($text, $cap)=GetConcursId($item); SetCache($file, $text, $cap); } UserTracker($link, $page); $text.=UsersComments($link, $page, $item['comments']);
		$edit="<div id='AdminEditItem'><a href='".$GLOBAL["mdomain"]."/admin/?cat=".$link."_edit&id=".(int)$dir[2]."'>Редактировать</a></div>";
		
		$or=$_SESSION['userid']?"`uid`=".$_SESSION['userid']:"`ip`='".$GLOBAL["ip"]."'";
		if($GLOBAL["now"] < $item["votingend"]) { $q=DB("SELECT `id` FROM `$table3` WHERE ($or AND `pid`=".$item['id']." AND `link`='$link' AND `data`>".$GLOBAL["tonight"].")"); }
		if($GLOBAL["now"] > $item["votingend"] || $item['voting'] == 1 && !$_SESSION['userid'] || $q["total"]) { $script='<script type="text/javascript">$(\'.votingButton a\').remove();</script>'; }
		if($q["total"] && $_SESSION['userid']==0) { $script.='<script type="text/javascript">$("#MsgAboutHave").show(); $("#MsgAboutHave").html("Сегодня с вашего IP адреса уже принят голос!");</script>'; }
		if($q["total"] && $_SESSION['userid']!=0) { $script.='<script type="text/javascript">$("#MsgAboutHave").show(); $("#MsgAboutHave").html("Сегодня вы уже голосовали в этом конкурсе!");</script>'; }
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
	
	$cap=$item["name"]; $text = ''; $node["design"]=$item["design"];
	if($item['pic']) $text.='<div class="nodePic"><a href="/userfiles/picoriginal/'.$item['pic'].'" rel="prettyPhoto[gallery]"><img src="/userfiles/picnews/'.$item['pic'].'" border="0" /></a></div>';
	$text .= $item['text'];
	$path='/userfiles/picnews/'.$item["pic"];
	
	// Если голосование окончено, выводим победителей
	if($GLOBAL["now"] > $item["votingend"]) {
		$data=DB("SELECT `".$table2."`.*, COUNT(`".$table3."`.id) as `cnt` FROM `".$table2."` LEFT JOIN `".$table3."` ON `".$table3."`.`vid`=`".$table2."`.`id` WHERE (`".$table2."`.`link`='".($dir[0])."' AND `".$table2."`.`pid`=".(int)$dir[2].") GROUP BY 1 ORDER BY `cnt` DESC LIMIT ".$item['winnerscount']);			
		$text.='<div class="C30"></div><h2 class="CenterText">Голосование окончено. ';
		$text.=$item['winnerscount'] == 1 ? 'Победитель:</h2>' : 'Победители:</h2>';		
		$text.='<div class="CBG"></div>';			
	}

	// Если голосование не окончено, выводим список кандидатов
	else{
		$data=DB("SELECT `".$table2."`.*, COUNT(`".$table3."`.`id`) as `cnt` FROM `".$table2."` LEFT JOIN `".$table3."` ON `".$table3."`.`vid`=`".$table2."`.`id` WHERE (`".$table2."`.`link`='".($dir[0])."' AND `".$table2."`.`pid`=".(int)$dir[2]." AND `".$table2."`.`stat`=1) GROUP BY 1 ORDER BY `".$table2."`.`rate` ASC");			
		$q=DB("SELECT `id` FROM `$table3` WHERE (`ip`='".$GLOBAL["ip"]."' AND `pid`=".(int)$dir[2]." AND `link`='$link' AND `data`>".$GLOBAL["tonight"].")");
		$text.='<div class="C30"></div><h2 class="CenterText">Голосование</h2><div class="CBG"></div><div id="MsgAboutHave"></div>';
		if($item['voting'] == 1 && !$_SESSION['userid']) $text.='<div class="Info CenterText">Голосовать могут только зарегистрированные пользователи</div>';		
	}
	
	if($data['total']){
		$pics = '';
		for ($i=0, $j=0; $i<$data["total"]; $i++) {
			@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
			if($ar["cnt"] == 0 && $GLOBAL["now"] > $item["votingend"]) continue;
			$pics.='<ins>';
		if ($item['elemsstyle'] == 2) {
			$pics.='<div class="votingImg"><a href="/userfiles/picoriginal/'.$ar["pic"].'" title=\''.$ar["name"].'\' rel="prettyPhoto[gallery]"><img title=\''.Hsc($ar["name"]).'\'  src="/userfiles/picpreview/'.$ar['pic'].'" border="0" /></a></div>';
			$pics.='<div class="votingText"><h3 class="votingName">'.$ar["name"].'</h3>'.$ar["text"].'</div>';
		} else {
			$pics.='<div class="votingImg"><a href="/userfiles/picoriginal/'.$ar["pic"].'" title=\''.$ar["name"].'\' rel="prettyPhoto[gallery]"><img title=\''.Hsc($ar["name"]).'\'  src="/userfiles/picpreview/'.$ar['pic'].'" border="0" /></a><div class="C10"></div>'.$ar["name"].'</div>';
		}
			
			$pics.='<div class="votingButton">';
			$pics.='<a href="javascript:void(0);" onclick=\'voteForm('.$ar["id"].', '.$item["id"].', "'.$link.'", "'.Hsc($cap).'", "Я голосую за: '.Hsc($ar["name"]).'", "http://'.$GLOBAL["host"]."/".$GLOBAL["page"].'", "http://'.$GLOBAL["host"].'/userfiles/picpreview/'.$ar['pic'].'");\'>Голосовать</a>';									
			$pics.='<strong>Голосов: <span class="votes">'.$ar["cnt"].'</span></strong>';			
			$pics.='</div></ins>';
		if ($item['elemsstyle'] == 2) { $pics.='<div class="CBG"></div>'; }
			$j++;
		}
		
		$elemsStyle = $GLOBAL["now"] > $item["votingend"] ? 2 : $item['elemsstyle'];
		$text.='<div class="voting elemsStyle'.$elemsStyle.'">'.$pics.'</div>'.$C;		
	}
	$text.=$C10."<div class='Likes'>".Likes(Hsc($cap), "", "http://".$RealHost.$path, Hsc(strip_tags($item['lid']))).$C."</div>".$C10;
	return(array($text, $cap));
}

#############################################################################################################################################

function GetLentaList() {
	global $VARS, $GLOBAL, $dir, $ORDERS, $RealHost, $Page, $node, $UserSetsSite, $table, $table2, $table3, $table4, $table5, $C, $C20, $C10, $C25;
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$RealHost."'>Главная</a> &raquo; ".$node["name"]."</div>";
	
	//$where=$GLOBAL["USER"]["role"]==0?"WHERE (`".$table."`.`stat`=1)":"";
	$onpage=$node["onpage"]; $pg = $dir[2] ? $dir[2] : 1; $orderby=$ORDERS[$node["orderby"]]; $from=($pg - 1)*$onpage; $onblock=4; /* Новостей в каждом блоке */
	$data=DB("SELECT `".$table."`.id,  `".$table."`.name, `".$table."`.uid, `".$table."`.pic, `".$table."`.data,`".$table."`.comcount, `".$table."`.comments, `_users`.`nick`
	FROM `".$table."` LEFT JOIN `_users` ON `".$table."`.`uid`=`_users`.`id` WHERE (`".$table."`.`stat`=1)  GROUP BY 1 ".$orderby." LIMIT $from, $onpage");
	
	for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]); $pic="";
		if ($ar["pic"]!="") { if (strpos($ar["pic"], "old")!=0) { /*Старый вид картинок*/ $pic="<img src='".$ar["pic"]."' title='".$ar["name"]."' />"; } else { /*Новый вид картинок*/ $pic="<img src='/userfiles/pictavto/".$ar["pic"]."' title='".$ar["name"]."' />"; }}
		if ($ar["uid"]!=0 && $ar["nick"]!="") { $auth="<a href='http://".$VARS["mdomain"]."/users/view/".$ar["uid"]."/'>".$ar["nick"]."</a>"; } else { $auth="<a href='http://".$VARS["mdomain"]."/add/2/'>Народный корреспондент</a>"; }
		if ($UserSetsSite[3]==1 && $ar["comments"]!=2) { $coms="<div class='CommentBox'><a href='/".$dir[0]."/view/".$ar["id"]."#comments'>".$ar["comcount"]."</a></div>"; } else { $coms=""; }
		$text.="<div class='NewsLentaList' id='NewsLentaList-".$ar["id"]."'><a href='/".$dir[0]."/view/".$ar["id"]."'>".$pic."</a><h2><a href='/".$dir[0]."/view/".$ar["id"]."'>".$ar["name"]."</a></h2>".$C."
		<div class='Info'><div class='Other'>".Replace_Data_Days($d[4]).",  <a href='/".$dir[0]."/cat/".$ar["cat"]."'>".$ar["ncat"]."</a>,  Автор: ".$auth."</div>".$coms."</div></div>";
		if($data["total"]>($i+1)){ if (($i+1)%$onblock==0) { $text.=$C25."<div class='banner2' style='margin-left:10px;' id='Banner-6-".(floor($i/$onblock)+1)."'></div>".$C; } else { $text.=$C25; }}
	}

	$data=DB("SELECT count(id) as `cnt` FROM `".$table."`"); @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
	$text.=Pager2($pg, $onpage, ceil($ar["cnt"]/$onpage), $dir[0]."/".$dir[1]."/[page]");
	return(array($text, ""));
}
?>