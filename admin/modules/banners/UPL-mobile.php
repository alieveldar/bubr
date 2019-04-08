<?php
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	
$loaded=0; $max_image_size=10000; $valid_types=array("jpg", "gif", "png"); $msg="Пробуем загрузить..."; $userfile=$_FILES['usermobile']['tmp_name']; $error=1;
$ext=str_replace("jpeg", "jpg", strtolower(substr($_FILES['usermobile']['name'], 1+strrpos($_FILES['usermobile']['name'], ".")))); $vmobile=date("Y.m.d")."-mobile-".$P["zay"]."-".rand(1111, 9999).".".$ext;
	
if (filesize($userfile)>($max_image_size*1024)) { $msg="Файл больше $max_image_size килобайт"; } 
	elseif (!in_array($ext, $valid_types)) { $msg="Файл не является форматом ".implode(", ", $valid_types); } else {
		if (@move_uploaded_file($_FILES['usermobile']['tmp_name'], $ROOT."/advert/files/mobile/".$vmobile)) {
			$msg=""; $error=0; # Все загрузилось
		} else { $msg="Ошибка сервера. Свяжитесь с администратором!"; }
}
	
} else { $msg="Ошибка сервера. Отказано в доступе!"; }
if ($msg!="") { $_SESSION["Msg"]="<div class='ErrorDiv'>$msg</div>"; }
?>