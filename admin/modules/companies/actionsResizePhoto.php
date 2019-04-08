<?
function actionsResizePhoto($picname){ 
	global $GLOBAL,	$ROOT;
	@require($ROOT."/modules/standart/ImageResizeCrop.php");
	
	foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) {			
		if (!is_dir($ROOT."/userfiles/".$path)) { mkdir($ROOT."/userfiles/".$path, 0777); }
		list($w,$h)=getimagesize($ROOT."/userfiles/temp/".$picname);
		list($sw, $sh)=explode("-", $size);
		
		if ($path!="picoriginal") {
			if($path=="picpreview") resize($ROOT."/userfiles/temp/".$picname, $ROOT."/userfiles/".$path."/".$picname, $sw, $sh);
			else{					
				$k = min($w / $sw, $h / $sh);
				$x = round(($w - $sw * $k) / 2); $y = round(($h - $sh * $k) / 2);
				crop($ROOT."/userfiles/temp/".$picname, $ROOT."/userfiles/".$path."/".$picname, array($x, $y, round($sw * $k), round($sh * $k)));
				resize($ROOT."/userfiles/".$path."/".$picname, $ROOT."/userfiles/".$path."/".$picname, $sw, $sh);			
			}			
		}
	}
}
?>