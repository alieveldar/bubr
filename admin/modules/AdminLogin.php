<?
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	$DATA=$_POST; $Msg=''; $RealHost = str_replace(array('http://', 'www.'), '', $_SERVER['HTTP_HOST']);
	if ($_SESSION['admincount']<5) {
		

if ((int)$_COOKIE['usercookuid']!=0 && isset($_COOKIE['usercookmd5']) && (int)$_SESSION['userid']==0) {
	$ucuid=(int)$_COOKIE['usercookuid']; $ucmd5=preg_replace('/[^a-zA-Z0-9]+/i', '', $_COOKIE['usercookmd5']);
	$q="SELECT `login`,`pass`,`created`,`role` FROM `_users` WHERE (`id`='".$ucuid."') LIMIT 1"; $usdata=DB($q);
	@mysql_data_seek($usdata["result"],0); $arus=@mysql_fetch_array($usdata["result"]);
	$dbmd5=md5($arus["login"]."-".$arus["pass"]."-".$arus["created"]."-".$RealHost);
	if ($dbmd5==$ucmd5) {
		$_SESSION['userrole']=(int)$arus["role"]; $_SESSION['userid']=(int)$ucuid;
		setcookie('usercookuid', $ucuid, time()+60*60*24*30, "/"); setcookie('usercookmd5', $dbmd5, time()+60*60*24*30, "/");
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	} else { setcookie('usercookuid','',time()-30000, "/"); setcookie('usercookmd5','',time()-30000, "/"); }
} 
		 
		if (isset($DATA["loginbtn"]) && $DATA["login"]!='' && $DATA["password"]!='') {
			$l=cutdata($DATA["login"]); $p=md5(cutdata($DATA["password"])); $_SESSION['admincount']++; $_SESSION['adminblock']=time()+(5*60);
			$data=DB("SELECT `id`, `role`,`login`,`pass`,`created` FROM `_users` WHERE (`login`='$l' && `pass`='$p' && `role`>'1' && `stat`='1') LIMIT 1");		
			if ($data["total"]==1) { @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $_SESSION['admincount']=0; 
			DB("INSERT INTO `_lentalog` (`link`, `id`, `uid`, `data`, `ip`, `text`) VALUES ('[login]', '0', '".$ar['id']."', '".time()."', '".$_SERVER['REMOTE_ADDR']."', 'Вход в систему администрирования (login)')");
			$_SESSION['userrole']=(int)$ar["role"]; $_SESSION['userid']=(int)$ar["id"];
			$md5=md5($ar["login"]."-".$ar["pass"]."-".$ar["created"]."-".$RealHost);
			setcookie('usercookuid', $ar["id"], time()+60*60*24*30,"/"); setcookie('usercookmd5', $md5, time()+60*60*24*30,"/");
			@header("location: ".$_SERVER["REQUEST_URI"]); exit(); } else { $Msg="<div class='ErrorDiv'>".ATextReplace('LoginErrorEnter')."</div>"; }
		}
		$AdminLogin="<div class='SystemInfo'><h1>Авторизация</h1><div class='C5'></div>
		<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return LoginInput();'>".$Msg."
		<div class='Left190 AuthForm' style='margin-right:20px;'> Логин<br /><input type='text' name='login' id='login' autofocus></div>
		<div class='Left190 AuthForm'> Пароль<br /><input type='password' name='password' id='password'></div>
		<div class='C10'></div><div class='CenterText AuthSbm'><input type='submit' name='loginbtn' value='Войти'></div></form></div>";	
	} else {
		if ($_SESSION['adminblock']<time()) { $_SESSION['admincount']=0; $_SESSION['adminblock']=0; @header("location: /admin/"); exit(); }
		$AdminLogin="<div class='SystemAlert'>".ATextReplace('LoginErrorText', $_SESSION['adminblock']-time())."</div>";
	}
}
?>