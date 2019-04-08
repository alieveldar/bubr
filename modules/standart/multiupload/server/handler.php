<?
session_start();
if ($_SESSION['userid']!=''){
	$GLOBAL["sitekey"]=1;	
	@require_once $_SERVER['DOCUMENT_ROOT'].'/modules/standart/DataBase.php';
	@require_once $_SERVER['DOCUMENT_ROOT'].'/modules/standart/Settings.php';
	@require_once $_SERVER['DOCUMENT_ROOT'].'/modules/standart/ImageResizeCrop.php';		
	@require_once 'qqFileUploader.php';		
	
	$filename = $GLOBAL["pic"];
	$table = $_REQUEST['table'] ? $_REQUEST['table'] : '_widget_pics';
	$field = $_REQUEST['field'] ? $_REQUEST['field'] : 'pic';
	$pid = $_REQUEST['pid'] ? $_REQUEST['pid'] : 0;
	$point = $_REQUEST['point'] ? $_REQUEST['point'] : "";
	$link = $_REQUEST['link'];
	
	if (!is_dir($ROOT.'/userfiles/temp')) { mkdir($ROOT.'/userfiles/temp', 0777); }
	
	$uploader = new qqFileUploader();	
	
	// Допустимые разрешения файлов
	$uploader->allowedExtensions = array('jpg','jpeg', 'gif', 'png', 'mp3', 'avi', 'mp4');
	
	// Максимальный размер загружаемого файла
	$uploader->sizeLimit = 200 * 1024 * 1024;
	
	$ext = $uploader->getExt();

	// Запускаем функцию загрузки с указанием папки для загрузки
	$result = $uploader->handleUpload($ROOT.'/userfiles/temp', $filename.'.'.$ext);
	
	$picname = $uploader->getUploadName();
	
	if($result['success']){		
		# Обработка фотографий под все размеры
		foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) {			
			if (!is_dir($ROOT."/userfiles/".$path)) { mkdir($ROOT."/userfiles/".$path, 0777); }
			list($w,$h)=getimagesize($ROOT."/userfiles/temp/".$picname);
			list($sw, $sh)=explode("-", $size);
			
			if($path=="picpreview") resize($ROOT."/userfiles/temp/".$picname, $ROOT."/userfiles/".$path."/".$picname, $sw, $sh);
			else if($path=="picoriginal"){
				if($w > $sw) resize($ROOT."/userfiles/temp/".$picname, $ROOT."/userfiles/".$path."/".$picname, $sw, $sh);
				else copy($ROOT."/userfiles/temp/".$picname, $ROOT."/userfiles/".$path."/".$picname);
			}
			else{					
				$k = min($w / $sw, $h / $sh);
				$x = round(($w - $sw * $k) / 2); $y = round(($h - $sh * $k) / 2);
				crop($ROOT."/userfiles/temp/".$picname, $ROOT."/userfiles/".$path."/".$picname, array($x, $y, round($sw * $k), round($sh * $k)));
				resize($ROOT."/userfiles/".$path."/".$picname, $ROOT."/userfiles/".$path."/".$picname, $sw, $sh);		
			}
		}
		$msg.= $type; $picxy=trim($picxy, ";");
		if($_REQUEST['widget_pic']){
			$res=DB("INSERT INTO `".$table."` (`pid`,`$field`,`link`, `point`) VALUES ($pid, '$picname', '$link', '$point')");
			$rate = DBL(); 
			DB("UPDATE `".$table."` SET `rate`=$rate WHERE `id`=$rate"); 
			DB("UPDATE `".$table."` SET `data`='".time()."' WHERE `id`=$rate");
			DB("UPDATE `".$table."` SET `picxy`='$picxy' WHERE `id`=$rate");
		}
		else{
			$res=DB("INSERT INTO `".$table."` (`pid`, `pic`, `data`) VALUES ($pid, '$picname', '".time()."')");
			$rate = DBL(); 
			DB("UPDATE `".$table."` SET `rate`=$rate WHERE `id`=$rate");
			DB("UPDATE `".$table."` SET `uid`=".$_SESSION['userid']." WHERE `id`=$rate");  
		}
	}
	
	$result['uploadName'] = $picname;
	
	header('Content-Type: text/plain');
	echo json_encode($result);	
}
?>
