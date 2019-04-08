<?
session_start(); $result["Code"]=0; $cache=3; $now=time();
$dir=explode("/", $_SERVER['HTTP_REFERER']); $HTTPREFERER=$dir[2];
//if ($HTTPREFERER==$_SERVER['HTTP_HOST']) {
	
	$GLOBAL["sitekey"]=1; $text=""; $file=""; $all=array(); $ban=array(); $rate=array(); $setka=array();
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php"; $JsHttpRequest=new JsHttpRequest("utf-8");
	
	# id; pid; did; prior; flash; pic; mobile; link; link2; link3; w; h;outer;  text
	// полученные данные ==================================================================
	$R = $_REQUEST;
	$d=(int)$R["domain"];
	
	/* КЭШ */
	$path=$_SERVER['DOCUMENT_ROOT']."/advert/cache/domain-".$d.".dat";
	if (is_file($path) && (time()-filemtime($path))<$cache) { $tmp=explode("<|>", @file_get_contents($path)); 
		foreach ($tmp as $item) { if ($item!="") { list($pid, $data)=explode("<===>", $item); $text[$pid]=explode("|", $data); }} 
		
	} else {
		// загружаем список баннеров ==========================================================
		if (is_file($_SERVER['DOCUMENT_ROOT']."/advert/domains/domain-9999.dat")) { $file.=@file_get_contents($_SERVER['DOCUMENT_ROOT']."/advert/domains/domain-9999.dat"); }
		if (is_file($_SERVER['DOCUMENT_ROOT']."/advert/domains/domain-".$d.".dat")) { $file.=@file_get_contents($_SERVER['DOCUMENT_ROOT']."/advert/domains/domain-".$d.".dat"); }
		$all=explode("<|>", $file); foreach ($all as $item) { if ($item!="") { $tmp=explode(";", $item); $ban[$tmp[1]][$tmp[0]]=$item; $rate[$tmp[0]]=$tmp[3]; }} 	
		// создаем список баннеров по каждому региону, согласно приоритету =====================
		foreach ($ban as $pid=>$items) { $text[$pid]=getSetka($items); $cachetext.=$pid."<===>".implode("|", $text[$pid])."<|>"; } @file_put_contents($path, $cachetext);
	}
	// отдаем список баннеров ==============================================================
	$result["Code"]=1;
	$result["Banners"]=$text;
	$result["log"]=$text;
	
//}
// отправляемые данные =====================================================================
$GLOBALS['_RESULT']	= $result;	

function getSetka($items) { global $rate; $tmp=array(); $red=array(); $have=array(); $x=0; foreach ($items as $key=>$data) { for ($i=0; $i<$rate[$key]; $i++) { $tmp[]=$data; }} ### Создали сетку со всеми баннерами * приоритет
while ($x<count($items)) { $rnd=rand(0, count($tmp)-1); $p=explode(";", $tmp[$rnd]); $id=$p[0]; if (!in_array($id, $have)) { $x++; $have[]=$id; $red[]=$items[$id]; }} return $red; }
?>