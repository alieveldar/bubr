<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$query=""; $sets=""; for($i=0; $i<10; $i++) { $sets.=((int)$P["Inp"][$i])."|"; }
		$res=DB("UPDATE `_pages` SET `sets`='".trim($sets, "|")."' WHERE (`link`='users')");
		$_SESSION["Msg"]="<div class='SuccessDiv'>Данные успешно сохранены!</div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT `sets` FROM `_pages` WHERE (`link`='users') LIMIT 1"); @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
	$ch=explode("|", $ar["sets"]); foreach ($ch as $k=>$v) { if ((int)$v==1) { $ch[$k]="checked"; }}
    
    $AdminText='<h2>Настройки пользователей</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";
	$AdminText.='<div class="RoundText"><table>
	<tr class="TRLine1">
		<td width="1%"><input name="Inp[0]" id="registration" type="checkbox" value="1" '.$ch[0].'></td><td width="49%">Разрешить регистрацию пользователей</td>
	    <td width="1%"><input name="Inp[1]" id="confirm_email" type="checkbox" value="1" '.$ch[1].'></td><td width="49%">Требовать подтверждение E-mail</td>
	</tr>
    <tr class="TRLine0">
		<td><input name="Inp[2]" id="social_registration" type="checkbox" value="1" '.$ch[2].'></td><td>Разрешить регистрацию через соц. сети</td>
    	<td><input name="Inp[3]" id="registration" type="checkbox" value="1" '.$ch[3].'></td><td>Разрешить комментарии к материалам</td>
	</tr>
	<tr class="TRLine1">
		<td><input name="Inp[4]" id="anonims_comments" type="checkbox" value="1" '.$ch[4].'></td><td>Разрешить комментарии от анонимов</td>
		<td><input name="Inp[5]" id="anonims_captcha" type="checkbox" value="1" '.$ch[5].'></td><td>Запрашивать у анонимов CAPTCHA</td>
	</tr>
    <tr class="TRLine0">
		<td><input name="Inp[6]" id="signature_in_comments" type="checkbox" value="1" '.$ch[6].'></td><td>Разрешить подписи в комментариях</td>
		<td><input name="Inp[7]" id="attachments_to_comments" type="checkbox" value="1" '.$ch[7].'></td><td>Разрешить вложения в комментариях материалов</td>
	</tr>
	</table></div><div class="C10"></div><h2>Дополнительные настройки</h2>';
	
	
	$AdminText.='<div class="RoundText"><table>
	
	
	</table></div>';
	
    $AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
	$AdminText.="</form>";


// ПРАВАЯ КОЛОНКА
$AdminRight="<h2>Аватар по умолчанию</h2><div class='RoundText' id='Tgg'><div class='AvatarV' id='AvatarT'><div id='AvatarI' class='Corner'><img src='/userfiles/avatar/no_photo.jpg?r=".time()."></div><div class='C5'></div><form action='return false;' enctype='multipart/form-data'><div title='Нажмите для выбора файла' id='Podstava' class='Podstava1'><input type='file' id='uavatar' name='uavatar' accept='image/jpeg,image/gif,image/x-png' onChange=\"StartUploadAvatar('no_photo');\" /></div></form></div><div class='C'></div></div><div class='C30'></div><div class='CenterText'><div class='LinkR'><a href='?cat=adm_settings'>Основные настройки сайта</a></div></div>";
}
$_SESSION["Msg"]="";
?>