<?
### Запрашиваемый файл должен определять переменную $rsstext 

$rsstext='<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url><loc>http://'.$GLOBAL["host"].'</loc></url>
<url><loc>http://'.$GLOBAL["host"].'/best/</loc></url>
';

$datat=DB("SELECT `link` FROM `_pages` WHERE (`stat`='1' && `inmap`='1' && `hidden`='0')");
for($it=0; $it<$datat["total"]; $it++) { @mysql_data_seek($datat["result"], $it); $at=@mysql_fetch_array($datat["result"]);
$rsstext.='<url><loc>http://'.$GLOBAL["host"].'/'.$at["link"].'</loc></url>
'; }

$q=""; foreach($tables as $table) { $tmp=explode("_", $table); $link=$tmp[0]; $q.="(SELECT `$table`.`id`, `$table`.`name`,  `$table`.`data`, `$table`.`pic`, `_pages`.`link` FROM `$table` LEFT JOIN `_pages` ON `_pages`.`link`='$link'  WHERE (`$table`.`stat`='1' && `$table`.`promo`!='1') GROUP BY 1 ORDER BY `data` DESC LIMIT 1000) UNION ";}
$datat=DB(trim($q, "UNION ")); for($it=0; $it<$datat["total"]; $it++) { @mysql_data_seek($datat["result"], $it); $at=@mysql_fetch_array($datat["result"]); $rsstext.='<url><loc>http://'.$GLOBAL["host"].'/'.$at["link"].'/view/'.$at["id"].'</loc></url>
'; }

$rsstext.='</urlset>';
?>