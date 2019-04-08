<?	
$file="_index-indexmobilepage"; if (RetCache($file)=="true") { list($Page["Content"], $cap)=GetCache($file, 0); } else { list($Page["Content"], $cap)=CreateIndexPage(); SetCache($file, $Page["Content"]); } $Page["Caption"]=$VARS["sitename"];	

function CreateIndexPage() {
	global $VARS, $GLOBAL, $dir, $Page, $node, $UserSetsSite, $C, $C20, $C10, $C25; $text=''; $notin=array(0);
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
		### Остальные фотки - обычные записи
		$text.="<div class='IndexLentaLists' id='IndexLentaLists'>"; $old=0; $table="post_lenta"; $limit=20;
			$data=DB("SELECT `name`,`lname`,`pic`,`data`,`id` FROM `".$table."` WHERE (`stat`=1) GROUP BY 1 ORDER BY `data` DESC LIMIT ".$limit);
			for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]);	$old=$ar["data"];
			if ($ar["pic"]!="") { $pic="<img src='/userfiles/picmiddle/".$ar["pic"]."' title='".$ar["name"]."' />"; } else { $pic=""; }
			$text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaList' id='NewsLentaList-".$ar["id"]."'>".$pic."<capt><b>".GetSpanCaption($ar["name"])."</b><br>".GetSpanCaption($ar["lname"])."</capt></a>"; if (($i+1)%5==0) { $text.=$C; } endfor;
		$text.="</div>".$C;
		if ($data["total"]==$limit) { $text.="<div id='LoadMore'><a href='javascript:void(0);' onclick=\"IndexLoadMore('$old','$limit');\">Показать ещё</a></div>"; }
		
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	return(array($text, ""));
}
?>
