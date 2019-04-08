<?
### Запрашиваемый файл должен определять переменную $rsstext 

$rsstext='<?xml version="1.0" encoding="UTF-8" ?><rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
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


$q=""; foreach($tables as $table) { $tmp=explode("_", $table); $link=$tmp[0]; $q.="(SELECT `$table`.`id`, `$table`.`name`, `$table`.`lid` as `text`, `$table`.`data`, `$table`.`pic`, `_pages`.`link` FROM `$table` LEFT JOIN `_pages` ON `_pages`.`link`='$link'  WHERE (`$table`.`stat`='1' && `$table`.`promo`!='1') GROUP BY 1) UNION ";}
$datat=DB(trim($q, "UNION ")." ORDER BY `data` DESC LIMIT 20"); for($it=0; $it<$datat["total"]; $it++) { @mysql_data_seek($datat["result"], $it); $at=@mysql_fetch_array($datat["result"]);
if ($at["pic"]!="") {
	$rsstexti='
	<enclosure url="http://'.$GLOBAL["host"].'/userfiles/picpreview/'.$at["pic"].'" type="image/jpeg" />';
	#$rsstextim='<a href="http://'.$GLOBAL["host"].'/'.$link.'/view/'.$at["id"].'"><img src="http://'.$GLOBAL["host"].'/userfiles/picsquare/'.$at["pic"].'" style="float:left; margin:0 10px 10px 0;"></a>';
}
$rsstext.='
<item>
	<title>'.str_replace(array("\r","\n","—","&mdash;",'&laquo;','&raquo;'), array(" ", " ", "-", "-", '"', '"'), htmlentities(str_replace(array("\r","\n","—","&mdash;",'&laquo;','&raquo;'), array(" ", " ", "-", "-", '"', '"'), $at["name"]), ENT_QUOTES)).'</title>
	<author>http://'.$GLOBAL["host"].'</author>
	<pubDate>'.date("r", $at["data"]).'</pubDate>
	<link>http://'.$GLOBAL["host"].'/'.$at["link"].'/view/'.$at["id"].'</link>'.$rsstexti.'
	<guid isPermaLink="true">http://'.$GLOBAL["host"].'/'.$at["link"].'/view/'.$at["id"].'</guid>
	<description><![CDATA[ '.$at["text"].' ]]></description>';	
	#<description><![CDATA[ '.$rsstextim.$at["text"].'<div style="clear:both;"></div> ]]></description>
$rsstext.='
</item>';
}

$rsstext.='
</channel>
</rss>';
?>