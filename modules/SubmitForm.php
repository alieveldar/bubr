<?
session_start(); $dir=explode("/", $_SERVER['HTTP_REFERER']); $HTTPREFERER=$dir[2];
if ($HTTPREFERER==$_SERVER['SERVER_NAME']) {
	$bp=$_GET["bp"];
	$_SESSION['Data']=$_POST;
	if($_FILES){
		foreach( $_FILES as $key => $file ){
			$filename = "file-".date("YmdHis")."-".round(rand(111,999));
			$ext=strtolower(substr($file['name'], 1+strrpos($file['name'], ".")));
			if(move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/userfiles/temp/'.$filename.'.'.$ext)) 
				$_SESSION['Files'][$key] = array('name' => $filename, 'ext' => $ext, 'size' => $file['size'], 'file' => $_SERVER['DOCUMENT_ROOT'].'/userfiles/temp/'.$filename.'.'.$ext);
		}
		
	}
	header("Location: /".$bp); exit();
}
?>