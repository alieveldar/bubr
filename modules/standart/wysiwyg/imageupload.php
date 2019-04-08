<?php
$ROOT=$_SERVER["DOCUMENT_ROOT"];
$dir=$ROOT.'/userfiles/wysiwygimages'; if (!is_dir($dir)) { mkdir($dir); chmod($dir, 0777); } 
$dir=$ROOT.'/userfiles/wysiwygimages/'.date("Ym"); if (!is_dir($dir)) { mkdir($dir); chmod($dir, 0777); }

$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
if ($_FILES['file']['type']=='image/png' || $_FILES['file']['type']=='image/jpg' || $_FILES['file']['type']=='image/gif' || $_FILES['file']['type']=='image/jpeg' || $_FILES['file']['type']=='image/pjpeg') 
{
	$ext=str_replace("jpeg", "jpg", strtolower(substr($_FILES['file']['name'], 1+strrpos($_FILES['file']['name'], "."))));
    $filename = date('YmdHis')."-".rand(10000, 99999).'.'.$ext; $file = $dir."/".$filename;
    @copy($_FILES['file']['tmp_name'], $file); list($w,$h)=getimagesize($file); $res=0; 
	if ($w>1000 && $w>=$h) { $res=1; $k=$w/1000; $w=1000; $h=round($h/$k); }
	if ($h>1000 && $h>$w) { $res=1; $k=$h/600; $h=600; $w=round($w/$k); }
	/* Обработка файла */ if ($res==1) { @require($ROOT."/modules/standart/ImageResizeCrop.php"); $type = resize($file, $file, $w, $h); }
   	/* Отправка файла */ $array = array('filelink' => '/userfiles/wysiwygimages/'.date("Ym")."/".$filename); echo stripslashes(json_encode($array));   
}
?>