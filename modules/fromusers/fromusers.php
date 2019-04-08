<?
$table=$link."_nodes";
$table2=$link."_cats";

if (isset($_SESSION['Data']["sendbutton"])) {
	$P = $_SESSION['Data'];
	if((isset($P['captcha']) && strtolower($P['captcha']) != strtolower($_SESSION["CaptchaCode"])) || trim($P['name']) == '' || trim($P['author']) == '' || trim($P['contacts']) == '') $msg = '<div class="ErrorDiv">Ошибка! Поля не заполнены или заполнены неверно</div>';
	else{
		if (mb_strpos($P['text'], "[url=")===false) { 
		$pics = "";	
		$name = strip_tags($P['name']);
		$text = strip_tags($P['text']);
		$author = strip_tags($P['author']);
		$contacts = strip_tags($P['contacts']);
		$cid = $P['cat'] ? $P['cat'] : $start;
		if($P["attachment"]){
			foreach ($P["attachment"] as $pic) {
				$pics .= $pics ? "|".$pic : $pic;
			}
		}
		$q="INSERT INTO `$table` (`cat`, `name`, `text`, `author`, `contacts`, `pics`, `data`) VALUES ($cid, '$name', '$text', '$author', '$contacts', '$pics', '".time()."')";	DB($q);		
		$msg = '<div class="SuccessDiv">Спасибо! Ваша заявка отправлена редактору сайта</div>';
		$P = array();
		
		$data=DB("SELECT `email`, `name` FROM `".$table2."` WHERE `id`=$cid");
		@mysql_data_seek($data["result"], 0); $cat=@mysql_fetch_array($data["result"]);
		$subject = 'Поступила заявка в '.$cat['name'];
		$body = $name.'"<hr>';
		$body .= $text.'"<hr>';
		$body .= date('d.m.Y H:i', time()).' Автор: '.$author;
		MailSend($cat['email'], $subject, $body, $VARS["sitemail"]);
		} 
	}
	SD();	
}

$where = $start ? "WHERE `id`=$start" : ''; $data=DB("SELECT `id`,`name` FROM `".$table2."` $where"); @mysql_data_seek($data["result"], 0); $cat=@mysql_fetch_array($data["result"]);
if($data["total"] > 1){ $cats = array(); for ($i=0; $i<$data["total"]; $i++){ @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $cats[$ar['id']] = $ar['name']; }}

$text = '<link media="all" href="/modules/standart/multiupload/client/uploader2.css" type="text/css" rel="stylesheet"><script type="text/javascript" src="/modules/standart/multiupload/client/uploader.js"></script>';
$text.=$msg;
$text.='<div class="RoundText" id="Tgg"><form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post" onsubmit="return JsVerify();"><table>';
$text.='<tr class="TRLine0"><td class="VarText">Название<star>*</star></td><td class="LongInput"><input name="name" type="text" value="'.$P['name'].'" class="JsVerify2" style="width:400px !important;"></td></tr>';
$text.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top; padding-top:10px;">Текст</td><td class="LongInput"><textarea name="text" style="outline:none; width:400px !important;">'.$P['text'].'</textarea></td></tr>';
$text.='<tr class="TRLine0"><td class="VarText">Автор<star>*</star></td><td class="LongInput"><input name="author" type="text" value="'.$GLOBAL["USER"]['nick'].'" style="outline:none; width:400px !important;" class="JsVerify2"></td></tr>';
$text.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top; padding-top:10px;">Контакты<star>*</star></td><td class="LongInput"><input name="contacts" style="outline:none; width:400px !important;" type="text" value="'.$P['contacts'].'" class="JsVerify2"><div class="Info">Укажите ваши контактные данные: телефон или e-mail. Контакты не публикуются и нужны только для уточнения информации редактором сайта, мы не требуем отправлять СМС или других операций.</div></td></tr>';
$text.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:25px;">Фотографии</td><td class="LongInput">'.$C10.'<div id="uploader"></div>'.$C5.'<div class="Info">Вы можете загрузить фотографию в форматах jpg, gif и png</div></td></tr>';
if(!$start) $text.='<tr class="TRLine0"><td class="VarText">Категория</td><td class="LongInput"><select name="cat">'.GetSelected($cats, $cat['id']).'</select></td></tr>';
if($UserSetsSite[5] == '1') $text.='<tr class="TRLine0"><td class="VarText">Код с картинки<star>*</star></td><td class="MiniInput"><img src="/modules/standart/captcha/Captcha.php?'.time().'" class="captchaImg" /><input name="captcha" type="text" class="JsVerify2"></td></tr>';
$text.='</table>'.$C10.'<div class="CenterText"><input type="submit" name="sendbutton" id="sendbutton" class="SaveButton" value="Отправить"></div>';
$text.='</form></div>';

$Page["Content"] = $text; if($start) $Page["Caption"] = $cat['name'];

function GetSelected($ar, $id) {
	$text=""; foreach ($ar as $key=>$val) { if ($key==$id) { $text.="<option value='$key' selected style='color:#FFF; background:#036;'>$val</option>"; } else { $text.="<option value='$key'>$val</option>"; }} return $text;
}
?>