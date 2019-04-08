<?
### МЕНЮ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

$table="_users";

### Получаем данные страницы в $user;
$data=DB("SELECT * FROM `$table` WHERE (id='".$id."') LIMIT 1");
if ($data["total"]!=1) { 
	### Запись не найдена	
	$AdminText=ATextReplace('Item-Module-Error', $id, $table); $GLOBAL["error"]=1;
} else {
	### Запись найдена
	@mysql_data_seek($data["result"], 0); $user=@mysql_fetch_array($data["result"]);
	
if ($user["role"]>$_SESSION['userrole']) {
	$AdminText.='<h2 style="float:left;">Редактирование пользователя</h2>'.$C5."<div id='Msg2' class='ErrorDiv'>Вы не можете редактировать <a href='/users/view/$id'><b>$ar[nick]</b></a>, с уровнем выше чем у вас.</div>";
	$AdminText.="<div class='RoundText' id='Tgg'><a href='?cat=adm_users'>Вернуться к списку пользователей</a></div>";
} else {	

	// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["addbutton"])) {
		$P["pass"]=$P["pass"]=="" ? $user["pass"] : md5($P["pass"]);
		
		$res=DB("UPDATE `".$table."` SET
		`nick`='".$P["nick"]."',
		`login`='".$P["login"]."',
		`pass`='".$P["pass"]."',
		`role`='".(int)$P["role"]."',
		`karma`='".$P["karma"]."',
		`mail`='".$P["mail"]."',
        `spectitle`='".$P["spectitle"]."',
        `signature`='".$P["signature"]."',
        `vkontakte`='".$P["vkontakte"]."',
        `mailru`='".$P["mailru"]."',
        `twitter`='".$P["twitter"]."',
        `facebook`='".$P["facebook"]."',
        `odnoklas`='".$P["odnoklas"]."',
        `google`='".$P["google"]."',
        `yandex`='".$P["yandex"]."'
		WHERE (id='".$id."')");
				
		$_SESSION["Msg"]="<div class='SuccessDiv'>Пользователь сохранен!</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
}
	
// ФОРМА ДОБАВЛЕНИЯ
	$AdminText='<h2>Редактирование пользователя</h2>'.$_SESSION["Msg"]; if ($user["stat"]==1) { $chk="checked"; }
	
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";
	$AdminText.="<div class='RoundText' id='Tgg'><table>".'<tr class="TRLine0"><td style="width:25%;"></td><td style="width:75%;"></td></tr>
	<tr class="TRLine1"><td class="VarText">Ник<star>*</star></td><td class="LongInput"><input name="nick" id="nick" type="text" class="JsVerify2" value=\''.$user["nick"].'\'></td></tr>
	<tr class="TRLine1"><td class="VarText">Логин<star>*</star></td><td class="LongInput"><input name="login" id="login" type="text" class="JsVerify2" value=\''.$user["login"].'\'></td></tr>
	<tr class="TRLine0"><td class="VarText">Пароль<star>*</star></td><td class="LongInput"><input name="pass" id="pass" type="password" value=""></td></tr>
    <tr class="TRLine1"><td class="VarText">Почта<star>*</star></td><td class="LongInput"><input name="mail" id="mail" type="text" class="JsVerify2" value=\''.$user["mail"].'\'></td></tr>
    <tr class="TRLine0"><td class="VarText">Карма</td><td class="LongInput"><input name="karma" id="karma" type="text" value=\''.$user["karma"].'\'></td></tr>
    <tr class="TRLine1"><td class="VarText">Тайтл</td><td class="LongInput"><input name="spectitle" id="spectitle" type="text" value=\''.$user["spectitle"].'\'></td></tr>
    <tr class="TRLine0"><td class="VarText">Подпись</td><td class="LongInput"><input name="signature" id="signature" type="text" value=\''.$user["signature"].'\'></td></tr>
    <tr class="TRLine1"><td class="VarText">Роль</td><td class="LongInput"><div class="sdiv"><select name="role">'.GetSelected($GLOBAL["roles"], $user["role"]).'</select></div></td></tr>
    <tr class="TRLine0"><td class="VarText" colspan="2"><h2 style="margin:0;">ID и/или логины в соцсетях:</h2></td></tr>
    <tr class="TRLine1"><td class="VarText">Вконтакте</td><td class="LongInput"><input name="vkontakte" id="vkontakte" type="text" value=\''.$user["vkontakte"].'\'></td></tr>
    <tr class="TRLine0"><td class="VarText">Mail.ru</td><td class="LongInput"><input name="mailru" id="mailru" type="text" value=\''.$user["mailru"].'\'></td></tr>
    <tr class="TRLine1"><td class="VarText">twitter</td><td class="LongInput"><input name="twitter" id="twitter" type="text" value=\''.$user["twitter"].'\'></td></tr>
    <tr class="TRLine0"><td class="VarText">facebook</td><td class="LongInput"><input name="facebook" id="facebook" type="text" value=\''.$user["facebook"].'\'></td></tr>
    <tr class="TRLine1"><td class="VarText">Одноклассники</td><td class="LongInput"><input name="odnoklas" id="odnoklas" type="text" value=\''.$user["odnoklas"].'\'></td></tr>
    <tr class="TRLine0"><td class="VarText">Google+</td><td class="LongInput"><input name="google" id="google" type="text" value=\''.$user["google"].'\'></td></tr>
    <tr class="TRLine1"><td class="VarText">Яндекс</td><td class="LongInput"><input name="yandex" id="yandex" type="text" value=\''.$user["yandex"].'\'></td></tr>                        
	'."</table><div class='C5'></div>
	<div class='C15'></div><div class='CenterText'><input type='submit' name='addbutton' id='addbutton' class='SaveButton' value='Сохранить'></div></div></form>";
	
	if ($user["avatar"]!="") { $img="<img src='/".$user["avatar"]."'>"; } else { $img=""; } $lasttime=$ar["lasttime"] ? date("d.m.Y, H:i:s", $ar["lasttime"]) : 'Нет';
	$data=DB("SELECT `id` FROM `_comments` WHERE (`uid`='".$id."')"); $total=$data["total"];
	
	$AdminRight="<h2>Аватар</h2><div class='RoundText' id='Tgg'><div class='AvatarV' id='AvatarT'><div id='AvatarI' class='Corner'>".$img."</div><div class='C5'></div><form action='return false;' enctype='multipart/form-data'>
	<div title='Нажмите для выбора файла' id='Podstava' class='Podstava1'><input type='file' id='uavatar' name='uavatar' accept='image/jpeg,image/gif,image/x-png' onChange='StartUploadAvatar(".$id.");' /></div></form></div><div class='C'></div></div>
	<div class='C10'></div><div class='CenterText'><div class='LinkG'><a href='?cat=adm_userschange&id=$user[id]'>Войти под этим логином</a></div></div><div class='C20'></div>
	<h2>Статус</h2><div class='RoundText' id='Tgg'><table>".'
	<tr class="TRLine0"><td class="VarName">Активен</td><td><input type="checkbox" id="RS-'.$id.'-'.$table.'" name="dlvl" value="1" '.$chk.' /></td></tr>
	<tr class="TRLine1"><td class="VarName">Регистрация</td><td>'.date("d.m.Y, H:i:s", $user["created"]).'</td></tr>
	<tr class="TRLine0"><td class="VarName">Активность</td><td>'.$lasttime.'</td></tr>
	<tr class="TRLine1"><td class="VarName">Карма</td><td>'.$user["karma"].'</td></tr>
	<tr class="TRLine0"><td class="VarName">Записей</td><td>'.$total.'</td></tr>
	<tr class="TRLine1"><td class="VarName">Профиль</td><td><a href="/users/view/'.$id.'/">Перейти</a></td></tr>	
	'."</table></div><div class='C20'></div><div class='CenterText'><div class='LinkR'>
	<a href='javascript:void(0);' onclick='LinkBlank(\"Удалить комментарии\",\"Удалить все комментарии этого пользователя без возможности восстановления?<br>Так же будут удалены все ответы на комментарии и все приложенные файлы.\", \"?cat=adm_clearcomms&id=".$id."\")'>Удалить все комментарии</a></div></div>";
}	
}}
$_SESSION["Msg"]="";

?>