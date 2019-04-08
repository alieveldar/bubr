<?
	$GLOBAL["sitekey"]=1;
	@require_once $_SERVER['DOCUMENT_ROOT'].'/modules/standart/DataBase.php';	
	@require_once $_SERVER['DOCUMENT_ROOT'].'/modules/standart/Settings.php';
	@require_once $_SERVER['DOCUMENT_ROOT'].'/modules/standart/ImageResizeCrop.php';
	@require_once $_SERVER['DOCUMENT_ROOT'].'/modules/standart/watermark/watermark.php';		
	@require_once 'qqFileUploader.php';		
	
	$ROOT=$_SERVER['DOCUMENT_ROOT'];
	$filename = $GLOBAL["pic"];
	
	if (!is_dir($ROOT.'/userfiles/temp')) { mkdir($ROOT.'/userfiles/temp', 0777); }
	
	$uploader = new qqFileUploader();	
	
	// Допустимые разрешения файлов
	$uploader->allowedExtensions = array('jpg', 'jpeg', 'gif', 'png');
	
	// Максимальный размер загружаемого файла
	$uploader->sizeLimit = 10 * 1024 * 1024;
	
	$ext = $uploader->getExt();

	// Запускаем функцию загрузки с указанием папки для загрузки
	$result = $uploader->handleUpload($ROOT.'/userfiles/temp', $filename.'.'.$ext);
	
	if($result['success']){
		$result['uploader']="handler4";
		$result['uploadName'] = $uploader->getUploadName(); $result['picoriginal'] = $VARS["picoriginal"];
		$src = $ROOT.'/userfiles/temp/'.$result['uploadName']; list($w,$h)=getimagesize($src); $k = $w/$h;
		if($w > $VARS["picoriginal"]){ $sw = $VARS["picoriginal"]; $sh = $sw/$k; resize($src, $src, $sw, $sh); }
		
		$picname=$result['uploadName']; $result['picname']=$ROOT."/userfiles/temp/".$picname;
		list($w, $h)=getimagesize($ROOT."/userfiles/temp/".$picname); 
		$sw=300; $sh=300; $k = min($w / $sw, $h / $sh);
		$x = round(($w - $sw * $k) / 2); $y = round(($h - $sh * $k) / 2);
		$result['picname']=$ROOT."/userfiles/temp/".$picname."(original: ".$w."x".$h." -> ".$sw."x".$sh.")";
		crop($ROOT."/userfiles/temp/".$picname, $ROOT."/userfiles/temp/s-".$picname, array($x, $y, round($sw * $k), round($sh * $k)));
		resize($ROOT."/userfiles/temp/s-".$picname, $ROOT."/userfiles/temp/s-".$picname, $sw, $sh);
	}
	
	
	header('Content-Type: text/plain');
	echo json_encode($result);	
?>
