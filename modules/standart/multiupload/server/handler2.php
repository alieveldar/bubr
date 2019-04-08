<?
session_start();
if ($_SESSION['adminenter']!=''){
	$GLOBAL["sitekey"]=1;
	@require_once $_SERVER['DOCUMENT_ROOT'].'/modules/standart/DataBase.php';	
	@require_once $_SERVER['DOCUMENT_ROOT'].'/modules/standart/Settings.php';		
	@require_once 'qqFileUploader.php';		
	
	$filename = $GLOBAL["file"];
	
	if (!is_dir($ROOT.'/userfiles/files')) { mkdir($ROOT.'/userfiles/files', 0777); }
	
	$uploader = new qqFileUploader();	
	
	// Допустимые разрешения файлов
	$uploader->allowedExtensions = array('jpg', 'jpeg', 'gif', 'png', 'mp3', 'avi', 'mp4', 'zip', 'rar', 'doc', 'docx', 'xsl', 'xslx');
	
	// Максимальный размер загружаемого файла
	$uploader->sizeLimit = 200 * 1024 * 1024;
	
	$ext = $uploader->getExt();

	// Запускаем функцию загрузки с указанием папки для загрузки
	$result = $uploader->handleUpload($ROOT.'/userfiles/files', $filename.'.'.$ext);

	$result['uploadName'] = $uploader->getUploadName();
	
	header('Content-Type: text/plain');
	echo json_encode($result);	
}
?>
