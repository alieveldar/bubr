<?
### Генерация тизеров для Челнов, Казани и Самары 
$ROOT = $_SERVER['DOCUMENT_ROOT']; $GLOBAL["sitekey"] = 1; @require_once($ROOT."/modules/standart/DataBase.php");

// ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ---- 

$filename=$ROOT."/teaser_progoroda.html"; $css='<style>.teaser_bubr { clear:both; margin:20px 0; padding:7px; border-radius:7px; border:1px solid #CCC; overflow:hidden; }
.teaser_bubr_table { width:100%; border:none; border-collapse:collapse; } .teaser_bubr_table td { border:none; border-collapse:collapse; width:33%; vertical-align:top; text-align:center; } .teaser_bubr_table td:nth-child(1) { padding-right:10px; } .teaser_bubr_table td:nth-child(2) { padding-right:5px; padding-left:5px; } .teaser_bubr_table td:nth-child(3) { padding-left:10px; } .teaser_bubr_table img { border:none; width:100%; height:auto; }
.teaser_bubr_caption { text-align:center; } .teaser_bubr_caption a { color:#000; font:19px/30px Arial; font-weight:bold; }
.teaser_bubr_caption2 { text-align:center; margin-bottom:10px; } .teaser_bubr_caption2 a { color:#000; font:16px/26px Arial; font-weight:normal; }
</style>';

$datat=DB("SELECT `_widget_pics`.`pid`, COUNT(`_widget_pics`.`id`) as `cnt` FROM `_widget_pics` LEFT JOIN `post_lenta` ON `post_lenta`.`id`=`_widget_pics`.`pid`
WHERE (`_widget_pics`.`link`='post' && `_widget_pics`.`stat`=1 && `post_lenta`.`stat`=1 && `post_lenta`.`mailtizer`=1)
GROUP BY `_widget_pics`.`pid` HAVING (COUNT(`_widget_pics`.`id`)>2) ORDER BY `_widget_pics`.`data` DESC LIMIT 1"); @mysql_data_seek($datat["result"], 0); $at=@mysql_fetch_array($datat["result"]); $id=$at["pid"];

$q="SELECT `_widget_pics`.`pic`, `post_lenta`.`name`,`post_lenta`.`lname` FROM `_widget_pics` LEFT JOIN `post_lenta` ON `post_lenta`.`id`=`_widget_pics`.`pid` WHERE (`_widget_pics`.`pid`='".$id."') GROUP BY 1 ORDER BY RAND() LIMIT 3"; $data=DB($q);
if ($data["total"]==3) { $text.="<table class='teaser_bubr_table'><tr>"; for($it=0; $it<$data["total"]; $it++): @mysql_data_seek($data["result"], $it); $ar=@mysql_fetch_array($data["result"]);
	$cap="<div class='teaser_bubr_caption'><a href='http://bubr.ru/post/view/".$id."' target='_blank' rel='nofollow'>".$ar["name"]."</a></div>";
	$cap.="<div class='teaser_bubr_caption2'><a href='http://bubr.ru/post/view/".$id."' target='_blank' rel='nofollow'>".$ar["lname"]."</a></div>";
	
	$text.="<td><a href='http://bubr.ru/post/view/".$id."' target='_blank' rel='nofollow'><img src='http://bubr.ru/userfiles/picmiddle/".$ar["pic"]."' title='".$ar["name"]."' alt='".$ar["name"]."'></a></td>";
endfor; $text.="</table>"; } @file_put_contents($filename, $css."<div class='teaser_bubr'>".$cap."<noindex>".$text."</noindex>"."</div>");

// ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ---- 

$filename1=$ROOT."/prokazan_main.html"; $filename2=$ROOT."/prokazan_main.xml"; $text1=""; 
$text2='<?xml version="1.0" encoding="UTF-8" ?><rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0"><channel><atom:link href="http://'.$GLOBAL["host"].'/rss.xml" rel="self" type="application/rss+xml"/><title>Bubr.ru index news</title><item>';
$css="<style>.BubrDiv { width:99.5%; overflow:hidden; position:relative; z-index:1; border:1px solid #AAA; } .BubrDiv img { width:100%; height:auto; border:none; position:relative; z-index:1; }
.BubrDiv .BubrName { position:absolute; left:0; bottom:3px; z-index:100; } .BubrDiv .BubrName span { display:inline-block; background:#FFF; color:#000; font-size:19px; padding:2px 4px; line-height:18px; }
.BubrDiv .BubrName i { display:inline-block; background:#FFF; color:#000; font-style:normal; font-size:16px; padding:2px 4px; line-height:15px; }
.BubrDiv .BubrSite { position:absolute; right:3px; top:3px; z-index:110; color:rgba(255,255,255,0.8); font-size:15px; line-height:19px; font-weight:bold; } .BubrDiv .BubrSite a { color:rgba(255,255,255,0.8); }
</style>";

$q="SELECT `id`,`name`,`lname`,`pic`,`data`,`lid` FROM `post_lenta` WHERE (`pkmain`=1 && `stat`=1) ORDER BY `data` DESC LIMIT 1"; $data=DB($q); $text2="";  $text1="";
if ($data["total"]==1) { @mysql_data_seek($data["result"],0); $ar=@mysql_fetch_array($data["result"]);
	$text1=$css."<noindex><div class='BubrDiv'><a href='http://bubr.ru/post/view/$ar[id]' rel='nofollow'><img src='http://".$GLOBAL["host"]."/userfiles/picbig/".$ar["pic"]."' border='0'>
	<div class='BubrName'>".GetSpanCaption($ar["name"],$ar["lname"])."</div><div class='BubrSite'><a href='http://bubr.ru' target='_blank' rel='nofollow'>BUBR.RU</a></div></a></div></noindex>";
	
	$text2.='<title>'.$ar["name"].'</title>
	<ttwo>'.$ar["lname"].'</ttwo>
	<link>http://'.$GLOBAL["host"].'/post/view/'.$ar["id"].'</link>
	<pic>'.$ar["pic"].'</pic>
	<data>'.$ar["data"].'</data>
	<lid>'.$ar["lid"].'</lid>';
	$text2.='</item></channel></rss>'; 	
} @file_put_contents($filename1, $text1); @file_put_contents($filename2, $text2);

// ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ---- 

$filename3=$ROOT."/prokazan_news.xml"; $text3='<?xml version="1.0" encoding="UTF-8" ?><rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0"><channel><atom:link href="http://'.$GLOBAL["host"].'/rss.xml" rel="self" type="application/rss+xml"/><title>Bubr.ru index news</title>';
$q="SELECT `id`,`name`,`lname`,`pic`,`data`,`lid`,`samara` FROM `post_lenta` WHERE (`pknews`=1 && `stat`=1) ORDER BY `data` DESC LIMIT 10"; $data=DB($q);
if ($data["total"]>0) { for($i=0; $i<$data["total"]; $i++):  @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
$text3.='<item><title>'.$ar["name"].'</title><ttwo>'.$ar["lname"].'</ttwo><link>http://'.$GLOBAL["host"].'/post/view/'.$ar["id"].'</link><pic>http://'.$GLOBAL["host"].'/userfiles/picsquare/'.$ar["pic"].'</pic>
<picmiddle>http://'.$GLOBAL["host"].'/userfiles/picmiddle/'.$ar["pic"].'</picmiddle><adv>'.$ar["samara"].'</adv><picbig>http://'.$GLOBAL["host"].'/userfiles/picbig/'.$ar["pic"].'</picbig><data>'.$ar["data"].'</data><lid>'.$ar["lid"].'</lid></item>';
endfor; $text3.='</channel></rss>'; @file_put_contents($filename3, $text3); }

// ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ---- 
$filename4=$ROOT."/prokazan_news.html"; $css4="<style>.BubrDiv { width:32%; margin-right:1.3%; float:left; overflow:hidden; position:relative; z-index:1; border:1px solid #AAA; }
.BubrDiv img { width:100%; height:auto; border:none; position:relative; z-index:1; } .BubrDiv .BubrName { position:absolute; left:0; bottom:0px; z-index:100; }
.BubrDiv .BubrName span { display:inline-block; background:#FFF; color:#000; font-size:15px; padding:0px 3px; line-height:19px; font-weight:bold; }
.BubrDiv .BubrName i { display:inline-block; background:#FFF; color:#000; font-style:normal; font-size:12px; padding:0px 3px; line-height:16px; }
.BubrDivS .BubrDiv:last-of-type { margin-right:0px; } .BubrDivS { overflow:hidden; margin-bottom:30px; }
.BubrDiv .BubrSite { position:absolute; right:3px; top:3px; z-index:110; color:rgba(255,255,255,0.8); font-size:12px; line-height:15px; font-weight:bold; } .BubrDiv .BubrSite a { color:rgba(255,255,255,0.8); }
</style>";
$q="SELECT `id`,`name`,`lname`,`pic` FROM `post_lenta` WHERE (`pk3st`=1 && `stat`=1) ORDER BY `data` DESC LIMIT 3"; $data=DB($q); if ($data["total"]>0) { for($i=0; $i<$data["total"]; $i++):  @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
	$text4.="<div class='BubrDiv'><a href='http://bubr.ru/post/view/$ar[id]' rel='nofollow' target='_blank'><img src='http://".$GLOBAL["host"]."/userfiles/picmiddle/".$ar["pic"]."' border='0'>
	<div class='BubrName'>".GetSpanCaption($ar["name"],$ar["lname"])."</div><div class='BubrSite'><a href='http://bubr.ru' target='_blank' rel='nofollow'>BUBR.RU</a></div></a></div>";
endfor; @file_put_contents($filename4, $css4."<noindex><div class='BubrDivS'>".$text4."</div></noindex>"); }

// ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----

function GetSpanCaption($cap, $ds='') { $cap="<span>".nl2br(trim($cap, "."))."</span>"; $cap=str_replace("<br />", "</span><br /><span>", $cap); $cap=str_replace("<span></span>", "", $cap); $cap=str_replace("<span> </span>", "", $cap); if ($ds!="") { $ds="<i>".nl2br(trim($ds, "."))."</i>"; $ds=str_replace("<br />", "</i><br /><i>", $ds); $ds=str_replace("<i></i>", "", $ds); $ds=str_replace("<i> </i>", "", $ds); $ds="<br>".$ds;  } $cap=$cap.$ds; $cap=str_replace(array("\r","\n"), "", $cap); return $cap; }

?>