<?	
$P=$_SESSION['Data'];
if (isset($P["sendbtn"])) {
	$linked=preg_replace('/[^a-zA-Z0-9_\.\-\/\:]+/i', '', $P["linked"]);
	$msg=trim(htmlspecialchars(strip_tags(str_replace(array("'"),"\'",$P["msgtoadmin"]))));
	$con=trim(htmlspecialchars(strip_tags(str_replace(array("'"),"\'",$P["conts"]))));
	if ($linked=="") { unset($_SESSION["Data"]["sendbtn"]); $_SESSION["msg"]='<div class="ErrorDiv">Ошибка! Не указан адрес страницы с нарушением</div>'; @header("location: /".$RealPage); exit(); }
	if ($msg=="") { unset($_SESSION["Data"]["sendbtn"]); $_SESSION["msg"]='<div class="ErrorDiv">Ошибка! Не указана суть претензии</div>'; @header("location: /".$RealPage); exit(); }
	if ($con=="") { unset($_SESSION["Data"]["sendbtn"]); $_SESSION["msg"]='<div class="ErrorDiv">Ошибка! Не указаны ваши контактные данные</div>'; @header("location: /".$RealPage); exit(); }

	$data=DB("SELECT `sets` FROM `_pages` WHERE (`module`='mistakes')"); @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
	DB("INSERT INTO `_mistakes` (`link`,`text`,`comment`,`data`) VALUES ('$linked', '$msg', '$con', '".time()."')"); SD(); unset($P);
	if ($ar["sets"]!="") { $text="Страница: ".$linked."<hr>".$msg."<hr>Контакты: ".$con."<hr>"; MailSend($ar["sets"], "Претензия", $text, $VARS["sitemail"]); }
	$_SESSION["msg"]="<div id='ShowSuccess'>Ваше сообщение успешно отправлено!</div>"; @header("location: /".$RealPage); exit(); 
}


$Page["Content"].=$C5.$_SESSION["msg"].$C10.'<h2>Отправить сообщение администрации:</h2>'.$C10.'
<form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post"><div class="RoundArts">'."
<input type='text' name='linked' placeholder='Введите адрес страницы с нарушением на Bubr.ru' class='FullInp' style='width:96%;' value='".$P["linked"]."'>".$C15."
<textarea class='FullArea' name='msgtoadmin' placeholder='Введите текст претензии'>".$P["msgtoadmin"]."</textarea>".$C10."
<textarea class='FullArea' name='conts' placeholder='Телефон, e-mail или иные контактные данные'>".$P["conts"]."</textarea>".$C."
<input type='submit' name='sendbtn' value='Отправить сообщение' class='SendInp'>".$C."</div></form>"; $_SESSION["msg"]='';
?>
