<?
$limit=300; $file="sitemap-sitemap";
if (RetCache($file)=="true") { list($text, $cap)=GetCache($file, 0); } else { list($text, $cap)=CreateSiteMap($limit); SetCache($file, $text, ""); }
$Page["Content"]=$text; $Page["Caption"]="Карта сайта"; 

function CreateSiteMap($limit){
	global $VARS, $C20, $Domains, $C5; $r=mysql_query("SHOW TABLES"); if (mysql_num_rows($r)>0) { while($row = mysql_fetch_array($r, MYSQL_NUM)) { if (strpos($row[0], "_lenta")!==false) { $tables[] = $row[0]; }}}
	foreach($tables as $table) {
		$tmp=explode("_", $table); $link=$tmp[0]; 

		#$data=DB("SELECT `$table`.`id`, `$table`.`name`, `$table`.`data`, `_pages`.`domain`, `_pages`.`link`, `_pages`.`name` as `catname` FROM `$table` LEFT JOIN `_pages` ON `_pages`.`link`='$link' WHERE (`$table`.`stat`='1') GROUP BY 1 ORDER BY `$table`.`data` DESC LIMIT 500");
		$data=DB("SELECT `$table`.`id`, `$table`.`name`,  `$table`.`data`, `_pages`.`domain`, `_pages`.`link`, `_pages`.`name` as `catname` FROM `$table` LEFT JOIN `_pages` ON `_pages`.`link`='$link' WHERE (`$table`.`stat`='1') GROUP BY 1 ORDER BY `id` DESC LIMIT 500"); 

		if ($data["total"]>0) {
			@mysql_data_seek($data["result"], 0); $tmp=@mysql_fetch_array($data["result"]); $text.="<div class='MainRazdelName'><a href='http://".trim($Domains[$tmp["domain"]].".".$VARS["mdomain"], ".")."/".$tmp["link"]."/'>".$tmp["catname"]."</a></div>";
			$path="http://".trim($Domains[$tmp["domain"]].".".$VARS["mdomain"], ".")."/".$tmp["link"]."/view/"; for($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]); 
			$text.="<b style='color:#777; font-size:10px;'>".$d[4]."</b> ... <a style='font-size:11px;' href='".$path.$ar["id"]."'>".$ar["name"]."</a>".$C5;
		}
	} $text.=$C20;
	}
	return(array($text, ""));	
}
?>