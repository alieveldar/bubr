<?php
function watermark($img_file) {
	$offset = 10;
	return; 
	
	$watermark_file = $_SERVER['DOCUMENT_ROOT'].'/modules/standart/watermark/logo.png';
	$font = $_SERVER['DOCUMENT_ROOT'].'/modules/standart/watermark/KomikaTitle.ttf';
		
	list($w_i, $h_i, $type) = getimagesize($img_file);
	if (!$w_i || !$h_i) {
		return 'Невозможно получить длину и ширину изображения';
    }
    $types = array('','gif','jpeg','png');
    $ext = strtolower($types[$type]);
	if ($ext) {
    	$func = 'imagecreatefrom'.$ext;
    	$img = $func($img_file);
    } else {
    	return 'Некорректный формат файла';
    }
	
	$watermark = imagecreatefrompng($watermark_file);
	list($sx, $sy, $stype) = getimagesize($watermark_file);
	
	imagecopy($img, $watermark, imagesx($img) - $sx - $offset, imagesy($img) - $sy - $offset, 0, 0, $sx, $sy);
	
	/*$black = imagecolorallocate($img, 0, 0, 0);
	
	$c = imagecolorallocatealpha($img, 255, 255, 255, 50);
	$size = $h_i / 15;
	$box  = imagettfbbox ($size, 0, $font, $watermark);
	$x = $w_i - abs($box[4] - $box[0]) - $offset;
	$y = $h_i - $offset;
	
	imagettftext($img, $size, $angle, $x, $y, $c, $font, $watermark);
	 * 
	 */
	
	if ($type == 2) {
		imagejpeg($img, $img_file, 100);
	} else {
		$func = 'image'.$ext;
		$func($img, $img_file);
	}
}
?>