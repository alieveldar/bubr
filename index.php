<?
	session_start(); $HardCacheFile=""; $HardCacheTime=900; mb_internal_encoding("UTF-8");
	if (!$_SESSION["mobile"] || $_SESSION["mobile"]=="") { $_SESSION["mobile"]="auto"; } mb_internal_encoding("UTF-8");
	
	$iphone = mb_strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
	$android = mb_strpos($_SERVER['HTTP_USER_AGENT'],"Android");
	$palmpre = mb_strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
	$berry = mb_strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
	$ipod = mb_strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
	$ipad = mb_strpos($_SERVER['HTTP_USER_AGENT'],"iPad");
	$mobile = mb_strpos($_SERVER['HTTP_USER_AGENT'],"Mobile");
	$symb = mb_strpos($_SERVER['HTTP_USER_AGENT'],"Symbian");
	$operam = mb_strpos($_SERVER['HTTP_USER_AGENT'],"Opera M");
	$htc = mb_strpos($_SERVER['HTTP_USER_AGENT'],"HTC_");
	$fennec = mb_strpos($_SERVER['HTTP_USER_AGENT'],"Fennec/");
	$winphone = mb_strpos($_SERVER['HTTP_USER_AGENT'],"WindowsPhone");
	$wp7 = mb_strpos($_SERVER['HTTP_USER_AGENT'],"WP7");
	$wp8 = mb_strpos($_SERVER['HTTP_USER_AGENT'],"WP8");

	

		 	
	if (!preg_match('/^m\./', $_SERVER['HTTP_HOST']) && !$_SESSION["full"]) {
		if ($ipad) { $GLOBAL["design"]="mainpage"; } else { 
			if ($iphone || $android || $palmpre || $ipod || $berry || $mobile || $symb || $operam || $htc || $fennec || $winphone || $wp7 || $wp8 === true) { $r=explode(".", $_SERVER['HTTP_HOST']); $t=count($r); $domain=trim("http://m.".$r[$t-2].".".$r[$t-1]."/".$_SERVER['REQUEST_URI'], "/"); @header('Location: '.$domain); exit(); }
		}
	}

	if(preg_match('/^m\./', $_SERVER['HTTP_HOST'])) { unset($_SESSION["full"]); $GLOBAL["design"]="mobile"; @require ("modules/mobile.MainModule.php"); } else { @require ("modules/MainModule.php"); } 
	
### Жесткий кэш ### Для неавторизованных ### hardcache ### hardcache### hardcache### hardcache### hardcache### hardcache### hardcache### hardcache### hardcache
function HardCacheSite() { global $HardCacheFile, $HardCacheTime;
	$hdir=explode("/", trim($_SERVER["REQUEST_URI"], "/")); if ($hdir[0]=="") { $hdir[0]="index"; $HardCacheTime=300; }
	$hcacheFolder = $_SERVER['DOCUMENT_ROOT']."/cache_hard"; if (!is_dir($hcacheFolder)) { mkdir($hcacheFolder, 0777); }
	$HardCacheFile=$hcacheFolder."/".$hdir[0]."/".(str_replace(array("/","&","?","http:"), "-", trim($_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"], "/"))).".cache";
	if ((int)$_SESSION['userid']==0 && strpos($_SERVER["REQUEST_URI"], ".")===false) {
		if (!is_dir($hcacheFolder."/".$hdir[0])) { mkdir($hcacheFolder."/".$hdir[0], 0777); } 
		if (is_file($HardCacheFile)) { $ftime=filemtime($HardCacheFile); $now=time(); if (($now-$ftime)<$HardCacheTime) { @header ("HTTP/1.0 200 Ok"); echo @file_get_contents($HardCacheFile)."<!-- HARDCACHE: $HardCacheFile -->"; exit(); }}
	}
}
### Жесткий кэш ### Для неавторизованных ### hardcache ### hardcache### hardcache### hardcache### hardcache### hardcache### hardcache### hardcache### hardcche			
?>