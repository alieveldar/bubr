<?
$tableall=array("news_lenta","auto_lenta","sport_lenta","business_lenta"); # таблицы с новостями

$file="agregator-content_mobile"; if (RetCache($file)=="true") { list($text, $cap)=GetCache($file, 0); } else { list($text, $cap)=MobilePageCenter(); SetCache($file, $text, ""); }

list($text, $cap)=MobilePageCenter();
$Page["Content"]=$text; $Page["Caption"]="";

################################################################################################################################################################################################

function MobilePageCenter() {
	global $tableall, $tablecon; $text=""; $notin=array(0);
	
	$text.="<h1><span>Главные новости</span></h1><div class='TVNEWS'>"; $q=""; $it=array(); foreach($tableall as $table) { $tmp=explode("_", $table); $link=$tmp[0];
	$q.="(SELECT `$table`.`id`, `$table`.`name`, `$table`.`data`, `$table`.`pic`, `$table`.`comments`, `$table`.`comcount`, `_pages`.`domain`, `_pages`.`link`, `_pages`.`name` as `cname` FROM `$table` LEFT JOIN `_pages` ON `_pages`.`link`='$link' WHERE (`$table`.`stat`='1' && `$table`.`onind`='1' && `$table`.`id` NOT IN (".implode(",", $notin).")) GROUP BY 1) UNION "; }	
	$q=trim($q, "UNION ")." ORDER BY `data` DESC LIMIT 3"; $data=DB($q); for($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $it[]=$ar; $notin[]=$ar["id"]; }
	foreach($it as $item){
		$d=ToRusData($item["data"]);
		$a="<a href='/".$item["link"]."/view/".$item["id"]."'>";
		$p=$a."<img src='/userfiles/picnews/".$item["pic"]."'>"."</a>";
		$n="<span>".$a.$item["name"]."</a><br><i>".$d[1]."<br>Комментарии: ".$item["comcount"]."</i></span>";
		$text.="<div class='TvItem'>".$p.$n."</a></div>";
	} $text.="</div>";

	$text.='<div class="C10"></div><script type="text/javascript">google_ad_client = "ca-pub-2073806235209608"; google_ad_slot = "8225685414"; google_ad_width = 234; google_ad_height = 60; </script><script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script><div class="C10"></div>';

	$text.="<h1><span>Интересное в городе</span></h1><div class='TVNEWS'>"; $q=""; $it=array(); foreach($tableall as $table) { $tmp=explode("_", $table); $link=$tmp[0];
	$q.="(SELECT `$table`.`id`, `$table`.`name`, `$table`.`data`, `$table`.`pic`, `$table`.`comments`, `$table`.`comcount`, `_pages`.`domain`, `_pages`.`link`, `_pages`.`name` as `cname` FROM `$table` LEFT JOIN `_pages` ON `_pages`.`link`='$link' WHERE (`$table`.`stat`='1' && `$table`.`spec`='1' && `$table`.`id` NOT IN (".implode(",", $notin).")) GROUP BY 1) UNION "; }	
	$q=trim($q, "UNION ")." ORDER BY `data` DESC LIMIT 3"; $data=DB($q); for($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $it[]=$ar; $notin[]=$ar["id"]; }
	foreach($it as $item){
		$d=ToRusData($item["data"]);
		$a="<a href='/".$item["link"]."/view/".$item["id"]."'>";
		$p=$a."<img src='/userfiles/picnews/".$item["pic"]."'>"."</a>";
		$n="<span>".$a.$item["name"]."</a><br><i>".$d[1]."<br>Комментарии: ".$item["comcount"]."</i></span>";
		$text.="<div class='TvItem'>".$p.$n."</a></div>";
	} $text.="</div>";

	$text.='<div class="C10"></div><script type="text/javascript">google_ad_client = "ca-pub-2073806235209608"; google_ad_slot = "8225685414"; google_ad_width = 234; google_ad_height = 60; </script><script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script><div class="C10"></div>';		
	
	$text.="<h1><span>Колонка редактора</span></h1><div class='RDNEWS'>"; $q=""; $it=array(); foreach($tableall as $table) { $tmp=explode("_", $table); $link=$tmp[0];
	$q.="(SELECT `$table`.`id`, `$table`.`uid`, `$table`.`name`, `$table`.`data`, `$table`.`pic`, `$table`.`comments`, `$table`.`comcount`, `_pages`.`domain`, `_pages`.`link`, `_pages`.`name` as `cname`, `_users`.`nick` FROM `$table` LEFT JOIN `_pages` ON `_pages`.`link`='$link' LEFT JOIN `_users` ON `_users`.`id`=`$table`.`uid` WHERE (`$table`.`stat`='1' && `$table`.`redak`='1' && `$table`.`id` NOT IN (".implode(",", $notin).")) GROUP BY 1) UNION "; }	
	$q=trim($q, "UNION ")." ORDER BY `data` DESC LIMIT 4"; $data=DB($q); for($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $it[]=$ar; $notin[]=$ar["id"]; }
	foreach($it as $item){
		$d=ToRusData($item["data"]);
		$a="<a href='/".$item["link"]."/view/".$item["id"]."'>";
		$p=$a."<img src='/userfiles/picsquare/".$item["pic"]."'>"."</a>";
		$n="<span>".$a.$item["name"]."</a><br><i>Автор: ".$item["nick"]."<br>Комментарии: ".$item["comcount"]."</i></span>";
		$text.="<div class='TvItem'>".$p.$n."</a></div>";
	} $text.="</div>";
	
	
	foreach($tableall as $table) { $tmp=explode("_", $table); $link=$tmp[0];
		if($table=='concurs_lenta' || $table=='demotivators_lenta') continue;
		$data=DB("SELECT `$table`.`id`, `$table`.`comments`, `$table`.`comcount`, `$table`.`name`, `$table`.`data`, `$table`.`pic`, `_pages`.`domain`, `_pages`.`link`, `_pages`.`name` as `catname` FROM `$table` LEFT JOIN `_pages` ON `_pages`.`link`='$link' WHERE (`$table`.`promo`!='1' &&  `$table`.`stat`='1' && `$table`.`id` NOT IN (".implode(",", $notin).")) GROUP BY 1 ORDER BY `$table`.`data` DESC LIMIT 4");
		if ($data["total"]>0) { @mysql_data_seek($data["result"], 0); $tmp=@mysql_fetch_array($data["result"]); $text.="<h1><span>".$tmp["catname"]."</span></h1><div class='RDNEWS'>";
		for($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $item=@mysql_fetch_array($data["result"]); $d=ToRusData($item["data"]); 
			$d=ToRusData($item["data"]);
			$a="<a href='/".$item["link"]."/view/".$item["id"]."'>";
			$p=$a."<img src='/userfiles/picsquare/".$item["pic"]."'>"."</a>";
			$n="<span>".$a.$item["name"]."</a><br><i>".$d[1]."<br>Комментарии: ".$item["comcount"]."</i></span>";
			$text.="<div class='TvItem'>".$p.$n."</a></div>";
		} $text.="</div>"; }
	}

	return(array($text, ""));
}

?>