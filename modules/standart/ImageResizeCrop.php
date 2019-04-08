<?php
/**
 * @version 0.1
 * @author recens
 * @license GPL
 * @copyright Гельтищева Нина (http://recens.ru)
 */

/**
* Масштабирование изображения
*
* Функция работает с PNG, GIF и JPEG изображениями.
* Масштабирование возможно как с указаниями одной стороны, так и двух, в процентах или пикселях.
*
* @param string Расположение исходного файла
* @param string Расположение конечного файла
* @param integer Ширина конечного файла
* @param integer Высота конечного файла
* @param bool Размеры даны в пискелях или в процентах
* @return bool
*/

@require_once $_SERVER['DOCUMENT_ROOT'].'/modules/standart/watermark/watermark.php';
$imagick=extension_loaded("imagick");

function resize($file_input, $file_output, $w_o, $h_o) {
	global $imagick;	
	list($w_i, $h_i, $type) = getimagesize($file_input);
	if (!$w_i || !$h_i) {
		return 'Невозможно получить длину и ширину изображения';
    }
    $types = array('','gif','jpeg','png');
    $ext = strtolower($types[$type]);
    if ($ext) {
    	$func = 'imagecreatefrom'.$ext;
    	$img = $func($file_input);
    } else {
    	return 'Некорректный формат файла';
    }
	
	$x_ratio = $w_o / $w_i;
	$y_ratio = $h_o / $h_i;

	$ratio       = max($x_ratio, $y_ratio);
	$use_x_ratio = ($x_ratio == $ratio);
	
	$w_o   = $use_x_ratio  ? $w_o  : floor($w_i * $ratio);
	$h_o  = !$use_x_ratio ? $h_o : floor($h_i * $ratio);
	
	if($imagick){
		$im=new Imagick($file_input);
		$im->resizeImage($w_o, $h_o, Imagick::FILTER_LANCZOS, 1);
		$im->writeImage($file_output);
		$im->destroy();
	}
	else{
		$img_o = imagecreatetruecolor($w_o, $h_o);
		imagecopyresampled($img_o, $img, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i);
		if ($type == 2) {
			imagejpeg($img_o,$file_output,100);
		} else {
			$func = 'image'.$ext;
			$func($img_o,$file_output);
		}
	}
	if($w_o > 500) watermark($file_output, $_SERVER['SERVER_NAME']);
	return $imagick ? " [IM] : ".date("d.m.Y, H:i:s") : " [GD] : ".date("d.m.Y, H:i:s");
}

/**
* Обрезка изображения
*
* Функция работает с PNG, GIF и JPEG изображениями.
* Обрезка идёт как с указанием абсоютной длины, так и относительной (отрицательной).
*
* @param string Расположение исходного файла
* @param string Расположение конечного файла
* @param array Координаты обрезки
* @param bool Размеры даны в пискелях или в процентах
* @return bool
*/
function crop($file_input, $file_output, $crop = 'square') {
	global $imagick;
	list($w_i, $h_i, $type) = getimagesize($file_input);
	if (!$w_i || !$h_i) {
		return 'Невозможно получить длину и ширину изображения';
    }
    $types = array('','gif','jpeg','png');
    $ext = $types[$type];
    if ($ext) {
    	$func = 'imagecreatefrom'.$ext;
    	$img = $func($file_input);
    } else {
    	return 'Некорректный формат файла';
    }
	if ($crop == 'square') {
		$min = min($w_i, $h_i);
		$w_o = $h_o = $min;
		$x_o = ($w_i - $w_o) / 2;
		$y_o = ($h_i - $h_o) / 2;
	} else list($x_o, $y_o, $w_o, $h_o) = $crop;
	
	if($imagick){
		$im=new Imagick($file_input);
		$im->cropImage($w_o, $h_o, $x_o, $y_o);
		$im->writeImage($file_output);
		$im->destroy();
	}
	else{
		$img_o = imagecreatetruecolor($w_o, $h_o);
		imagecopy($img_o, $img, 0, 0, $x_o, $y_o, $w_o, $h_o);
		if ($type == 2) {
			imagejpeg($img_o,$file_output,100);
		} else {
			$func = 'image'.$ext;
			$func($img_o,$file_output);
		}
	}
	return $imagick ? " [IM] : ".date("d.m.Y, H:i:s") : " [GD] : ".date("d.m.Y, H:i:s");
}
?>