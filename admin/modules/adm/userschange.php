<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg; $table="_users";
	$data=DB("SELECT `nick`, `id`, `role` FROM `".$table."` WHERE (`id`='".(int)$id."') LIMIT 1"); @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
	if ($ar["role"]>$_SESSION['userrole']) {
		$AdminText.='<h2 style="float:left;">Смена авторизации</h2>'.$C5."<div id='Msg2' class='ErrorDiv'>Вы не можете авторизоваться как <a href='/users/view/$id'><b>$ar[nick]</b></a>, с уровнем выше чем у вас.</div>";
		$AdminText.="<div class='RoundText' id='Tgg'><a href='?cat=adm_superusers'>Вернуться к списку пользователей</a></div>";
	} else {
	$_SESSION['userid']=$ar["id"]; $_SESSION['userrole']=$ar["role"]; $_SESSION['userfrom']="";
		$AdminText.='<h2 style="float:left;">Смена авторизации</h2>'.$C5."<div id='Msg2' class='InfoDiv'>Теперь вы авторизованы как <a href='/users/view/$id'><b>$ar[nick]</b></a> (ID=$id)</div>";
		$AdminText.="<div class='RoundText' id='Tgg'><a href='?cat=adm_superusers'>Вернуться к списку пользователей</a></div>";
	}
	// ПРАВАЯ КОЛОНКА
	$AdminRight="";
}
//=============================================
$_SESSION["Msg"]="";
?>