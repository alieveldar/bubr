<?
### Запрашиваемый файл должен определять переменную $rsstext 

$rsstext='<?xml version="1.0" encoding="UTF-8"?><rss version="2.0">
<channel>
<atom:link href="http://'.$GLOBAL["host"].'/rss.xml" rel="self" type="application/rss+xml"/>
<title>'.$VARS["sitename"].'</title>
<link>http://'.$GLOBAL["host"].'</link>
<description>'.$VARS["sitename"].'</description>
<lastBuildDate>'.date("r").'</lastBuildDate>
<image>
  <url>http://'.$GLOBAL["host"].'/template/index/logo.png</url>
  <title>'.$VARS["sitename"].'</title>
  <link>http://'.$GLOBAL["host"].'</link>
</image>';


$q=""; foreach($tables as $table) { $tmp=explode("_", $table); $link=$tmp[0]; $q.="(SELECT `$table`.`id`, `$table`.`name`, `$table`.`lid`, `$table`.`text`, `$table`.`endtext`, `$table`.`data`, `$table`.`pic`, `_pages`.`link` FROM `$table` LEFT JOIN `_pages` ON `_pages`.`link`='$link'  WHERE (`$table`.`stat`='1' && `$table`.`promo`!='1') GROUP BY 1) UNION ";}
$datat=DB(trim($q, "UNION ")." ORDER BY `data` DESC LIMIT 20");
for($it=0; $it<$datat["total"]; $it++) { @mysql_data_seek($datat["result"], $it); $at=@mysql_fetch_array($datat["result"]);
	if ($at["pic"]!="") { $rsstexti='<enclosure url="http://'.$GLOBAL["host"].'/userfiles/picbig/'.$at["pic"].'" type="image/jpeg" />'; }
	// === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === ===
	if ($at["text"]!="") { $fulltext=$at["text"]; }
		// ФОТООТЧЕТ === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === ===
		$p=DB("SELECT * FROM `_widget_pics` WHERE (`link`='$at[link]' && `stat`='1' && `pid`='$at[id]' && `point`='report') ORDER BY `rate` ASC");
		if ($p["total"]>0) { for ($i=0; $i<$p["total"]; $i++): mysql_data_seek($p["result"],$i); $ap=@mysql_fetch_array($p["result"]);
			if ($ap["name"]!='' && $ap["showname"]==1) { $fulltext.="<h2>$ap[name]</h2>"; }
			$fulltext.="<img src='http://".$GLOBAL["host"]."/userfiles/picoriginal/$ap[pic]'>";
			if ($ap["text"]!='') { $fulltext.=$ap["text"]; }
		endfor; }
		// ВИДЕО === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === 
		$p=DB("SELECT * FROM `_widget_video` WHERE (`link`='$at[link]' && `stat`='1' && `pid`='$at[id]') ORDER BY `rate` ASC");
		if ($p["total"]>0) { for ($i=0; $i<$p["total"]; $i++): mysql_data_seek($p["result"],$i); $ap=@mysql_fetch_array($p["result"]);
			if ($ap["name"]!='') { $fulltext.="<h2>$ap[name]</h2>"; } if ($ap["text"]!='') { $fulltext.=$ap["text"]; }
		endfor; }
		// === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === =
	if ($at["endtext"]!="") {  $fulltext.="<span style='font-style:italic;'>".$at["endtext"]."</span>"; }
	// === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === ===
	$rsstext.='
	<item> 
		<title>'.str_replace(array("\r","\n","—","&mdash;",'&laquo;','&raquo;'), array(" ", " ", "-", "-", '"', '"'), htmlentities(str_replace(array("\r","\n","—","&mdash;",'&laquo;','&raquo;'), array(" ", " ", "-", "-", '"', '"'), $at["name"]), ENT_QUOTES)).'</title>
		<author>http://'.$GLOBAL["host"].'</author>
		<pubDate>'.date("r", $at["data"]).'</pubDate>
		<link>http://'.$GLOBAL["host"].'/'.$at["link"].'/view/'.$at["id"].'</link>'.$rsstexti.'
		<guid isPermaLink="true">http://'.$GLOBAL["host"].'/'.$at["link"].'/view/'.$at["id"].'</guid>
		<description><![CDATA[ '.$at["lid"].' ]]></description>
		<content:encoded><![CDATA[ '.$fulltext.' ]]></content:encoded>
	</item>';
}

$rsstext.='
</channel>
</rss>';

$rsstext=str_replace(array("   ", "  "), " ", $rsstext);
$rsstext=str_replace(array("   ", "  "), " ", $rsstext);
?>