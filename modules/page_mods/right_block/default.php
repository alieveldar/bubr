<?	
if ($link=="post" && $start=="cat") {
	$file="_rightblock-tags"; if (RetCache($file, "cacheblock")=="true") { list($Page["RightContent"], $cap)=GetCache($file, 0); } else { list($Page["RightContent"], $cap)=CreateRightBlockTags(); SetCache($file, $Page["RightContent"], "", "cacheblock"); }
} else {
	$file="_rightblock-default"; if (RetCache($file, "cacheblock")=="true") { list($Page["RightContent"], $cap)=GetCache($file, 0); } else { list($Page["RightContent"], $cap)=CreateRightBlock(); SetCache($file, $Page["RightContent"], "", "cacheblock"); }	
}
function CreateRightBlock() {
	global $Domains, $SubDomain, $GLOBAL, $C20, $C25, $C15, $C, $C10; 
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	
	$text.='<div id="Banner-2-1"></div>';
$text.='<!-- Yandex.RTB R-A-334678-3 -->
<div id="yandex_rtb_R-A-334678-3"></div>
<script type="text/javascript">
    (function(w, d, n, s, t) {
        w[n] = w[n] || [];
        w[n].push(function() {
            Ya.Context.AdvManager.render({
                blockId: "R-A-334678-3",
                renderTo: "yandex_rtb_R-A-334678-3",
                async: true
            });
        });
        t = d.getElementsByTagName("script")[0];
        s = d.createElement("script");
        s.type = "text/javascript";
        s.src = "//an.yandex.ru/system/context.js";
        s.async = true;
        t.parentNode.insertBefore(s, t);
    })(this, this.document, "yandexContextAsyncCallbacks");
</script>';	
	$text.='<h2>Бабр любит:</h2>';
	$data=DB("SELECT `id`,`name`,`lname`,`pic`,`data` FROM `post_lenta` WHERE (`stat`=1 && `gis`=1) ORDER BY `data` DESC LIMIT 12");
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]);
		$text.="<a href='/post/view/$ar[id]' class='RightItemBlock'><img src='/userfiles/picright/$ar[pic]' /><span>".nl2br($ar["name"])."<br><i>".nl2br($ar["lname"])."</i></span></a>".$C15;	

		if (($i+1)/3==1) { ### 1 - Вклинивания в ленту картинок
			$text.=$C20.'<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script><!-- Bubr.ru - 300x250 --><ins class="adsbygoogle" style="display:inline-block;width:300px;height:250px" data-ad-client="ca-pub-2073806235209608" data-ad-slot="7979061413"></ins><script>(adsbygoogle = window.adsbygoogle || []).push({});</script>'.$C25;
			$text.=GetVKGroup(300,250).$C10;
		} 
		if (($i+1)/3==2) { $text.=$C25; } ### 2 - Вклинивания в ленту картинок
		if (($i+1)/3==3) { $text.=$C25; } ### 3 - Вклинивания в ленту картинок

	endfor; 

	$text.=$C10.'<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script><!-- Bubr.ru - 300x250 --><ins class="adsbygoogle" style="display:inline-block;width:300px;height:250px" data-ad-client="ca-pub-2073806235209608" data-ad-slot="7979061413"></ins><script>(adsbygoogle = window.adsbygoogle || []).push({});</script>'.$C25;

	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	return(array($text, ""));
}

function CreateRightBlockTags() {
	global $Domains, $SubDomain, $GLOBAL, $C20, $C25, $C15, $C; 
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	$text='<h2>Бабротэги:</h2>';
	$data=DB("SELECT `id`,`name`,`stat` FROM `_tags` ORDER BY rand()");
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]);
		
		$text.="<a href='/tags/$ar[id]' class='TagsList TagSize".$ar["stat"]."'>".$ar["name"]."</a>";
	
		if (($i+1)/10==1) { $text.=$C5.$C20; } ### 1 - Вклинивания в ленту картинок 
		if (($i+1)/10==2) { $text.=$C5.$C20; } ### 2 - Вклинивания в ленту картинок
		if (($i+1)/10==3) { $text.=$C5.$C20; } ### 3 - Вклинивания в ленту картинок
	endfor; 
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	return(array($text, ""));
}
?>