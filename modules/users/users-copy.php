<?
### ПОЛЬЗОВАТЕЛИ ##########################################################################################################################################
$start.=""; $table="_users";  $edit='';

if ($UserSetsSite[0]==1) {

	if ($start=="auth") { list($text, $cap)=GetUsersAuth(); } // авторизация
	if ($start=="reg") { list($text, $cap)=GetUsersReg(); }  // регистрация
	if ($start=="sendpass") { list($text, $cap)=GetUsersSendPass(); }  // ВОССТАНОВЛЕНИЕ
	if ($start=="msg") { list($text, $cap)=GetUsersMsg(); } // сообщения от админов
	if ($start=="my") { list($text, $cap)=GetUsersSets(); } // настройки 
	if ($start=="list") { list($text, $cap)=GetUsersList(); } // список статей
	if ($start=="add") { list($text, $cap)=GetUsersAdd(); } // добавить статьи
	if ($start=="moderate") { list($text, $cap)=SendToModerate(); } // отправить на модерацию
	if ($start=="escapemoderate") { list($text, $cap)=EscapeModerate(); } // отменить модерацию
	if ($start=="edit") { list($text, $cap)=GetUsersEdit(); } // править статьи
	if ($start=="confirmdel") { list($text, $cap)=ConfirmDel(); } // подтверждение удаления
	if ($start=="confirmdelyes") { list($text, $cap)=ConfirmDelYes(); } // удалить статью
	if ($start=="view") { list($text, $cap)=GetUsersId(); }	// данные о пользователе
	if ($start=="preview") { list($text, $cap)=GetUsersArticle(); }	// просмотр статьи
	
	
	
	if ($start=="finance") { list($text, $cap)=GetUsersMoney(); } // начисления и кнопка вывода
	if ($start=="pay") { list($text, $cap)=GetUsersMoneyOut(); } // вывод денег
	
	
	if ($start=="exit") { $_SESSION["userid"]=0; $_SESSION["userrole"]=0; setcookie('usercookuid','',time()-30000, "/"); setcookie('usercookmd5','',time()-30000, "/"); $_COOKIE['usercookuid']=''; $_COOKIE['usercookmd5']=''; unset($_SESSION); unset($_COOKIE); @header("Location: ".$_SERVER['HTTP_REFERER']); exit(); }
	if ($start=="lostid") { $Page404=1; $cap="Доступ закрыт"; $text="<b>Это могло случиться по нескольким причинам:</b><ul><li>Истекло время жизни сессии: авторизуйтесь ещё раз</li><li>На сайте идет модернизация: работа некоторых модулей нестабильна</li><li>Доступ закрыт администратором: скорее всего, вы нарушили правила сайта</li></ul>"; }
} else {
	$text=@file_get_contents($ROOT."/template/404.html"); $cap="Страница не найдена - 404"; $Page404=1;
}

$Page["Content"] = $text; $Page["Caption"] = $cap;  

#############################################################################################################################################
#############################################################################################################################################
#############################################################################################################################################

function SendToModerate() { global $dir, $GLOBAL; $data=DB("UPDATE `post_users` SET `stat`='1' WHERE (`uid`='".$GLOBAL["USER"]["id"]."' && `id`='".(int)$dir[2]."' && `stat`='0') LIMIT 1"); header("location: /users/list#moderate"); exit(); }
function EscapeModerate() { global $dir, $GLOBAL; $data=DB("UPDATE `post_users` SET `stat`='0' WHERE (`uid`='".$GLOBAL["USER"]["id"]."' && `id`='".(int)$dir[2]."' && `stat`='1') LIMIT 1"); header("location: /users/list#chernovik"); exit(); }

function ConfirmDel() { global $VARS, $GLOBAL, $dir, $Page, $node, $page, $table, $RealPage, $RealHost, $C10, $C15, $C5, $C, $C20, $ROOT; $USER=$GLOBAL["USER"]; $cap="Подтверждение удаления"; if ((int)$USER["id"]==0) { @header("location: /users/auth"); exit(); }
$data=DB("SELECT * FROM `post_users` WHERE (`id`='".(int)$page."') LIMIT 1"); if ($data["total"]==0) { return(array("Публикация не найдена", "Ошибка")); } @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); 
if ($ar["stat"]==1 || $ar["stat"]==2) { return(array("Запрещено удалять эту публикацию", "Ошибка")); } if ($ar["uid"]!=(int)$USER["id"] && (int)$USER["role"]<2) { return(array("В доступе отказано", "Ошибка")); }
$text="<h3>«".$ar["name"]."»</h3>".$C10."Вы действительно хотите удалить эту статью? (Действие будет невозможно отменить!)".$C20.
"<a href='/users/confirmdelyes/".$page."' class='Yes'>Да, удалить!</a><a href='/users/edit/".$page."' class='No'>Нет, оставить</a>".$C; $_SESSION["msg"]=''; return(array($text, $cap)); }

function ConfirmDelYes() { global $VARS, $GLOBAL, $dir, $Page, $node, $page, $table, $RealPage, $RealHost, $C10, $C15, $C5, $C, $C20, $ROOT; $USER=$GLOBAL["USER"]; $cap="Удаление статьи"; if ((int)$USER["id"]==0) { @header("location: /users/auth"); exit(); }
$data=DB("SELECT * FROM `post_users` WHERE (`id`='".(int)$page."') LIMIT 1"); if ($data["total"]==0) { return(array("Публикация не найдена", "Ошибка")); } @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); 
if ($ar["stat"]==1 || $ar["stat"]==2) { return(array("Запрещено удалять эту публикацию", "Ошибка")); } if ($ar["uid"]!=(int)$USER["id"] && (int)$USER["role"]<2) { return(array("В доступе отказано", "Ошибка")); }
DB("DELETE FROM `_widget_pics` WHERE (`pid`='".(int)$page."' && `link`='userslenta')"); DB("DELETE FROM `post_users` WHERE (`id`='".(int)$page."') LIMIT 1");
$text="<h3>«".$ar["name"]."»</h3>".$C10."Статья была удалена. <a href='/users/list'>Перейти к списку статей</a>".$C; $_SESSION["msg"]=''; return(array($text, $cap)); }
 
#############################################################################################################################################
#############################################################################################################################################
#############################################################################################################################################

function GetUsersMoneyOut() {
	global $VARS, $GLOBAL, $dir, $Page, $node, $table, $RealPage, $RealHost, $C10, $C15, $C5, $C, $C20, $ROOT; $USER=$GLOBAL["USER"]; if ((int)$USER["id"]==0) { @header("location: /users/auth"); exit(); }
	// ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- ---
	if (isset($_SESSION['Data']["sendbtn"])) { $P=$_SESSION['Data'];
		$bal=DB("SELECT SUM(`money`) as `balance` FROM `_userspays` WHERE (`uid`='$USER[id]')"); @mysql_data_seek($bal["result"], $i); $ar=@mysql_fetch_array($bal["result"]); $balance=$ar["balance"];
		$m=(int)$P["sumout"];
		$p=$P["phoneout"]; $p=preg_replace('/[^0-9_\.\-]+/i', '', $p);
		$w=$P["webmoneyout"]; $w=preg_replace('/[^0-9_\.\-]+/i', '', $w);
		$s=$P["sberout"]; $s=preg_replace('/[^0-9_\.\-]+/i', '', $s);
		if ($m==0 || $m>$balance) {
			$_SESSION["msg"]='<div class="ErrorDiv">Ошибка! Неверно введена сумма вывода</div>'; @header("location: /users/my"); exit(); SD(); 
		} else {
			if ($m==0 || $m>$balance) {	
			
			
			Ъ else {
				
				
				
		}			
		}
		
		SD();
	}
	// ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- ---
	$bal=DB("SELECT SUM(`money`) as `balance` FROM `_userspays` WHERE (`uid`='$USER[id]')"); @mysql_data_seek($bal["result"], $i); $ar=@mysql_fetch_array($bal["result"]); $balance=$ar["balance"]; $cap="Ваш баланс: ".$balance." руб.";
	if ($balance<500) { $text="Вывод денег доступен при балансе более 500 рублей"; } else {
		if ($USER["phone"]=="" || (int)$USER["phone"]==0) { $text="Для вывода денег, вне зависимости от способа вывода, необходимо <a href='/users/my'>заполнить поле контактного телефона</a>, именно по этому номеру свами свяжется администратор, если возникнул каки-либо вопросы."; } else {
		// ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- ---
		$text.="<h2>Заявка на вывод:</h2>";
		$text.=$_SESSION["msg"].$C10.'<form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post">';
		$text.="<span style='color:red;'>Внимание!</span> Вывод денег производится 3 раза в неделю, информацию о завершении вывода денег вы увидите в <a href='/users/msg'>личных сообщениях</a>.".$C10;
		$text.="<div class='RoundArts'> Введите сумму для вывода:".$C5."<input type='text' name='sumout' placeholder='Введите сумму для вывода' class='FullInp' style='width:96%;' value='$balance'>".$C."</div>".$C;
		$text.="Заполните поле ниже соответственно более удобному для вас методу вывода денег, если вы заполняете несколько полей, вывод будет на усмотрение администрации Bubr.ru:".$C15;
		$text.="<div class='RoundArts'> Вывод на счет мобильного телефона:".$C5."<input type='text' name='phoneout' placeholder='Введите номер мобильного телефона' class='FullInp' style='width:96%;'>".$C."</div>".$C5;
		$text.="<div class='RoundArts'> Вывод на R кошелек webmoney:".$C5."<input type='text' name='webmoneyout' placeholder='Введите номер webmoney кошелька без R' class='FullInp' style='width:96%;'>".$C."</div>".$C5;
		$text.="<div class='RoundArts'> Вывод на карту Сбербанка:".$C5."<input type='text' name='sberout' placeholder='Введите номер карты Сбербанка' class='FullInp' style='width:96%;'>".$C."</div>".$C5;
		$text.="<input type='submit' name='sendbtn' value='Заказать вывод денег' class='SendInp'></form>";		
	}}
	// ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- ---
	$text.=$C20."<h2>История операций:</h2>"; $data=DB("SELECT * FROM `_userspays` WHERE (`uid`='$USER[id]') order by `data` desc");
	if ($data["total"]==0) { $mon='Здесь нет операций'; } else { $mon="<div class='RoundArts'><table>"; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $mon.="<tr>"; $d=ToRusData($ar["data"]);
	if ((int)$ar["money"]>0) { $ar["money"]="<b style='color:green;'>+".$ar["money"]."</b>"; } else { $ar["money"]="<b style='color:red;'>".$ar["money"]."</b>"; }  
	$mon.="<td class='act2'><data>".$d[4]."</data></td><td class='act2'><data>".$ar["money"]." руб.</data></td><td class='name'>".$ar["text"]."</td></tr>"; endfor; $mon.="</table></div>"; $text.=$mon; }
	// ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- --- ---- ---
	$_SESSION["msg"]=''; return(array($text, $cap));
}

#############################################################################################################################################
#############################################################################################################################################
#############################################################################################################################################


function GetUsersAuth() {
	global $USER, $C, $C10, $RealPage; $cap="Авторизация"; if ((int)$USER["id"]!=0) { @header("location: /users/my"); exit(); }
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	if (isset($_SESSION['Data']["authbtn"])) { $P=$_SESSION['Data'];
	if (trim($P['authlogin'])=='' || trim($P['authpass'])=='') { $_SESSION["msg"]='<div class="ErrorDiv">Ошибка! Поля не заполнены или заполнены неверно</div>'; SD(); @header("location: /".$RealPage); exit(); } else {
	$data=DB("SELECT `id`,`role` FROM `_users` WHERE (`login`='".htmlspecialchars($P['authlogin'])."' && `pass`='".md5($P['authpass'])."' && `stat`=1) LIMIT 1");
	if ($data["total"]==0) { $_SESSION["msg"]='<div class="ErrorDiv">Ошибка! Пользователь с такими данными не найден</div>'; SD(); @header("location: /".$RealPage); exit(); } else {
	@mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $_SESSION['userid']=$ar["id"]; $_SESSION['userrole']=$ar["role"]; @header("location: /users/my"); exit(); }} SD(); }
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	$text="<b>Для добавления своих материалов, необходимо авторизоваться.</b>".$C10."Если вы первый раз входите на сайт, пройдите процедуру <a href='/users/reg'>Регистрации</a> &mdash; это просто и не займет много времени.";
	$text.=$_SESSION["msg"].$C10.'<form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post">'; $text.="<input type='text' name='authlogin' value='' placeholder='Введите Email - он же ваш логин' class='FullInp'>".$C10;
		$text.="<input type='password' name='authpass' value='' placeholder='Введите пароль' class='FullInp'>".$C10; $text.="<input type='submit' name='authbtn' value='Авторизация' class='SendInp'><a href='/users/sendpass' style='margin-left:50px;'>Забыли пароль?</a>".$C;
	$text.="</form>"; $_SESSION["msg"]=''; return(array($text, $cap));
}

#############################################################################################################################################
#############################################################################################################################################
#############################################################################################################################################

function GetUsersReg() {
	global $USER, $VARS, $C, $C10, $RealPage; $cap="Регистрация"; if ((int)$USER["id"]!=0) { @header("location: /users/my"); exit(); }
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	if (isset($_SESSION['Data']["regbtn"])) { $P=$_SESSION['Data']; $P['reglogin']=htmlspecialchars($P['reglogin']); $P['regname']=htmlspecialchars($P['regname']); $_SESSION["msg"]='';
		if (trim($P['reglogin'])=='' || trim($P['regpass1'])=='' || trim($P['regpass2'])=='' || trim($P['regname'])=='') { $_SESSION["msg"]='<div class="ErrorDiv">Ошибка! Не заполнены требуемые поля</div>'; SD(); @header("location: /".$RealPage); exit(); }
		if (trim($P['regpass1'])!=trim($P['regpass2'])) { $_SESSION["msg"]='<div class="ErrorDiv">Ошибка! Пароли не совпадают</div>'; SD(); @header("location: /".$RealPage); exit(); }
		if (!preg_match("/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i", trim($P['reglogin']))) { $_SESSION["msg"]='<div class="ErrorDiv">Ошибка! Введен неверный Email, он же логин</div>'; SD(); @header("location: /".$RealPage); exit(); }
		$data=DB("SELECT `id` FROM `_users` WHERE (`login`='".$P['reglogin']."') LIMIT 1"); if ($data["total"]!=0) { $_SESSION["msg"]='<div class="ErrorDiv">Ошибка! Email уже используется, <a href="/users/sendpass">восстановить доступ</a>?</div>'; SD(); @header("location: /".$RealPage); exit(); }
		DB("INSERT INTO `_users` (`login`,`pass`,`nick`,`stat`,`created`) VALUES ('".$P['reglogin']."','".md5($P['regpass1'])."','".$P['regname']."','1','".time()."')"); $_SESSION['userid']=DBL();
		$body = 'Вы зарегистрировались на сайте '.$VARS['sitename'].'<br><hr><br>Логин: '.$P['reglogin'].'<br><br>Пароль: '.$P['regpass1'].'<br><br><hr><br>C уважением, команда <a href="http://bubr.ru" target="_blank">Bubr.ru</a>';
	$subject = 'Добро пожаловать на '.$VARS['sitename']; MailSend($P['reglogin'], $subject, $body, $VARS["sitemail"]); $_SESSION["msg"]=''; SD(); @header("location: /users/my"); exit(); }
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	$text.="<p style='text-align:justify;'>После регистрации вы сможете публиковать свои материалы, пожалуйста, отнеситесь серьезно к данной процедуре, чтобы в дальнейшем не возникло проблем с выводом заработанных вами денег.</p>";
	$text.="<p style='text-align:justify;'><B>Все поля обязательны для заполнения.</B></p>"; $text.=$_SESSION["msg"].$C10.'<form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post">';
		$text.="<input type='text' name='reglogin' placeholder='Введите Email - он же ваш логин' class='FullInp'>".$C10; $text.="<input type='text' name='regname' placeholder='Введите ваш никнейм (псевдоним)' class='FullInp'>".$C10;
		$text.="<input type='password' name='regpass1' placeholder='Введите пароль' class='FullInp'>".$C10; $text.="<input type='password' name='regpass2' placeholder='Повторите пароль' class='FullInp'>".$C10;
		$text.="<input type='submit' name='regbtn' value='Регистрация' class='SendInp'><a href='/agreement' target='_blank' style='margin-left:50px;'>Пользовательское соглашение</a>".$C;
	$text.="</form>"; $_SESSION["msg"]=''; return(array($text, $cap));
}

#############################################################################################################################################
#############################################################################################################################################
#############################################################################################################################################

function GetUsersSendPass() {
	global $USER, $VARS, $C, $C10, $RealPage; $cap="Восстановление доступа"; if ((int)$USER["id"]!=0) { @header("location: /users/my"); exit(); }
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	if (isset($_SESSION['Data']["authbtn"])) { $P=$_SESSION['Data'];
		if (trim($P['authlogin'])=='' || !preg_match("/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i", trim($P['authlogin']))) { $_SESSION["msg"]='<div class="ErrorDiv">Ошибка! Поле Email не заполнено или заполнено неверно</div>'; SD(); @header("location: /".$RealPage); exit(); } else {
		$data=DB("SELECT `id`,`stat` FROM `_users` WHERE (`login`='".htmlspecialchars($P['authlogin'])."') LIMIT 1");
		if ($data["total"]==0) { $_SESSION["msg"]='<div class="ErrorDiv">Ошибка! Пользователь с такими логином не найден</div>'; SD(); @header("location: /".$RealPage); exit(); } @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $id=$ar["id"];
		if ($ar["stat"]==0) { $_SESSION["msg"]='<div class="ErrorDiv">Ошибка! Пользователь заблокирован за нарушение правил</div>'; SD(); @header("location: /".$RealPage); exit(); } $newpass=rand(111111111, 9999999999); DB("UPDATE `_users` SET `pass`='".md5($newpass)."' WHERE (`id`='".$id."') LIMIT 1");
		$body = 'Восстановление доступа на сайте '.$VARS['sitename'].'<br><hr><br>Логин: '.$P['authlogin'].'<br><br>Пароль: '.$newpass.'<br><br><hr><br>C уважением, команда <a href="http://bubr.ru" target="_blank">Bubr.ru</a>';
	$subject = 'Новый пароль '.$VARS['sitename']; MailSend($P['authlogin'], $subject, $body, $VARS["sitemail"]); $_SESSION["msg"]='<div class="SuccessDiv">Новый пароль был отправлен на указанный Email</div>'; SD(); @header("location: /".$RealPage); exit(); } SD(); }
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	$text="<b>Для получения нового пароля, введите ваш логин (Email).</b>".$C10."Если вы первый раз входите на сайт, пройдите процедуру <a href='/users/reg'>Регистрации</a> &mdash; это просто и не займет много времени.";
	$text.=$_SESSION["msg"].$C10.'<form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post">'; $text.="<input type='text' name='authlogin' value='' placeholder='Введите Email - он же ваш логин' class='FullInp'>".$C10;
	$text.="<input type='submit' name='authbtn' value='Отправить пароль' class='SendInp'><a href='/users/reg' style='margin-left:50px;'>Новая регистрация</a>".$C; $text.="</form>"; $_SESSION["msg"]=''; return(array($text, $cap));
}

#############################################################################################################################################
#############################################################################################################################################
#############################################################################################################################################

function GetUsersSets() {
	global $VARS, $GLOBAL, $dir, $Page, $node, $table, $RealPage, $RealHost, $C10, $C15, $C5, $C, $C20, $ROOT; $USER=$GLOBAL["USER"]; $cap="Настройки аккаунта"; if ((int)$USER["id"]==0) { @header("location: /users/auth"); exit(); }
	if ($USER["avatar"]=="" || !is_file($ROOT."/".$USER["avatar"]) || filesize($ROOT."/".$USER["avatar"])<100) { $avatar="<img src='/userfiles/avatar/no_photo.jpg'>"; } else { $avatar="<img src='/".$USER["avatar"]."'>"; }
	### Основные настройки сайта
	$text.="<b>Логин:</b> ".$USER["login"].$C20."<div style='float:left;'><div class='MailUs'><div id='sendstat' class='SaveDiv'>Сохранение настроек</div>
	<div class='Lab'>Введите псевдоним (вас будут знать под этим именем)<star>*</star></div><input class='Inp380' id='uname' type='text' value='$USER[nick]' maxlenght='64' placeholder='это имя видят посетители сайта' /><div class='Lab'>Введите телефон (на него можно вывести деньги)</div><input class='Inp380' id='uphone' type='text' value='$USER[phone]' maxlenght='64' placeholder='это имя видят посетители сайта' />";
	if ($ufrom=="") { $text.="<div class='Lab'>Сменить пароль от аккаунта</div><input class='Inp380' id='upass' type='text' maxlenght='64' placeholder='новый пароль' />".$C; } else { $text.="<div class='Lab' style='display:none;'>Сменить пароль от аккаунта</div><input class='Inp380' id='upass' type='hidden' maxlenght='64' placeholder='новый пароль' />".$C; }
	$text.="<input type='submit' name='sendbutton' id='sendbutton' class='SaveButton' value='Сохранить настройки' onClick='SaveSettings();'></div></div><div class='Avatar' id='AvatarT' style='width:270px;'><div id='AvatarI'>".$avatar."</div><span class='Info'>Загрузить фотографию<br>Рекомендуем: 100x100px</span>
	<form action='return false;' enctype='multipart/form-data'><div title='Нажмите для выбора файла' id='Podstava' class='Podstava1'><input type='file' id='uavatar' name='uavatar' accept='image/jpeg,image/gif,image/x-png' onChange='StartUploadAvatar();' /></div></form></div>".$C;

	### Баланс
	$bal=DB("SELECT SUM(`money`) as `balance` FROM `_userspays` WHERE (`uid`='$USER[id]')"); @mysql_data_seek($bal["result"], $i); $ar=@mysql_fetch_array($bal["result"]); $balance=$ar["balance"];
		
	$text.=$C20.$C10."<h2>Ваш баланс <span style='font-size:14px;'>(<a href='/rules' target='_blank'>Как тут заработать</a>)</span></h2>";
	$text.="<div class='Round'>Ваш баланс: <b>".(int)$balance."</b> руб.<a href='/users/pay'>Вывести деньги</a></div>".$C10;
	$text.=$C10."<h2>Ваши статьи <span style='font-size:14px;'>(<a href='/rules' target='_blank'>Правила публикации</a>)</span></h2>"; $data=DB("SELECT `id` FROM `post_users` WHERE (`uid`='".(int)$USER["id"]."')");
	$text.="<div class='Round'>Всего статей добавлено: <b>".(int)$data["total"]."</b><a href='/users/add'>Добавить статью</a><a href='/users/list' style='margin-right:5px;'>Мои статьи</a></div>".$C10;
	return(array($text, $cap)); 
}

#############################################################################################################################################
#############################################################################################################################################
#############################################################################################################################################

function GetUsersList() {
	$stavka=0.2; global $VARS, $GLOBAL, $dir, $Page, $node, $table, $RealPage, $RealHost, $C10, $C15, $C5, $C, $C20, $ROOT; $USER=$GLOBAL["USER"]; $cap="Список ваших статей"; if ((int)$USER["id"]==0) { @header("location: /users/auth"); exit(); }
	if ($USER["avatar"]=="" || !is_file($ROOT."/".$USER["avatar"]) || filesize($ROOT."/".$USER["avatar"])<100) { $avatar="<img src='/userfiles/avatar/no_photo.jpg'>"; } else { $avatar="<img src='/".$USER["avatar"]."'>"; }
	# ЧЕРНОВИКИ
	$data=DB("SELECT `id`,`name`,`data` FROM `post_users` WHERE (`stat`='0' && `uid`='".$USER["id"]."') ORDER BY `data` DESC"); if ($data["total"]==0) { $articleschern='Здесь нет статей'; } else {
	$articleschern="<table>"; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $articleschern.="<tr>"; $d=ToRusData($ar["data"]);
		$articleschern.="<td class='act2' title='Номер статьи (ID)'><data>ID=".$ar["id"]."</data></td>";
		$articleschern.="<td class='name'><a href='/users/preview/".$ar["id"]."' target='_blank'>".$ar["name"]."</a><data> / $d[0]</data></td>";
		$articleschern.="<td class='act1' title='Редактировать статью'><a href='/users/edit/".$ar["id"]."'>Править</a></td>";
		$articleschern.="<td class='act2' title='Отправить на проверку модератором'><a href='/users/moderate/".$ar["id"]."'>На модерацию</a></td>";
	$articleschern.="</tr>"; endfor; $articleschern.="</table>"; }
	# НА МОДЕРАЦИИ
	$data=DB("SELECT `id`,`name`,`data` FROM `post_users` WHERE (`stat`='1' && `uid`='".$USER["id"]."') ORDER BY `data` DESC"); if ($data["total"]==0) { $articlesno='Здесь нет статей'; } else {
	$articlesno="<table>"; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $articlesno.="<tr>"; $d=ToRusData($ar["data"]);
		$articlesno.="<td class='act2' title='Номер статьи (ID)'><data>ID=".$ar["id"]."</data></td>";
		$articlesno.="<td class='name'><a href='/users/preview/".$ar["id"]."' target='_blank'>".$ar["name"]."</a><data> / $d[0]</data></td>";
		$articlesno.="<td class='act2' title='Снять с проверки модератором'><a href='/users/escapemoderate/".$ar["id"]."'>Отмена проверки</a></td>";
	$articlesno.="</tr>"; endfor; $articlesno.="</table>"; }
	# ОПУБЛИКОВАНО МЕНЕЕ 7 ДНЕЙ
	$data=DB("SELECT `post_users`.`id` as `oldid`, `post_lenta`.`id`, `post_lenta`.`data`, `post_lenta`.`name`, `post_lenta`.`seen` FROM `post_users` LEFT JOIN `post_lenta` ON `post_users`.`lentaid`=`post_lenta`.`id`
	WHERE (`post_users`.`stat`='2' && `post_users`.`uid`='".$USER["id"]."' && `post_lenta`.`data`>'".(time()-60*60*24*7)."') ORDER BY `data` DESC");
	if ($data["total"]==0) { $articlesok='Здесь нет статей'; } else { $articlesok="<table>"; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $articlesok.="<tr>"; $d=ToRusData($ar["data"]);
		$articlesok.="<td class='name'><a href='/post/view/".$ar["id"]."' target='_blank'>".$ar["name"]."</a><br><data>ID=".$ar["id"]." / Одобрено: $d[0] / Статус: идет заработок</data></td>"; $articlesok.="<td class='act2'>Показов: ".$ar["seen"]."</td><td class='act2'>Сумма: ".round($ar["seen"]*$stavka, 1)." руб.</td>";
	$articlesok.="</tr>"; endfor; $articlesok.="</table>"; }
	# ОПУБЛИКОВАНО БОЛЕЕ 7 ДНЕЙ	
	$data=DB("SELECT `post_users`.`id` as `oldid`, `post_lenta`.`id`, `post_lenta`.`data`, `post_lenta`.`name`, `post_lenta`.`seen` FROM `post_users` LEFT JOIN `post_lenta` ON `post_users`.`lentaid`=`post_lenta`.`id`
	WHERE (`post_users`.`stat`='2' && `post_users`.`uid`='".$USER["id"]."' && `post_lenta`.`data`<'".(time()-60*60*24*7)."') ORDER BY `data` DESC");
	if ($data["total"]==0) { $articlesold='Здесь нет статей'; } else { $articlesold="<table>"; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $articlesold.="<tr>"; $d=ToRusData($ar["data"]);
		$articlesold.="<td class='name'><a href='/post/view/".$ar["id"]."' target='_blank'>".$ar["name"]."</a><br><data>ID=".$ar["id"]." / Одобрено: $d[0] / Статус: заработок остановлен</data></td>"; $articlesold.="<td class='act2'>Показов: ".$ar["seen"]."</td><td class='act2'>Сумма: ".round($ar["seen"]*$stavka, 1)." руб.</td>";
	$articlesold.="</tr>"; endfor; $articlesold.="</table>"; }
	# ВЫВОД СТАТИСТИКИ
	$text.="<a id='active' name='active'></a>".$C10."<h2 style='float:left;'>Опубликовано за последние 7 дней</h2><div class='AddArt'><a href='/users/add'>Добавить пост</a></div>".$C5."<div class='RoundArts'>".$articlesok."</div>";
	$text.="<span style='font-size:11px; line-height:14px;'>Вся сумма, за показы в течение недели, будет зачислена на ваш баланс через 7 дней после публикации вашей статьи модератором. Внимание, отображение данных идет с задержкой, сумма вывода считается точно на 8 день и, обычно, превышает отображаемую сумму в настоящее время.</span>";
	$text.="<a id='moderate' name='moderate'></a>".$C10."<h2 style='float:left;'>Статьи на проверке модератором</h2>".$C5."<div class='RoundArts'>".$articlesno."</div>";
	$text.="<a id='old' name='old'></a>".$C10."<h2 style='float:left;'>Опубликовано более 7 дней назад</h2>".$C5."<div class='RoundArts'>".$articlesold."</div>";
	$text.="<a id='chernovik' name='chernovik'></a>".$C10."<h2 style='float:left;'>Черновики</h2>".$C5."<div class='RoundArts'>".$articleschern."</div>";
	return(array($text, $cap)); 
}


#############################################################################################################################################
#############################################################################################################################################
#############################################################################################################################################

function GetUsersMsg() {
	global $VARS, $GLOBAL, $dir, $Page, $node, $table, $RealPage, $RealHost, $C10, $C15, $C5, $C, $C20, $ROOT; $USER=$GLOBAL["USER"];
	$cap="Сообщения"; if ((int)$USER["id"]==0) { @header("location: /users/auth"); exit(); } $articlesok='';
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	if (isset($_SESSION['Data']["authbtn"])) { $P=$_SESSION['Data']; $msgtoadmin=trim(htmlspecialchars(strip_tags(str_replace(array("'"),"\'",$P["msgtoadmin"])))); if ($msgtoadmin!="") {
	DB("INSERT INTO `_usersmess` (`withuid`,`fromorto`,`text`,`data`,`ip`) VALUES ('".(int)$USER["id"]."','1','".$msgtoadmin."','".time()."','".$GLOBAL["ip"]."')"); $_SESSION["msg"]='<div class="SuccessDiv">Ваше сообщение успешно отправлено</div>'; } SD(); @header("location: /".$RealPage); exit(); }
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
		### Отправка сообщения
		$newmess=$C."<div id='newmess' class='RoundArts' style='margin:10px 0 20px 0; display:none;'><textarea class='FullArea' name='msgtoadmin' placeholder='Введите текст сообщения. Если сообщение касается какой-либо статьи, укажите её ID и заголовок'></textarea><input type='submit' name='authbtn' value='Отправить' class='SendInp'></div>";
		$text.='<form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post">'."<div class='AddArt'><a href='javascript:void(0);' onclick=\"$('#newmess').toggle('normal');\">Отправить сообщение администрации</a></div>".$C.$_SESSION["msg"].$newmess.$C10."</form>";
		### Список сообщений
		$moth=time()-31*24*60*60; $data=DB("SELECT * FROM `_usersmess` WHERE (`withuid`='".(int)$USER["id"]."' && `data`>'".$moth."') ORDER BY `data` DESC");
		if ($data["total"]>0) { for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]); 
			if ($ar["fromorto"]==1) { $name=$USER["nick"]; } else { $name="Администратор"; } if ($ar["fromorto"]==0 && $ar["readed"]==0) { $new="<b>Новое!</b> "; } else { $new=""; }
			$articlesok.="<div class='MsgFromTo".$ar["fromorto"]."'><i>".$new.$name.", ".$d[1]."</i>".$C5.nl2br($ar["text"])."</div>".$C5;
			DB("UPDATE `_usersmess` SET `readed`=1 WHERE (`withuid`='".(int)$USER["id"]."' && `fromorto`=0)");
		endfor; } if ($articlesok=="") { $articlesok="Нет сообщений";  } $text.="<div class='RoundArts'>".$articlesok."</div>";
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	$_SESSION["msg"]=''; return(array($text, $cap));	
}

#############################################################################################################################################
#############################################################################################################################################
#############################################################################################################################################

function GetUsersAdd() {
	global $VARS, $GLOBAL, $dir, $Page, $node, $table, $RealPage, $RealHost, $C10, $C15, $C5, $C, $C20, $C30, $ROOT; $USER=$GLOBAL["USER"];
	$cap="Добавление публикации"; if ((int)$USER["id"]==0) { @header("location: /users/auth"); exit(); } $articlesok=''; $Data=$_SESSION["Data"];
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	if (isset($Data["authbtn"])) {
		$Data["newname"] = htmlspecialchars(strip_tags(str_replace("'","\'",$Data["newname"])));
		$Data["newlid"] = htmlspecialchars(strip_tags(str_replace("'","\'",$Data["newlid"])));
		$Data["newkw"] = htmlspecialchars(strip_tags(str_replace("'","\'",$Data["newkw"])));
		$Data["newds"] = htmlspecialchars(strip_tags(str_replace("'","\'",$Data["newds"])));
		$Data["newcontent"] = strip_tags($Data["newcontent"], "<p><br><li><ul><ol><hr><i><strike>");
		$Data["mainpicfile"] = preg_replace('/[^a-zA-Z0-9_\.\-]+/i', '', $Data["mainpicfile"]);
		$Data["mainpicxy"] = preg_replace('/[^a-zA-Z0-9_;,=\.\-]+/i', '', $Data["mainpicxy"]);
		$Data["mainpicauth"] = htmlspecialchars(strip_tags(str_replace("'","\'",$Data["mainpicauth"]))); 
		$Data["newvideo"] = htmlspecialchars(strip_tags(str_replace("'","\'",$Data["newvideo"])));
		if ((int)$Data["autosend"]==1) { $stat="1"; } else { $stat="0"; }
		$q="INSERT INTO `post_users` (`stat`,`uid`,`data`,`name`,`lid`,`kw`,`ds`,`text`,`pic`,`picxy`,`picauth`,`video`) VALUES ('".$stat."','".$USER["id"]."','".time()."','".$Data["newname"]."','".$Data["newlid"]."','".$Data["newkw"]."','".$Data["newds"]."','".$Data["newcontent"]."','".$Data["mainpicfile"]."','".$Data["mainpicxy"]."','".$Data["mainpicauth"]."','".$Data["newvideo"]."')"; DB($q); $newid=DBL();
		$ROOT=$_SERVER['DOCUMENT_ROOT']; @require_once $_SERVER['DOCUMENT_ROOT'].'/modules/standart/ImageResizeCrop.php';
		foreach($Data["albumpic"] as $key=>$picpath) {
			$picpath = preg_replace('/[^a-zA-Z0-9_\.\-]+/i', '', $picpath); $Data["albumname"][$key]=htmlspecialchars(strip_tags(str_replace("'","\'",$Data["albumname"][$key])));
			$Data["albumtext"][$key]=nl2br(htmlspecialchars(strip_tags(str_replace("'","\'",$Data["albumtext"][$key])))); $Data["albumauth"][$key]=htmlspecialchars(strip_tags(str_replace("'","\'",$Data["albumauth"][$key])));
			foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { $picname=$picpath; if (!is_dir($ROOT."/userfiles/".$path)) { mkdir($ROOT."/userfiles/".$path, 0777); } list($w,$h)=getimagesize($ROOT."/userfiles/temp/".$picname); list($sw, $sh)=explode("-", $size);
			if($path=="picpreview") { resize($ROOT."/userfiles/temp/".$picname, $ROOT."/userfiles/".$path."/".$picname, $sw, $sh);
			} else if($path=="picoriginal"){ if($w > $sw) { resize($ROOT."/userfiles/temp/".$picname, $ROOT."/userfiles/".$path."/".$picname, $sw, $sh); } else { copy($ROOT."/userfiles/temp/".$picname, $ROOT."/userfiles/".$path."/".$picname); }
			} else { $k = min($w / $sw, $h / $sh); $x = round(($w - $sw * $k) / 2); $y = round(($h - $sh * $k) / 2); crop($ROOT."/userfiles/temp/".$picname, $ROOT."/userfiles/".$path."/".$picname, array($x, $y, round($sw * $k), round($sh * $k))); resize($ROOT."/userfiles/".$path."/".$picname, $ROOT."/userfiles/".$path."/".$picname, $sw, $sh); }
		} DB("INSERT INTO `admin_bubr`.`_widget_pics` (`pid`,`link`,`pic`,`name`,`text`,`author`,`data`,`point`,`sets`) VALUES ('".$newid."','userslenta','".$picpath."','".$Data["albumname"][$key]."','".$Data["albumtext"][$key]."','".$Data["albumauth"][$key]."','".time()."','report','1')");
		  DB("UPDATE `_widget_pics` SET `rate`=`id` WHERE (`link`='userslenta' && `pid`='".$newid."')"); } if ($stat==1) { SD(); header("location: /users/list"); exit(); }
	$_SESSION["msg"]="<div id='ShowSuccess'>Статья #".$newid." добавлена в черновики, <a href='/users/list#chernovik'>перейдите в кабинет</a> и отправьте её на модерацию</div>"; SD(); }
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	$text="<a id='ShowErrorA' name='ShowErrorA'></a><div class='RoundArts'>При добавлении новой статьи, вы принимаете и соглашаетесь с документами: «<a href='/rules' target='_blank'>Правила публикации</a>» и «<a href='/agreement' target='_blank'>Пользовательское соглашение</a>». Если вы не согласны с какими-либо пунктами - откажитесь от добавления материала.</div>".$C10;
	$text.=$_SESSION["msg"].'<div id="ShowError"></div><form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post" onsubmit="return VerifyFormAdd();">';
	$text.='<h2">Основные настройки статьи</h2>'.$C10; $text.="<input type='text' name='newname' id='newname' placeholder='Заголовок статьи' class='UserInpt'>".$C5;
	$text.="<input type='text' name='newds' id='newds' placeholder='Короткое описание (Description)' class='UserInpt'>".$C5;
	$text.="<input type='text' name='newkw' id='newkw' placeholder='3-5 ключевых слов через запятую (Keywords)' class='UserInpt'>".$C5;
	$text.='<textarea id="newlid" name="newlid" class="UserArea" style="height:40px;" placeholder="Лид статьи (вступление)"></textarea>'.$C5;
	$text.='<h2>Основное содержание статьи</h2><script src="/modules/standart/wysiwyg/editor.js"></script><script src="/modules/standart/wysiwyg/ru.js"></script><link rel="stylesheet" href="/modules/standart/wysiwyg/editor.css" />
	<script>$(document).ready(function() { var buttons=["html","|","bold","italic","deleted","|","unorderedlist","orderedlist","outdent","indent","|","alignment","|","horizontalrule"];
	var tags=["span", "br", "p", "b", "i", "strike", "u", "blockquote", "ul", "ol", "li", "hr", "strong", "h3"]; $("#newcontent").redactor({focus:true, lang:"ru", buttons:buttons, allowedTags:tags, autoresize:false}); })</script>
	'.$C10.'<textarea id="newcontent" name="newcontent" class="UserArea" style="height:400px;"></textarea>'.$C15;
	$text.='<h2>Главная фотография</h2>'.$C10."<div id='MainPicInfo'></div><div class='MainPic' id='MainPicT' style='width:100%;'><div id='MainPicI'></div><span class='Info'>Вы можете загрузить фотографию jpg, gif или png, объемом не более 5М</span>
	<div title='Нажмите для выбора файла' id='Podstava' class='Podstava3'><input type='file' id='uavatar' name='uavatar' accept='image/jpeg,image/gif,image/x-png' onChange='StartUploadMainPic();' /></div>
	<input type='hidden' id='mainpicfile' name='mainpicfile' value='' /><input type='hidden' id='mainpicxy' name='mainpicxy' value='0' />
	<input type='text' id='mainpicauth' name='mainpicauth' value='' class='UserInpt' placeholder='Введите автора фото или ссылку на источник' style='margin-top:20px; width:62%; display:none;' /></div>";
	$text.='<h2>Фотоальбом публикации</h2><i style="font-family:Arial;">Здесь вы можете загружать подборки фотографий и дополнять их текстами</i>'.$C5;
	$text.='<link href="/modules/standart/multiupload/client/uploader2.css" type="text/css" rel="stylesheet"><script type="text/javascript" src="/modules/standart/multiupload/client/uploader.js"></script>';
	$text.=$C10.'<div id="uploadercom"></div><div id="uploadercompics"></div>'.$C20; 
	$text.='<h2>Вставка видео с YouTube</h2>'.$C10."<input type='text' name='newvideo' id='newvideo' placeholder='Ссылка на видео YouTube, пример: www.youtube.com/embed/vlYKV7rYwcY' class='UserInpt'>".$C10;
	$text.='<input type="checkbox" value="1" name="autosend"> Отправить статью на модерацию (иначе она будет сохранена в черновиках)';
	$text.=$C20.'<input type="submit" name="authbtn" value="Добавить публикацию" class="SendInp" formaction="/modules/SubmitForm.php?bp='.$RealPage.'"></form>';
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	$_SESSION["msg"]=''; return(array($text, $cap));	
}

#############################################################################################################################################
#############################################################################################################################################
#############################################################################################################################################

function GetUsersEdit() {
	global $VARS, $GLOBAL, $dir, $Page, $node, $table, $page, $RealPage, $RealHost, $C10, $C15, $C5, $C, $C20, $C30, $ROOT; $USER=$GLOBAL["USER"];
	$cap="Редактирование публикации"; if ((int)$USER["id"]==0) { @header("location: /users/auth"); exit(); } $articlesok=''; $Data=$_SESSION["Data"]; $page=(int)$page;
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	if (isset($Data["authbtn"])) { 
		$Data["newname"] = htmlspecialchars(strip_tags(str_replace("'","\'",$Data["newname"])));
		$Data["newlid"] = htmlspecialchars(strip_tags(str_replace("'","\'",$Data["newlid"])));
		$Data["newkw"] = htmlspecialchars(strip_tags(str_replace("'","\'",$Data["newkw"])));
		$Data["newds"] = htmlspecialchars(strip_tags(str_replace("'","\'",$Data["newds"])));
		$Data["newcontent"] = strip_tags($Data["newcontent"], "<p><br><li><ul><ol><hr><i><strike>");
		$Data["mainpicfile"] = preg_replace('/[^a-zA-Z0-9_\.\-]+/i', '', $Data["mainpicfile"]);
		$Data["mainpicxy"] = preg_replace('/[^a-zA-Z0-9_;,=\.\-]+/i', '', $Data["mainpicxy"]);
		$Data["mainpicauth"] = htmlspecialchars(strip_tags(str_replace("'","\'",$Data["mainpicauth"]))); 
		$Data["newvideo"] = htmlspecialchars(strip_tags(str_replace("'","\'",$Data["newvideo"])));
		if ((int)$Data["autosend"]==1) { $stat="1"; } else { $stat="0"; }
		$q="UPDATE `post_users` SET `stat`='".$stat."',`data`='".time()."',`name`='".$Data["newname"]."',`lid`='".$Data["newlid"]."',`kw`='".$Data["newkw"]."',`ds`='".$Data["newds"]."',`text`='".$Data["newcontent"]."',`pic`='".$Data["mainpicfile"]."',`picxy`='".$Data["mainpicxy"]."',`picauth`='".$Data["mainpicauth"]."',`video`='".$Data["newvideo"]."' WHERE (`id`='".$page."')"; DB($q);
		$ROOT=$_SERVER['DOCUMENT_ROOT']; @require_once $_SERVER['DOCUMENT_ROOT'].'/modules/standart/ImageResizeCrop.php';
		DB("DELETE FROM `_widget_pics` WHERE (`pid`='".$page."' && `link`='userslenta')");
		foreach($Data["albumpic"] as $key=>$picpath) {
			$picpath = preg_replace('/[^a-zA-Z0-9_\.\-]+/i', '', $picpath); $Data["albumname"][$key]=htmlspecialchars(strip_tags(str_replace("'","\'",$Data["albumname"][$key])));
			$Data["albumtext"][$key]=nl2br(htmlspecialchars(strip_tags(str_replace("'","\'",$Data["albumtext"][$key])))); $Data["albumauth"][$key]=htmlspecialchars(strip_tags(str_replace("'","\'",$Data["albumauth"][$key])));
			foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { $picname=$picpath; if (!is_dir($ROOT."/userfiles/".$path)) { mkdir($ROOT."/userfiles/".$path, 0777); } list($w,$h)=getimagesize($ROOT."/userfiles/temp/".$picname); list($sw, $sh)=explode("-", $size);
			if($path=="picpreview") { resize($ROOT."/userfiles/temp/".$picname, $ROOT."/userfiles/".$path."/".$picname, $sw, $sh);
			} else if($path=="picoriginal"){ if($w > $sw) { resize($ROOT."/userfiles/temp/".$picname, $ROOT."/userfiles/".$path."/".$picname, $sw, $sh); } else { copy($ROOT."/userfiles/temp/".$picname, $ROOT."/userfiles/".$path."/".$picname); }
			} else { $k = min($w / $sw, $h / $sh); $x = round(($w - $sw * $k) / 2); $y = round(($h - $sh * $k) / 2); crop($ROOT."/userfiles/temp/".$picname, $ROOT."/userfiles/".$path."/".$picname, array($x, $y, round($sw * $k), round($sh * $k))); resize($ROOT."/userfiles/".$path."/".$picname, $ROOT."/userfiles/".$path."/".$picname, $sw, $sh); }
		}
		DB("INSERT INTO `_widget_pics` (`pid`,`link`,`pic`,`name`,`text`,`author`,`data`,`point`,`sets`) VALUES ('".$page."','userslenta','".$picpath."','".$Data["albumname"][$key]."','".$Data["albumtext"][$key]."','".$Data["albumauth"][$key]."','".time()."','report','1')");
		DB("UPDATE `_widget_pics` SET `rate`=`id` WHERE (`link`='userslenta' && `pid`='".$page."')"); } if ($stat==1) { SD(); header("location: /users/list"); exit(); }
	$_SESSION["msg"]="<div id='ShowSuccess'>Статья #".$page." сохранена, <a href='/users/list#chernovik'>перейдите в кабинет</a> и отправьте её на модерацию</div>"; SD(); }
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	$data=DB("SELECT * FROM `post_users` WHERE (`id`='".$page."') LIMIT 1"); if ($data["total"]==0) { return(array("Публикация не найдена", "Ошибка")); } @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); 
	if ($ar["stat"]==1 || $ar["stat"]==2) { return(array("Запрещено редактировать эту публикацию", "Ошибка")); } if ($ar["uid"]!=(int)$USER["id"] && (int)$USER["role"]<2) { return(array("В доступе отказано", "Ошибка")); }
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	$text="<a id='ShowErrorA' name='ShowErrorA'></a><div class='RoundArts'>При добавлении новой статьи, вы принимаете и соглашаетесь с документами: «<a href='/rules' target='_blank'>Правила публикации</a>» и «<a href='/agreement' target='_blank'>Пользовательское соглашение</a>». <span style='color:red;'>Внимание!</span> После редактирования публикации, она автоматически снимается с проверки модератором, необходимо ещё раз отправить на модерацию вручную. <a href='/users/list'>Перейти к списку моих статей</a>.</div>".$C10;
	$text.=$_SESSION["msg"].'<div id="ShowError"></div><form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post" onsubmit="return VerifyFormAdd();">';
	$text.='<h2 style="float:left">Основные настройки статьи</h2><h2 style="float:right;"><a href="/users/confirmdel/'.$page.'" style="font-size:16px; color:red;">Удалить статью</a></h2>'.$C10;
	$text.="<input type='text' name='newname' id='newname' placeholder='Заголовок статьи' class='UserInpt' value='".$ar["name"]."'>".$C5;
	$text.="<input type='text' name='newds' id='newds' placeholder='Короткое описание (Description)' class='UserInpt' value='".$ar["ds"]."'>".$C5;
	$text.="<input type='text' name='newkw' id='newkw' placeholder='3-5 ключевых слов через запятую (Keywords)' class='UserInpt' value='".$ar["kw"]."'>".$C5;
	$text.='<textarea id="newlid" name="newlid" class="UserArea" style="height:40px;" placeholder="Лид статьи (вступление)">'.$ar["lid"].'</textarea>'.$C5;
	$text.='<h2>Основное содержание статьи</h2><script src="/modules/standart/wysiwyg/editor.js"></script><script src="/modules/standart/wysiwyg/ru.js"></script><link rel="stylesheet" href="/modules/standart/wysiwyg/editor.css" />
	<script>$(document).ready(function() { var buttons=["html","|","bold","italic","deleted","|","unorderedlist","orderedlist","outdent","indent","|","alignment","|","horizontalrule"];
	var tags=["span", "br", "p", "b", "i", "strike", "u", "blockquote", "ul", "ol", "li", "hr", "strong", "h3"]; $("#newcontent").redactor({focus:true, lang:"ru", buttons:buttons, allowedTags:tags, autoresize:false}); })</script>
	'.$C10.'<textarea id="newcontent" name="newcontent" class="UserArea" style="height:400px;">'.$ar["text"].'</textarea>'.$C15;
	$text.='<h2>Главная фотография</h2>'.$C10."<div id='MainPicInfo'></div><div class='MainPic' id='MainPicT' style='width:100%;'><div id='MainPicI'><img src='/userfiles/picsquare/".$ar["pic"]."'></div>
	<span class='Info'>Вы можете загрузить фотографию jpg, gif или png, объемом не более 5М</span>
	<div title='Нажмите для выбора файла' id='Podstava' class='Podstava3'><input type='file' id='uavatar' name='uavatar' accept='image/jpeg,image/gif,image/x-png' onChange='StartUploadMainPic();' /></div>
	<input type='hidden' id='mainpicfile' name='mainpicfile' value='".$ar["pic"]."' /><input type='hidden' id='mainpicxy' name='mainpicxy' value='".$ar["picxy"]."' />
	<input type='text' id='mainpicauth' name='mainpicauth' value='".$ar["picauth"]."' class='UserInpt' placeholder='Введите автора фото или ссылку на источник' style='margin-top:20px; width:62%;' /></div>";
	$datap=DB("SELECT * FROM `_widget_pics` WHERE (`pid`='".$page."' && `link`='userslenta') ORDER BY `id` ASC"); $pics='';
	for ($i=0; $i<$datap["total"]; $i++) { @mysql_data_seek($datap["result"], $i); $ap=@mysql_fetch_array($datap["result"]);
		$pics.="<div class='AddAlbumPic' id='divalbumpic".$i."'><input type='hidden' name='albumpic[".$i."]' value='".$ap["pic"]."'>";
		$pics.="<div class='Photo'><img src='/userfiles/temp/s-".$ap["pic"]."' /><a href='javascript:void(0);' onclick=\"DeleteAlbumPic(".$i.");\" class='DelPic'>Удалить фото</a></div><div class='Areas'>";
		$pics.="<input type='text' name='albumname[".$i."]' placeholder='Введите название фотографии' class='UserInpt' style='margin-bottom:10px; width:96%;' value='".$ap["name"]."'>";
		$pics.="<input type='text' name='albumauth[".$i."]' placeholder='Введите автора фото или ссылку на источник' class='UserInpt' style='margin-bottom:10px; width:96%;' value='".$ap["author"]."'>";
		$pics.="<textarea name='albumtext[".$i."]' class='UserArea' style='height:120px; width:96%;' placeholder='Описание фотографии или просто текст'>".$ap["text"]."</textarea></div></div>";
	}
	$text.='<h2>Фотоальбом публикации</h2><i style="font-family:Arial;">Здесь вы можете загружать подборки фотографий и дополнять их текстами</i>'.$C5;
	$text.='<link href="/modules/standart/multiupload/client/uploader2.css" type="text/css" rel="stylesheet"><script type="text/javascript" src="/modules/standart/multiupload/client/uploader.js"></script>';
	$text.=$C10.'<div id="uploadercom"></div><div id="uploadercompics">'.$pics.'</div>'.$C20;
	$text.='<h2>Вставка видео с YouTube</h2>'.$C10."<input type='text' name='newvideo' id='newvideo' value='".$ar["video"]."'  placeholder='Ссылка на видео YouTube, пример: www.youtube.com/embed/vlYKV7rYwcY' class='UserInpt'>".$C10;
	$text.='<input type="checkbox" value="1" name="autosend"> Отправить статью на модерацию (иначе она будет сохранена в черновиках)';
	$text.=$C20.'<input type="submit" name="authbtn" value="Сохранить публикацию" class="SendInp" formaction="/modules/SubmitForm.php?bp='.$RealPage.'"></form>';
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	$_SESSION["msg"]=''; return(array($text, $cap));	
}

#############################################################################################################################################
#############################################################################################################################################
#############################################################################################################################################

function GetUsersId() {
	global $VARS, $GLOBAL, $ROOT, $dir, $Page, $node, $table, $table2, $C, $C10, $C15, $C25, $C5;
	$data=DB("SELECT `".$table."`.*  FROM `".$table."` WHERE (`id`='".(int)$dir[2]."') LIMIT 1");
	if ($data["total"]==0) { $text=@file_get_contents($ROOT."/template/404.html"); $cap="Пользователь не найден"; $Page404=1;
	} else {
		@mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); 
		$cap=$ar["nick"]; $d=ToRusData($ar["created"]);
		if ((int)$ar["lasttime"]!=0) { if ((time()-$ar["lasttime"])>600) { $l=ToRusData($ar["lasttime"]); } else {$l[1]="<b style='color:green;'>Сейчас на сайте<b>"; }} else { $l[1]="<i style='color:red;'>нет</i>"; }
		$text.="<div class='WhiteBlock'><table width=100%>";
		$text.="<tr>"; if ($ar["avatar"]!="" && is_file($ROOT."/".$ar["avatar"]) && filesize($ROOT."/".$ar["avatar"])>100) { $text.="<td rowspan=4 valign=top class='UserAvatar'><img src='/".$ar["avatar"]."' /></td>"; }
		$text.="<td width=50%><b>Аккаунт:</b> "; if ($ar["stat"]==1) { $text.="Активен"; } else { $text.="Заблокирован"; }
		$text.="</td><td></td></tr>"; 
		//$text.="</td><td width=50%><b>Статус:</b> ".$GLOBAL["roles"][$ar["role"]]."</td></tr>";
		$text.="<tr><td><b>Регистрация:</b> ".$d[1]."</td><td></td></tr>";
		//$text.="<tr><td><b>Рейтинг:</b> ".$ar["karma"]."</td>";
		$text.="</tr><tr><td colspan=2></td></tr>";
		$text.="</table></div>";		
		#### Слежение за комментариями
		$item=array(); $moth2ago=time()-60*60*24*30*1; $data=DB("SELECT `_tracker`.`link`, `_tracker`.`pid` FROM `_tracker` WHERE (`_tracker`.`uid`='".(int)$dir[2]."' && `data`>'".$moth2ago."') ORDER BY `_tracker`.`data` DESC");
		if ($data["total"]>0) { $text.=$C10."<h3>".$ar["nick"]." следит за темами:</h3>".$C10; for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $id=$ar["pid"]; $link=$ar["link"]; $themes[$link][$id]=$ar; }
		### Составляем список по всем таблицам
		$ids=array(); foreach ($themes as $link=>$ar) { foreach ($ar as $key=>$val) { $ids[$link].=$key.","; } $ids[$link]=trim($ids[$link],","); }
		### Выбираем из базы данных
		$news=array(); foreach ($ids as $link=>$pids) { $tab=$link."_lenta"; $data=DB("SELECT `$tab`.`id`, `$tab`.`name`, `$tab`.`comcount`, (select `_comments`.`data` from `_comments` WHERE (`_comments`.`pid`=`$tab`.`id` AND `_comments`.`link`='$link' AND `_comments`.`pid` IN ($pids)) ORDER BY `_comments`.`data` DESC LIMIT 1) as `data` 	, (select concat_ws('|', `_comments`.`uid`, `_users`.`nick`) from `_comments` LEFT JOIN `_users` ON `_comments`.`uid`=`_users`.`id`  WHERE (`_comments`.`pid`=`$tab`.`id` AND `_comments`.`link`='$link' AND `_comments`.`pid` IN ($pids)) ORDER BY `_comments`.`data` DESC  LIMIT 1) as `user`
		FROM `$tab` WHERE (`$tab`.`id` IN ($pids) && `$tab`.`comcount`>0) GROUP BY 1 LIMIT 50"); for($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $ar["link"]=$link; $news[]=$ar; }} $ars=array(); foreach($news as $key=>$arr){ $ars[$key]=$arr['data']; } array_multisort($ars, SORT_DESC, $news);
		$text.="<table class='RefreshTable'><tr class='TableHead'><td>Публикация</td><td>Ответов</td><td>Обновлено</td></tr>";
		$j=0; foreach($news as $i=>$item) { $j++; $link=$item["link"]; $id=$item["id"]; $path=""; $clas="tdrow".$j%2; $d=ToRusData($item["data"]); list($item["uid"], $item["nick"])=explode("|", $item["user"]); $path="http://".$VARS["mdomain"]."/".$link."/view/".$id."#comments";
		if ($item["uid"]!=0 && $item["nick"]!="") { $user="<a href='/users/view/$item[uid]/'><u>".$item["nick"]."</u></a>"; } else { $user="Гость сайта"; }
		$text.="<tr class='".$clas."'><td><a href='".$path."'>".$item["name"]."</a></td><td class='Data' width='1%' align='center'><a href='".$path."'><u>".$item["comcount"]."</u></a></td><td class='Data' width='1%'>".$user."<br>".$d[4]."</td></tr>";
		} if ($j==0) { $text.="<tr><td colspan=2><i></i></td></tr>";} $text.="</table>".$C5."<div class='Info'>Отображаются публикации, где пользователь оставлял комментарии за последний месяц</div>"; }
	}
	return(array($text, $cap));
}

#############################################################################################################################################
#############################################################################################################################################
#############################################################################################################################################

function GetUsersArticle() {
	global $VARS, $GLOBAL, $dir, $Page, $node, $table, $page, $RealPage, $RealHost, $C10, $C15, $C5, $C, $C20, $C30, $ROOT, $CSSmodules; $USER=$GLOBAL["USER"];
	if ((int)$USER["id"]==0) { @header("location: /users/auth"); exit(); } $articlesok=''; $Data=$_SESSION["Data"]; $page=(int)$page;
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	$data=DB("SELECT `post_users`.*,`_users`.`avatar`,`_users`.`nick`  FROM `post_users` LEFT JOIN `_users` ON `_users`.`id`=`post_users`.`uid` WHERE (`post_users`.`id`='".$page."') GROUP BY 1 LIMIT 1");
	if ($data["total"]==0) { return(array("Публикация не найдена", "Ошибка")); } @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); 
	if ($ar["uid"]!=(int)$USER["id"] && (int)$USER["role"]<2) { return(array("В доступе отказано", "Ошибка")); } $item=$ar;
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	$Page["Description"]=$item["ds"]; $Page["KeyWords"]=$item["kw"]; $cap="Предварительный просмотр статьи"; $CSSmodules["Просмотр статьи"]="/modules/lenta/lenta.css";
	if ($ar["stat"]==0) { $admin.=$C15."<hr>".$C5."<a href='/users/moderate/".$page."' class='Yes'>Отправить на модерацию</a><a href='/users/edit/".$page."' class='No'>Редактировать</a>".$C5."<hr>".$C10; }
	if ($ar["stat"]==1) { $admin.=$C15."<hr>".$C5."<a href='/users/escapemoderate/".$page."' class='Yes'>Статья находится на модерации. Отменить?</a>".$C5."<hr>".$C10; }
	if ($ar["stat"]==2) { $admin.=$C15."<hr>".$C5."Эта статья прошла модерацию и опубликована на сайте. <a href='/post/view/".$ar["lentaid"]."' target='_blank'>Перейти на опубликованную версию</a><hr>".$C10; }
	$admin.="<h1>".$item["name"]."</h1>";
	if ($item["pic"]!="") { $ar["author"]=""; $ar["authorlink"]=$item["picauth"];
		if ($ar["author"]!="" || $ar["authorlink"]!="") {
			$ar["author"]=str_replace("http://", '', trim($ar["author"], "./")); $ar["authorlink"]=str_replace("http://", '', trim($ar["authorlink"], "./")); if ($ar["author"]=="") { $tmp1=explode("/", $ar["authorlink"]); $tmp2=explode("?", $tmp1[0]); $ar["author"]=$tmp2[0]; }
			$imgauth=$ar["author"]; if ($ar["authorlink"]!="" && strpos($item["picauth"], "http:")!==false) { $imgauth="<a href='http://".$ar["authorlink"]."' target='_blank' rel='nofollow'>".$imgauth."</a>"; } $imgauth="<auth><span>Автор: ".$imgauth."</span></auth>";
		} $pic="<div class='PicItem' title='$cap'>";  $path='/userfiles/picoriginal/'.$item["pic"]; $pic.="<img src='".$path."' title='$cap' alt='$cap' />";
		if ($item["cens"]!="") { $pic.="<div class='Cens'>".$item["cens"]."</div>"; } if ($item["picauth"]!="") { $pic.="<div class='PicAuth'>".$imgauth."</div>"; } $pic.="</div>".$C20;
	}
	if ($item["lid"]!="") { $lid="<div class='ItemLid'><div class='Br'></div>".$C10.$item["lid"].$C10."<div class='Br'></div></div>".$C15; } $maintext=CutEmptyTags($item["text"]).$C15;
	$p=DB("SELECT * FROM `_widget_pics` WHERE (`pid`='".(int)$dir[2]."' && `link`='userslenta' && `point`='report' && `stat`=1) order by `rate` ASC"); $report=''; $marker=1; 
	if ($p["total"]>0) { for ($i=0; $i<$p["total"]; $i++): mysql_data_seek($p["result"],$i); $ar=@mysql_fetch_array($p["result"]);
	$alttitle=$item["name"]; $imgauth=""; if ($ar["name"]!="") { $alttitle=$ar["name"]; } $ar["sets"]=1; 
		if ($ar["author"]!="" || $ar["authorlink"]!="") {
			$ar["author"]=str_replace("http://", '', trim($ar["author"], "./")); $ar["authorlink"]=str_replace("http://", '', trim($ar["authorlink"], "./")); if ($ar["author"]=="") { $tmp1=explode("/", $ar["authorlink"]); $tmp2=explode("?", $tmp1[0]); $ar["author"]=$tmp2[0]; }
			$imgauth=$ar["author"]; if ($ar["authorlink"]!="") { $imgauth="<a href='http://".$ar["authorlink"]."' target='_blank' rel='nofollow'>".$imgauth."</a>"; } $imgauth="<auth><span>Автор: ".$imgauth."</span></auth>";
		} 
		if ($ar["sets"]==0) { $img="<div class='ReportPicSmall'><a href='/userfiles/picoriginal/".$ar["pic"]."' title='".$alttitle."' rel='prettyPhoto[gallery]'><img src='/userfiles/picoriginal/".$ar["pic"]."' title='".$alttitle."' alt='".$alttitle."'></a>".$imgauth."</div>";
		} else { $img="<div class='ReportPicBig'><a href='/userfiles/picoriginal/".$ar["pic"]."' title='".$alttitle."' rel='prettyPhoto[gallery]'><img src='/userfiles/picoriginal/".$ar["pic"]."' title='".$alttitle."' alt='".$alttitle."'></a>".$imgauth."</div>"; }
	 	if ($ar["mark"]==1) { # МАРКЕР ФОТО	
			$report.="<div class='Pointer'>".($marker)."</div><div class='PointerText'>"; if ($ar["name"]!="" && $ar["showname"]!=0) { $report.="<h2>".$ar["name"]."</h2>"; }
			if ($ar["sets"]==0) { $report.=$img; } if ($ar["text"]) { $report.=$ar["text"]; } $report.="</div>".$C;	if ($ar["sets"]==1) { $report.=$img; } $marker++;
	 	} else { # ФОТО ОТЧЕТ
	 	 	if ($ar["name"]!="" && $ar["showname"]!=0) { $report.="<h2>".$ar["name"]."</h2>"; } $report.=$img; if ($ar["text"]!="") { $report.=$ar["text"]; } 
		}
	$report.=$C15; endfor; }
	$video=""; if ($item["video"]!="") { $vid=GetNormalVideo('<iframe width="853" height="480" src="http://'.str_replace(array('watch?v=','http://',"//"), array('embed/','','') ,$item["video"]).'" frameborder="0" allowfullscreen></iframe>'); $video.=$C15.$vid.$C15; }
	if ($item["avatar"]=="" || !is_file($ROOT."/".$item["avatar"]) || filesize($ROOT."/".$item["avatar"])<100) { $avatar="<img src='/userfiles/avatar/no_photo.jpg'>"; } else { $avatar="<img src='/".$item["avatar"]."'>"; }
	$d=ToRusData($item["data"]); if ($item["uid"]!=0 && $item["nick"]!="") { $auth=$avatar."Автор: <a href='http://".$VARS["mdomain"]."/users/view/".$item["uid"]."/'>".$item["nick"]."</a>, ".$d[1]; } else { $auth="<img src='/userfiles/avatar/no_photo.jpg' />Автор: Народный корреспондент, ".$d[1]; }
	$mixblock.="<div class='ItemAuth'>".$auth."</div>";	$text=$admin.$pic."<div class='ArticleContent'>".$lid.$maintext.$report.$video.$mixblock."</div>"; $_SESSION["msg"]=''; return(array($text, $cap));	
}

#############################################################################################################################################
#############################################################################################################################################
#############################################################################################################################################



?>