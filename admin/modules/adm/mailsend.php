<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	@require_once $_SERVER['DOCUMENT_ROOT'].'/modules/standart/MailSend.php';
	
	$P=$_POST;
	if (isset($P["sendbutton"])) {		
		if( MailSend($P["to"], $P["subject"], $P["text"], $VARS["sitemail"], $P["attachment"], '/userfiles/files/') ) 
			$_SESSION["Msg"]="<div class='SuccessDiv'>Письмо отправлено!</div>";
		else $_SESSION["Msg"]="<div class='ErrorDiv'>Письмо не отправлено! Ошибка сервера</div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

	// ЭЛЕМЕНТЫ
	$AdminText.='<h2>Отправка сообщений по E-mail</h2>'.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете отправить сообщение на любой E-mail</div>";
	 
	$AdminText.='<div class="RoundText" id="Tgg"><form action="'.$_SERVER["REQUEST_URI"].'" enctype="multipart/form-data" method="post"><table>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Кому (E-mail)<star>*</star></td><td class="LongInput"><input name="to" id="to" type="text" class="JsVerify2"></td></tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Тема сообщения<star>*</star></td><td class="LongInput"><input name="subject" id="subject" type="text" class="JsVerify2"></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Текст сообщения<star>*</star></td><td class="LongInput"><textarea name="text" id="text" style="outline:none;"></textarea></td></tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText" style="width:17%; vertical-align:top; padding-top:15px;">Прикрепить файлы</td><td class="LongInput"><div id="uploader"></div></td></tr>';
	$AdminText.='</table>'.$C10.'<div class="CenterText"><input type="submit" name="sendbutton" id="sendbutton" class="SaveButton" value="Отправить"></div>';
	$AdminText.='</form></div>';
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="";
	
}

//=============================================
$_SESSION["Msg"]="";
?>