<?
### МЕНЮ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

$html=file_get_contents('../template/404.html');
// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
$P=$_POST;

if (isset($P["editbutton"])) {
	file_put_contents ('../template/404.html', stripslashes($P["text"]));
	$_SESSION["Msg"]="<div class='SuccessDiv'>Cтраница сохранена!</div>";
	@header("location: ".$_SERVER["REQUEST_URI"]); exit();
}
	
// ФОРМА ДОБАВЛЕНИЯ
	$AdminText='<h2>Редактирование страницы ошибки 404 (Страница не найдена)</h2>'.$_SESSION["Msg"];
	
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";
	$AdminText.="<div class='RoundText' id='Tgg'><div class='C5'></div>
	<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/filemanager/ajex.js'></script>
	<textarea name='text' id='textedit' style='outline:none;'>".$html."</textarea>
	<script type='text/javascript'>var editor=CKEDITOR.replace('textedit'); AjexFileManager.init({ returnTo: 'ckeditor', editor: editor});</script>
	<div class='C15'></div><div class='CenterText'><input type='submit' name='editbutton' id='editbutton' class='SaveButton' value='Сохранить страницу'></div></div></form>";

// ПРАВАЯ КОЛОНКА
	$AdminRight='';
	
}
$_SESSION["Msg"]="";

?>