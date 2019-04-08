<?php
function demotivator($inputFile, $outputFile, $title, $text, $author, $copyright) {

	list($x, $y, $type)=getimagesize($inputFile);
	
	$types=array('','gif','jpeg','png');
	$ext=$types[$type];
	if ($ext) {
		$func='imagecreatefrom'.$ext;
		$img=$func($inputFile);
	}
	
	$x_o=$x;
	$y_o=$y;
	$max_w=600;
	
	if ($x != $max_w) {		
		$y_o=$max_w/($x/$y);
		$x_o=$max_w;
	}
	
	// Размеры текстов
	$title_s=36;
	$text_s=20;
	$author_s=12;
	
	// Шрифты
	$times='modules/demots/fonts/times.ttf';
	$arial='modules/demots/fonts/arial.ttf';
	
	// Размер черного прямоугольника, который будем рисовать
	$bord=50;
	$dem_w=$x_o + $bord * 2;
	$dem_h=$y_o + $bord + $bord * 3;
	
	// Создаем новое изображение
	$img2=ImageCreateTrueColor($dem_w, $dem_h);
	
	// Цвета
	$black=ImageColorAllocate($img, 0, 0, 0);
	$white=ImageColorAllocate($img2, 255, 255, 255);
	$grey=ImageColorAllocate($img2, 123, 123, 123);
	
	// Наносим изображение
	ImageCopyResized($img2, $img, $bord, $bord, 0, 0, $x_o, $y_o, $x, $y);
	
	// Наносим белую рамку
	ImageRectangle($img2, $bord - 5, $bord - 5, $x_o + $bord + 4, $y_o + $bord + 4, $white);
	ImageRectangle($img2, $bord - 6, $bord - 6, $x_o + $bord + 5, $y_o + $bord + 5, $white);
	
	$text_end=$y_o + $bord * 2 + 10;
	// Наносим название
	$dx1=0;
	$dy1=$text_end;
	$t1=ImageTTFText($img2, $title_s, 0, $dx1, $dy1, $white, $times, $title);
	while(!$dx1){
		ImageFilledRectangle($img2, 0, $dy1 - $title_s, $dem_w, $dem_h, $black);	
		if($t1[2] < $dem_w - 30) {
			$dx1=($dem_w - $t1[2]) / 2;		
			$t1=ImageTTFText($img2, $title_s, 0, $dx1, $dy1, $white, $times, $title);
			$text_end=$t1[1];
		}
		elseif($title_s > 22) {
			$title_s -= 2;
			$t1=ImageTTFText($img2, $title_s, 0, $dx1, $dy1, $white, $times, $title);
		}
		else{
			$rows=partitionByRows($title);	
			$t1_1=ImageTTFText($img2, $title_s, 0, $dx1, $dy1, $white, $times, $rows[0]);
			$t1_2=ImageTTFText($img2, $title_s, 0, $dx1, $dy1, $white, $times, $rows[1]);
			$dx1_1=($dem_w - $t1_1[2]) / 2; $dy1_1=$dy1 - $title_s / 2;
			$dx1_2=($dem_w - $t1_2[2]) / 2; $dy1_2=$dy1 + $title_s;
			ImageFilledRectangle($img2, 0, $dy1 - $title_s, $dem_w, $dem_h, $black);
			$t1_1=ImageTTFText($img2, $title_s, 0, $dx1_1, $dy1_1, $white, $times, $rows[0]);
			$t1_2=ImageTTFText($img2, $title_s, 0, $dx1_2, $dy1_2, $white, $times, $rows[1]);
			$dx1=1;
			$text_end=$t1_2[1];
		}
	}
	
	// Наносим описание
	$dx2=0;
	$dy2=$text_end + $text_s + 15;
	$t2=ImageTTFText($img2, $text_s, 0, $dx2, $dy2, $white, $arial, $text);
	while(!$dx2){
		ImageFilledRectangle($img2, 0, $dy2 - $text_s, $dem_w, $dem_h, $black);	
		if($t2[2] < $dem_w - 30) {
			$dx2=($dem_w - $t2[2]) / 2;		
			$t2=ImageTTFText($img2, $text_s, 0, $dx2, $dy2, $white, $arial, $text);
		}
		elseif($text_s > 14) {
			$text_s -= 2;
			$t2=ImageTTFText($img2, $text_s, 0, $dx2, $dy2, $white, $arial, $text);
		}
		else{
			$rows=partitionByRows($text);	
			$t2_1=ImageTTFText($img2, $text_s, 0, $dx2, $dy2, $white, $arial, $rows[0]);
			$t2_2=ImageTTFText($img2, $text_s, 0, $dx2, $dy2, $white, $arial, $rows[1]);
			$dx2_1=($dem_w - $t2_1[2]) / 2; $dy2_1=$dy2 - $text_s / 2;
			$dx2_2=($dem_w - $t2_2[2]) / 2; $dy2_2=$dy2 + $text_s;
			ImageFilledRectangle($img2, 0, $dy2 - $text_s, $dem_w, $dem_h, $black);
			$t2_1=ImageTTFText($img2, $text_s, 0, $dx2_1, $dy2_1, $white, $arial, $rows[0]);
			$t2_2=ImageTTFText($img2, $text_s, 0, $dx2_2, $dy2_2, $white, $arial, $rows[1]);
			$dx2=1;
		}
	}
	
	// Наносим копирайт
	$dx3=0;
	$dy3=$dem_h - $bord/5;
	$t3=ImageTTFText($img2, $author_s, 0, $dx3, $dy3, $grey, $arial, $copyright);
	ImageFilledRectangle($img2, 0, $dy3 - $author_s, $dem_w, $dem_h, $black);
	ImageTTFText($img2, $author_s, 0, $dem_w - $t3[2] - $bord/5, $dy3, $grey, $arial, $copyright);
	
	// Наносим автора
	ImageTTFText($img2, $author_s, 0, $bord/5, $dy3, $grey, $arial, $author);
	
	ImageJpeg($img2, $outputFile, 80);
	ImageDestroy($img2);
}


function partitionByRows($string){
	$words=explode(' ', $string);
	$rows=array('', '');
	foreach ($words as $key => $value) {
		if(mb_strlen($rows[0]) < ceil(mb_strlen($string) / 2)) $rows[0] .= $value.' ';
		else $rows[1] .= $value.' ';
	}
	return $rows;
}
?>