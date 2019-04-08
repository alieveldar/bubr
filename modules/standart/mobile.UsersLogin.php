<?
	$UserAuthLine=""; $UserAuthBox=""; 
	if (!isset($_SESSION['userid'])) { $_SESSION['userid']=0; }
	
	//$_SESSION['userid']=0; 
	//unset($_SESSION["UserSetsSiteS"]);

	$GLOBAL["Providers"]="vkontakte,facebook,twitter,google,mailru,odnoklassniki,yandex";	
	$UserSetsSite=array();
	$mailtext="Требуется подтверждение электронного адреса";
	#0 Разрешить регистрацию пользователей		
	#1 Требовать подтверждение E-mail
	#2 Разрешить регистрацию через соц. сети
	#3 Разрешить комментарии к материалам
	#4 Разрешить комментарии от анонимов
	#5 Запрашивать у анонимов CAPTCHA
	#6 Разрешить подписи в комментариях
	#7 Разрешить вложения в комментариях материалов
	
	### Настройки пользователей
	if (!isset($_SESSION["UserSetsSiteS"])) { 
		$data=DB("SELECT `sets` FROM `_pages` WHERE (`module`='users') LIMIT 1");
		if ($data["total"]==1) {
			@mysql_data_seek($data["result"],0); $ar=@mysql_fetch_array($data["result"]);
			$_SESSION["UserSetsSiteS"]=$ar["sets"];
		}
	}
	$UserSetsSite=explode("|", $_SESSION["UserSetsSiteS"]);
	
	### Настройки форм: Авторизация
	# $FormsUserLogin="<h3>Авторизация по логину и паролю ".$VARS["mdomain"]."</h3>".$C5."<div class='UserLogin' id='UserForm1'><div id='UserLoginMsg'></div>";
	# $FormsUserLogin.="<input type='text' placeholder='Логин' id='UserLoginLogin' class='UserLoginInput' /><input id='UserLoginPassword' type='password' placeholder='Пароль' class='UserLoginInput' />";
	# $FormsUserLogin.="<a href='javascript:void(0);' onclick=UserLoginFunc('UserForm1');>Войти</a></div>";

	if ($UserSetsSite[2]==1 && !$_SESSION["userid"]) {
		$URL=rawurlencode("http://".$RealHost."/modules/standart/LoginSocial.php?back=http://".$RealHost."/".$RealPage);
		$FormsUserLogin.="<div id='uLogin1' x-ulogin-params='display=mobile&fields=first_name,last_name,photo&providers=".$GLOBAL["Providers"]."&redirect_uri=".$URL."'></div>";
		
		//$FormsUserLogin.='<a href="#" id="uLogin1" data-ulogin="display=window;fields=first_name,last_name,photo;providers='.$GLOBAL["Providers"].';redirect_uri='.$URL.'"><img src="http://ulogin.ru/img/button.png" width=160 height=25 alt="МультиВход"/></a>';
		//$FormsUserLogin.="<div class='C15'></div><div class='Info'><b style='color:red;'>Внимание!</b> Регистрируясь и совершая любые действия на сайте, вы автоматически соглашаетесь с <a href='/agreement'><u><b>Пользовательским соглашением</b></u></a>. Если вы не согласны с <a href='/agreement'>Cоглашением</a> или с отдельными его пунктами, пожалуйста, покиньте сайт.</div>";
		#$FormsUserLogin.="<div class='C15'></div><div class='Info'>Социальные сети не передают нам ваши логин и пароль, мы получаем только имя и аватар пользователя.</div>";
	}
	
	$FormsUserLogin.="<div class='C'></div>";

	### Данные пользователя
	if ($UserSetsSite[0]==1) {
		$GLOBAL["log"].="<i>Пользователи</i>: данный функционал включен - ";
		if ((int)$_SESSION['userid']==0) {
			### нет авторизации
			$GLOBAL["log"].="<b>нет авторизации</b><hr>";
			$UserAuthLine.="<a href='javascript:void(0);' onclick=\"$('#MobileForm').toggle();\">Войти</a>";
		} else {
			### авторизован
			$GLOBAL["log"].="<b>авторизован [".$_SESSION['userid']."]</b><hr>";
			$data=DB("SELECT * FROM `_users` WHERE (`id`='".(int)$_SESSION['userid']."') LIMIT 1");
			if ($data["total"]==1) {
				@mysql_data_seek($data["result"],0); $GLOBAL["USER"]=@mysql_fetch_array($data["result"]);
				if ($GLOBAL["USER"]["stat"]!=1) { @header("Location: /users/exit/"); exit(); }
		
				### Вывод информации
				$UserAuthLine.="<a href='/users/exit/'>".$GLOBAL["USER"]["nick"]." • Выход</a>";
				$data=DB("UPDATE `_users` SET `lasttime`='".time()."' WHERE (`id`='".(int)$_SESSION['userid']."') LIMIT 1");
			} else {
				$_SESSION['userid']=0; @header("Location: /users/lostid/"); exit();
			}
		}
	
		$Page["UserAuth"]=$UserAuthLine;
		$Page["UserForm"]=$FormsUserLogin;
		
	} else {
		$GLOBAL["log"].="<u>Пользователи</u>: данный функционал отключен<hr>";
	}

function GetUserAuthForm() {
	global $GLOBAL, $FormsUserLogin; $text=""; if ($GLOBAL["USER"]["id"]==0) { $text=$FormsUserLogin; } 
	$text=str_replace("UserForm1", "UserForm2", $text);
	$text=str_replace("uLogin1", "uLogin2", $text);
	return $text;		
}


?>