<?
session_start(); 
if ($_SESSION['userid']!=0) { 
	
	$GLOBAL["sitekey"]=1;
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/Settings.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/JsRequest.php";
	@require $_SERVER['DOCUMENT_ROOT']."/modules/standart/ImageResizeCrop.php";
	$JsHttpRequest=new JsHttpRequest("utf-8"); $ROOT=$_SERVER['DOCUMENT_ROOT'];

	// полученные данные ================================================
	
	$R = $_REQUEST;
	$path=$_SERVER['DOCUMENT_ROOT']."/userfiles/picoriginal/";
	
	//=================================================================================================================================================================================================

	$max_image_w=10000; $max_image_h=10000; $max_image_s=10000; $msgre="loaded"; $neW=100; $neH=100; $valid_types=array("gif","jpg", "png", "jpeg"); $npicname=$_FILES['userpic']['tmp_name'];
	$ext=substr($_FILES['userpic']['name'], 1+strrpos($_FILES['userpic']['name'], ".")); $ext=strtolower($ext); $picname=$GLOBAL["pic"].".".$ext;
	if (filesize($npicname) > ($max_image_s*1024)) { $msgre="Файл больше $max_image_s килобайт"; } elseif (!in_array($ext, $valid_types)) { $msgre="Файл не является форматом gif, jpg или png!"; } else { $size=getimagesize($npicname);
	if ($size[0]<$max_image_w && $size[1]<$max_image_h) { if (move_uploaded_file($_FILES['userpic']['tmp_name'], $path.$picname)) { $msgre="loaded";
		foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) {			
			if (!is_dir($ROOT."/userfiles/".$path)) { mkdir($ROOT."/userfiles/".$path, 0777); }
			list($w,$h)=getimagesize($ROOT."/userfiles/picoriginal/".$picname); list($sw, $sh)=explode("-", $size);
			if ($path!="picoriginal") {
				if ($path=="picpreview") resize($ROOT."/userfiles/picoriginal/".$picname, $ROOT."/userfiles/".$path."/".$picname, $sw, $sh);
				else{					
					$k = min($w / $sw, $h / $sh);
					$x = round(($w - $sw * $k) / 2); $y = round(($h - $sh * $k) / 2);
					$type = crop($ROOT."/userfiles/picoriginal/".$picname, $ROOT."/userfiles/".$path."/".$picname, array($x, $y, round($sw * $k), round($sh * $k)));
					$type = resize($ROOT."/userfiles/".$path."/".$picname, $ROOT."/userfiles/".$path."/".$picname, $sw, $sh);
					$picxy.=$path."=".$x.",".$y.",".round($sw * $k + $x).",".round($sh * $k + $y).";";				
				}			
			}
		}
	} else { $msgre="Ошибка сервера. Свяжитесь с администратором!"; }} else { $msgre="Картинка больше, чем $max_image_w на $max_image_h пикселей!"; }}
} else { $msgre="error user"; }
// отправляемые данные ==============================================
if ($msgre=="loaded") { $result["Answer"]="ok"; $result["Pic"]=$picname."?".time(); $result["file"]=$picname; $result["picxy"]=trim($picxy, ";"); } else { $result["Answer"]=$msgre; } $GLOBALS['_RESULT']=$result;
?>
