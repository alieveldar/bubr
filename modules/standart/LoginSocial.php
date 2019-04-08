<?
session_start(); $GLOBAL["sitekey"]=1;
$bp=rawurldecode($_GET["back"]); $ip=$_SERVER["REMOTE_ADDR"]; $userdata=array();
@require_once($_SERVER['DOCUMENT_ROOT']."/modules/standart/DataBase.php");

$shared="";

// ВОЗВРАЩАЕМ ДАННЫЕ С ЛОГИНЗЫ

if (!empty($_POST['token'])) {
	$s=file_get_contents('http://ulogin.ru/token.php?token='.$_POST['token'].'&host='.$_SERVER['HTTP_HOST']);
	$userdata=json_decode($s, true); $provider=$userdata["network"]; $userid=$userdata["uid"];

	switch ($provider) { 
		case "vkontakte": AuthUserVK($userdata, $userid); break;
		case "vk": AuthUserVK($userdata, $userid); break;
		case "facebook": AuthUserFB($userdata, $userid); break;
		case "mailru": AuthUserML($userdata, $userid); break;
		case "yandex": AuthUserYA($userdata, $userid); break;
		case "twitter": AuthUserTW($userdata, $userid); break;
		case "odnoklassniki": AuthUserOD($userdata, $userid); break;
		case "google": AuthUserGL($userdata, $userid); break;
		default: @header("Location: /users/loginerror/"); exit(); break;
	}
}
//var_dump($userdata); echo $GLOBAL["log"]; exit();
@header("Location: ".$bp); exit();

// АВТОРИЗАЦИЯ ИЛИ ДОБАВЛЕНИЕ НОВОГО
function  AuthUserVK($userdata, $userid) { global $ip, $DataBaseName; if (UserInDB("vkontakte='$userid'", "vk")===false) { $nid=GetNewId(); $name=$userdata["first_name"]." ".$userdata["last_name"]; 
$q=DB("INSERT INTO `_users` (`vkontakte`, `login`, `nick`) VALUES ('$userid', '".$name."-vk-".$userid."', '$name')"); $pic=CopyPic($userdata["photo"], $nid); SharedQuery($nid, $pic); NewAuthOk($nid, "vk"); } }


function  AuthUserFB($userdata, $userid) { global $ip, $DataBaseName; if (UserInDB("facebook='$userid'", "fb")===false) { $nid=GetNewId(); $name=$userdata["first_name"]." ".$userdata["last_name"];
$q=DB("INSERT INTO `_users` (`facebook`, `login`, `nick`) VALUES ('$userid', '".$name."-fb-".$userid."', '$name')"); $pic=CopyPic($userdata["photo"], $nid); SharedQuery($nid, $pic); NewAuthOk($nid, "fb"); }}


function  AuthUserTW($userdata, $userid) { global $ip, $DataBaseName; if (UserInDB("twitter='$userid'", "tw")===false) { $nid=GetNewId(); $name=$userdata["first_name"]." ".$userdata["last_name"];
$q=DB("INSERT INTO `_users` (`twitter`, `login`, `nick`) VALUES ('$userid', '".$name."-tw-".$userid."', '$name')"); $pic=CopyPic($userdata["photo"], $nid); SharedQuery($nid, $pic); NewAuthOk($nid, "tw"); }}


function  AuthUserGL($userdata, $userid) { global $ip, $DataBaseName; if (UserInDB("google='$userid'", "gl")===false) { $nid=GetNewId(); $name=$userdata["first_name"]." ".$userdata["last_name"]; 
$q=DB("INSERT INTO `_users` (`google`, `login`, `nick`) VALUES ('$userid', '".$name."-gl-".$userid."', '$name')"); $pic=CopyPic($userdata["photo"], $nid); SharedQuery($nid, $pic); NewAuthOk($nid, "gl"); }}


function  AuthUserML($userdata, $userid) { global $ip, $DataBaseName; if (UserInDB("mailru='$userid'", "ml")===false) { $nid=GetNewId();  $name=$userdata["first_name"]." ".$userdata["last_name"];
$q=DB("INSERT INTO `_users` (`mailru`, `login`, `nick`) VALUES ('$userid', '".$name."-ml-".$userid."', '$name')"); $pic=CopyPic($userdata["photo"], $nid); SharedQuery($nid, $pic); NewAuthOk($nid, "ml"); }}

function  AuthUserOD($userdata, $userid) { global $ip, $DataBaseName; if (UserInDB("odnoklas='$userid'", "od")===false) { $nid=GetNewId(); $name=$userdata["first_name"]." ".$userdata["last_name"];
$q=DB("INSERT INTO `_users` (`odnoklas`, `login`, `nick`) VALUES ('$userid', '".$name."-od-".$userid."', '$name')"); $pic=CopyPic($userdata["photo"], $nid); SharedQuery($nid, $pic); NewAuthOk($nid, "od"); }}

function  AuthUserYA($userdata, $userid) { global $ip, $DataBaseName; if (UserInDB("yandex='$userid'", "ya")===false) { $nid=GetNewId(); $name=$userdata["first_name"]." ".$userdata["last_name"];
$q=DB("INSERT INTO `_users` (`yandex`, `login`, `nick`) VALUES ('$userid', '".$name."-ya-".$userid."', '$name')"); $pic=CopyPic($userdata["photo"], $nid); SharedQuery($nid, $pic); NewAuthOk($nid, "ya"); }}




function NewAuthOk($login, $from) { global $bp, $_SESSION; $_SESSION['userid']=$login; $_SESSION['userrole']=0; $_SESSION['userfrom']=$from;  @header("Location: ".$bp); exit();  }

function UserInDB($query, $from) {
	$data=DB("SELECT `id`, `stat`, `role` FROM `_users` WHERE ($query)"); $total=@mysql_num_rows($sql);
	if ($data["total"]==0) { 
		return false;
	} else {
		@mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
		if ($ar['stat']==1) { $_SESSION['userid']=$ar["id"]; $_SESSION['userfrom']=$from; $_SESSION['userrole']=$ar["role"]; } else { @header("Location: /users/loginerror/"); exit(); }
		return true;
	}
}

function GetNewId() {
	global $DataBaseName;
	$q="SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='".$DataBaseName."' AND TABLE_NAME='_users'";
	$sql=@mysql_query($q); @mysql_data_seek($sql, 0); $ar=@mysql_fetch_array($sql); $nid=$ar[0]; 
	return $nid;
}

function SharedQuery($nid, $pic) { global $ip; DB("UPDATE `_users` SET `ip`='".$ip."', `stat`='1', `created`='".time()."', `lasttime`='".time()."', `avatar`='$pic' WHERE (`id`='$nid')"); }
function CopyPic($p, $nid) { if ($p!="") { $avatar=@file_get_contents($p); $pic="userfiles/avatar/".$nid.".jpg"; file_put_contents($_SERVER['DOCUMENT_ROOT']."/".$pic, $avatar); } else { $pic=""; } return $pic; }
?>