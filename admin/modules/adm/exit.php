<?
### ВЫХОД ИЗ АДМИНКИ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	$_SESSION['userrole']=0; $_SESSION['userid']=0; $_SESSION['admincount']=0; $_SESSION['adminblock']=0; $_SESSION['adminlevel']=0;
	setcookie('usercookuid', "", time()-30000, "/"); setcookie('usercookmd5', "", time()-30000, "/"); unset($_SESSION); unset($_COOKIE); header("location: /admin/"); exit();
}
?>