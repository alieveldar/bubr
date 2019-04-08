<?
session_start(); $uid=(int)$_SESSION['userid']; $userdata=array(); $table="_users";

if ($uid!=0) {
	
	$GLOBAL["sitekey"]=1;
	@require_once($_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php");

	if (!empty($_POST['token'])) { $s=@file_get_contents('http://ulogin.ru/token.php?token='.$_POST['token'].'&host='.$_SERVER['HTTP_HOST']);
	$userdata=json_decode($s, true); $provider=$userdata["network"]; $useridp=$userdata["uid"]; 
	
	/* Текущая сеть != Добавляемой сети*/
	if (1==1) {
	switch ($provider) {
		case "vkontakte": AuthUserVK($userdata, $useridp); break;
		case "vk": AuthUserVK($userdata, $useridp); break;
		case "facebook": AuthUserFB($userdata, $useridp); break;
		case "mailru": AuthUserML($userdata, $useridp); break;
		case "twitter": AuthUserTW($userdata, $useridp); break;
		case "odnoklassniki": AuthUserOD($userdata, $useridp); break;
		case "google": AuthUserGL($userdata, $useridp); break;
		case "yandex": AuthUserYA($userdata, $useridp); break;
		default: @header("Location: /loginerror/"); exit(); }
	}
	}
}

#echo $provider;
#echo "<hr>";
#echo $GLOBAL["log"];

$bp=rawurldecode($_GET["back"]); @header("Location: ".$bp); exit(); 


// ДОБАВЛЕНИЕ НОВОГО ЛОГИНА СОЦИАЛКИ
function  AuthUserVK($userdata, $useridp) { global $uid, $table; DB("UPDATE `".$table."` SET `vkontakte`='$useridp' WHERE (id='$uid') limit 1"); ClearPrev($useridp, $uid, "vkontakte"); }
function  AuthUserFB($userdata, $useridp) { global $uid, $table; DB("UPDATE `".$table."` SET `facebook`='$useridp' WHERE (id='$uid') limit 1"); ClearPrev($useridp, $uid, "facebook"); }
function  AuthUserML($userdata, $useridp) { global $uid, $table; DB("UPDATE `".$table."` SET `mailru`='$useridp' WHERE (id='$uid') limit 1"); ClearPrev($useridp, $uid, "mailru"); }
function  AuthUserTW($userdata, $useridp) { global $uid, $table; DB("UPDATE `".$table."` SET `twitter`='$useridp' WHERE (id='$uid') limit 1"); ClearPrev($useridp, $uid, "twitter"); }
function  AuthUserOD($userdata, $useridp) { global $uid, $table; DB("UPDATE `".$table."` SET `odnoklas`='$useridp' WHERE (id='$uid') limit 1"); ClearPrev($useridp, $uid, "odnoklas"); }
function  AuthUserGL($userdata, $useridp) { global $uid, $table; DB("UPDATE `".$table."` SET `google`='$useridp' WHERE (id='$uid') limit 1"); ClearPrev($useridp, $uid, "google"); }
function  AuthUserYA($userdata, $useridp) { global $uid, $table; DB("UPDATE `".$table."` SET `yandex`='$useridp' WHERE (id='$uid') limit 1"); ClearPrev($useridp, $uid, "yandex"); }

function ClearPrev($useridp, $uid, $where) {
	global $table; $d=DB("SELECT `id` FROM `".$table."` WHERE (`".$where."`='".$useridp."' && `id`!='".$uid."')");
	if ($d["total"]!=0) { @mysql_data_seek($d["result"], 0); $ar=@mysql_fetch_array($d["result"]); $olduid=$ar["id"]; DB("DELETE FROM `".$table."` WHERE (`".$where."`='".$useridp."' && `id`!='".$uid."')"); 
	$r=mysql_query("SHOW TABLES"); if (mysql_num_rows($r)>0) { while($row = mysql_fetch_array($r, MYSQL_NUM)) { $tables = $row[0]; if ($tables!=$table) { DB("UPDATE `".$tables."` SET `uid`='".$uid."' WHERE (`uid`='".$olduid."')"); }}}}
}
?>