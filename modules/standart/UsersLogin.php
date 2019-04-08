<?
$UserAuthLine=""; $UserAuthBoxs=""; $UserSetsSite=array(); $GLOBAL["Providers"]="vkontakte,facebook,twitter,google,mailru,odnoklassniki,yandex"; if (!isset($_SESSION['userid'])) { $_SESSION['userid']=0; }

#0 Разрешить регистрацию пользователей	#1 Требовать подтверждение E-mail		#2 Разрешить регистрацию через соц. сети
#3 Разрешить комментарии к материалам	#4 Разрешить комментарии от анонимов	#5 Запрашивать у анонимов CAPTCHA
#6 Разрешить подписи в комментариях 	#7 Разрешить вложения в комментариях материалов

if (!isset($_SESSION["UserSetsSiteS"])) { $data=DB("SELECT `sets` FROM `_pages` WHERE (`module`='users') LIMIT 1"); if ($data["total"]==1)
{ @mysql_data_seek($data["result"],0); $ar=@mysql_fetch_array($data["result"]); $_SESSION["UserSetsSiteS"]=$ar["sets"]; }} $UserSetsSite=explode("|", $_SESSION["UserSetsSiteS"]);

if (!isset($_SESSION["Referer"])) { $_SESSION["Referer"]=$_SERVER['HTTP_REFERER']; }

### Данные пользователя
if ($UserSetsSite[0]==1) { $GLOBAL["log"].="<i>Пользователи</i>: функционал ВКЛЮЧЕН<hr>";
if ((int)$_COOKIE['usercookuid']!=0 && isset($_COOKIE['usercookmd5']) && (int)$_SESSION['userid']==0) {
	$ucuid=(int)$_COOKIE['usercookuid']; $ucmd5=preg_replace('/[^a-zA-Z0-9]+/i', '', $_COOKIE['usercookmd5']);
	$usdata=DB("SELECT `login`,`pass`,`created` FROM `_users` WHERE (`id`='".$ucuid."') LIMIT 1"); @mysql_data_seek($usdata["result"],0); $arus=@mysql_fetch_array($usdata["result"]);
	$dbmd5=md5($arus["login"]."-".$arus["pass"]."-".$arus["created"]."-".$RealHost);
	if ($dbmd5==$ucmd5) { $_SESSION['userid']=$ucuid; $GLOBAL["log"].="<b>COOKIES авторизация</b><hr>";
	setcookie('usercookuid', $ucuid, time()+60*60*24*30,"/"); setcookie('usercookmd5', $dbmd5, time()+60*60*24*30,"/");
	} else { setcookie('usercookuid','',time()-20000,"/"); setcookie('usercookmd5','',time()-20000,"/"); }
}
$GLOBAL["ulogins"]='<div id="uLogin" data-ulogin="display=panel;fields=first_name,last_name;providers='.$GLOBAL["Providers"].';hidden=;redirect_uri='.rawurlencode("http://".$RealHost."/modules/standart/LoginSocial.php?back=http://".$RealHost."/".$RealPage).'"></div>';
 
// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -
	if ((int)$_SESSION['userid']==0) {
		### НЕ АВТОРИЗОВАН --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
		$GLOBAL["log"].="<b>нет авторизации</b><hr>"; 

		//$JSmodules["Авторизация ULogin"]="http://ulogin.ru/js/ulogin.js";
		$UserAuthLine.="<noindex><a class='UserAuthAdNew InBg' href='/users/auth' rel='nofollow'>Войти</a>";
		$UserAuthLine.="<a class='UserAuthAdNew InBg' href='/users/reg' rel='nofollow'>Регистрация</a></noindex>";
		$UserAuthLine.="<a class='UserAuthAdNew InBg' href='/users/add' style='color:red;' rel='nofollow'>Добавить пост</a></noindex>";
		$UserAuthLine.="<a class='UserAuthAdNew InBg' href='/rules' rel='nofollow'>Как заработать?</a></noindex>";
		
	} else {
		### СЕССИЯ ЗАПОЛНЕНА -- --- --- --- ------ --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
		$GLOBAL["log"].="<b>Есть авторизация ID=".(int)$_SESSION['userid']."</b><hr>";
		$usdata=DB("SELECT * FROM `_users` WHERE (`id`='".(int)$_SESSION['userid']."') LIMIT 1"); @mysql_data_seek($usdata["result"],0); $USER=@mysql_fetch_array($usdata["result"]);
		if ($usdata["total"]==0 || (int)$USER["stat"]==0 || (int)$USER["id"]==0) {
			### АВТОРИЗОВАН, НО ЧТОТО НЕ ТАК - --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
			$_SESSION["userid"]=0; $_SESSION["userrole"]=0; unset($_SESSION); setcookie('usercookuid','',time()+1); setcookie('usercookmd5','',time()+1); @header("Location: /users/loginerror"); exit();
		} else {
			### АВТОРИЗОВАН, ВСЕ ОК -- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
			$GLOBAL["log"].="<b>SESSION авторизация</b><hr>"; $_SESSION['userid']=$USER["id"]; $_SESSION['userrole']=$USER["role"]; $GLOBAL["USER"]=$USER;
			$md5=md5($USER["login"]."-".$USER["pass"]."-".$USER["created"]."-".$RealHost); setcookie('usercookuid', $USER["id"], time()+60*60*24*30,"/"); setcookie('usercookmd5', $md5, time()+60*60*24*30,"/");
			
			$moth=time()-31*24*60*60; $data=DB("SELECT `id` FROM `_usersmess` WHERE (`withuid`='".(int)$USER["id"]."' && `data`>'".$moth."' && `readed`=0 && `fromorto`=0)");
			if ($data["total"]==0) { $new=0;} else { $new="<b style='color:red;'>".$data["total"]."</b>"; } 
			
			$UserAuthLine.="<a class='UserAuthAdMe' href='/users/my'>Кабинет</a>";
			$UserAuthLine.="<a class='UserAuthAdMe' href='/users/add' style='color:red;'>Добавить пост</a>";
			$UserAuthLine.="<a class='UserAuthAdMe' href='/users/list'>Мои статьи</a>";			
			$UserAuthLine.="<a class='UserAuthAdMe' href='/users/msg'>Сообщения: ".$new."</a>";
			$UserAuthLine.="<a href='/users/exit' class='UserAuthEnter NtrBg'>Выход</a>";
		}
	}
	$Page["UserAuthLine"] = "<div class='UserAuthLine'>".$UserAuthLine."</div>";
	$Page["UserAuthBoxs"] = "<div class='UserAuthBoxs'>".$UserAuthBoxs."</div>";
// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- --
} else { $GLOBAL["log"].="<u>Пользователи</u>: функционал ОТКЛЮЧЕН<hr>"; }
?>