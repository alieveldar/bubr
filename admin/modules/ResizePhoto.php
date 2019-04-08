<?php
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	@require($ROOT."/modules/standart/ImageResizeCrop.php");
	$imagick=extension_loaded("imagick");
	$msg="Настройки уcпешно сохранены"; 
	$xy=array(); $i=0; $tmp=explode(";", $picxy); foreach ($tmp as $val) { $tmp2=explode("=", $val); $xy[$tmp2[0]]=explode(",", $tmp2[1]); }
	
		# Обработка фотографий под все размеры
		foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) {
		if (is_array($xy[$path]) && !in_array($path, array('picoriginal', 'picpreview'))) {
				
			$cs=$xy[$path]; if (!is_dir($ROOT."/userfiles/".$path)) { mkdir($ROOT."/userfiles/".$path, 0777); }
			list($w,$h)=getimagesize($ROOT."/userfiles/picoriginal/".$picname); list($sw, $sh)=explode("-", $size); 
			
			$type = crop($ROOT."/userfiles/picoriginal/".$picname, $ROOT."/userfiles/".$path."/".$picname, array($cs[0], $cs[1], $cs[2] - $cs[0], $cs[3] - $cs[1]));
			$type = resize($ROOT."/userfiles/".$path."/".$picname, $ROOT."/userfiles/".$path."/".$picname, $sw, $sh);
			
			if ((int)$PPEC==1 && $imagick) {	
				$water=$ROOT."/admin/modules/play.png"; list($pw, $ph)=getimagesize($water); $px=round(($sw-$pw)/2); $py=round(($sh-$ph)/2); $watermark = imagecreatefrompng($water);
				$img_file=$ROOT."/userfiles/".$path."/".$picname; $img = imagecreatefromjpeg($img_file); imagecopy($img, $watermark, $px, $py, 0, 0, $pw, $ph);
				if ($ext == "jpg" || $ext == "jpeg") { imagejpeg($img, $img_file, 100);	} else { $func = 'image'.$ext; $func($img, $img_file); }
			}
			
		}





		}
		$msg.= $type;
} else { $msg="Ошибка сервера. Отказано в доступе!"; }
?>
