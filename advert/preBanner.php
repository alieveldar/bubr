<?
$GLOBAL["sitekey"]=1; $id=(int)$_GET["id"];
$ROOT=$_SERVER['DOCUMENT_ROOT'];
@require($ROOT."/modules/standart/DataBase.php");
$table1="_banners_items";
$table2="_banners_pos";
 	
$q="SELECT `$table1`.pic, `$table1`.flash, `$table1`.`link`, `$table1`.`link2`, `$table1`.`link3`, `$table2`.`width`, `$table2`.`height` FROM `$table1` LEFT JOIN `$table2` ON $table1.pid=$table2.id WHERE (`$table1`.`id`='$id') LIMIT 1";
$data=DB($q); if ($data["total"]==0) { echo "<h1>Error: Banner not found</h1>"; } else { @mysql_data_seek($data["result"], 0); $n=@mysql_fetch_array($data["result"]);
	echo "<div style='position:absolute; text-align:center; left:50%; top:50%; width:".round($n["width"])."px; height:".round($n["height"])."px; margin-left:-".round($n["width"]/2)."px; margin-top:-".round($n["height"]/2)."px; border:1px solid #333;'>";
	if ($n["flash"]=="") {
		echo "<a href='$n[link]'><img src='/advert/files/image/$n[pic]' style=' width:".round($n["width"])."px; height:".round($n["height"])."px; border:none;'></a>";		
	} else {
		
		echo'<object type="application/x-shockwave-flash" data="/advert/files/flash/'.$n['flash'].'" width="'.$n['width'].'" height="'.$n['height'].'">';
		echo'<param name="movie" value="/advert/files/flash/'.$n['flash'].'" /><param name="quality" value="high"/><param name="wmode" value="opaque"/><param name="flashvars" value="link1='.rawurlencode($n["link"]).'&link2='.rawurlencode($n["link2"]).'&link3='.rawurlencode($n["link3"]).'">';
		echo'<embed width="'.$n['width'].'" height="'.$n['height'].'" type="application/x-shockwave-flash" quality="high" src="/advert/files/flash/'.$n['flash'].'" flashvars="link1='.rawurlencode($n["link"]).'&link2='.rawurlencode($n["link2"]).'&link3='.rawurlencode($n["link3"]).'">';
		echo'Install Flash 11.0!</object>';
		
	}
	echo "</div>";
}


?>