<?
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg; $table="_userspays"; $AdminRight=""; $AdminText.='<h2>Просмотр заявки на вывод</h2>'; $P=$_POST;
	 
	$q="SELECT `".$table."`.*, `_users`.`nick` FROM `".$table."` LEFT JOIN `_users` ON `_users`.`id`=`".$table."`.`uid` WHERE (`".$table."`.`id`=".$id.")"; $data=DB($q);
	if ($data["total"]==0) { $AdminText="Статья не найдена. <a href='?cat=moderate_pays'>Вернуться к списку</a>"; } else { @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]);
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---		
	if (isset($P["onbutton"])) {
		if ($P["dengion"]==2) { DB("DELETE FROM `$table` WHERE (`id`=".$id.")"); @header("location: ?cat=moderate_pays"); exit(); }
		DB("UPDATE `$table` SET `pid`='$P[dengion]', `text`='$P[prinal]', `money`='$P[summa]' WHERE (`id`=".$id.")");
		$_SESSION["Msg"]="<div class='C20'></div><div class='SuccessDiv'>Настройки сохранены</div>"; @header("location: ?cat=moderate_pay&id=".$id); exit();
	}
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	
		$text.="<h1>Сумма вывода: ".(0-$ar["money"])." руб.</h1><b>Автор: <a href='?cat=moderate_author&id=".$ar["uid"]."' target='_blank' style='color:red;'>$ar[nick]</b></a> (откроется в новом окне)<br><br><hr>";
		if ($ar["pid"]==0) { $s2=""; $s1="selected"; } else { $s1=""; $s2="selected"; }
		$text.=$_SESSION["Msg"]."<div class='C10'></div><form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'><h2 style='color:green; font-size:17px;'>Изменить статус заявки</h2><div class='LongInput'>
		Знак минус перед суммой обязателен! Если его не указать - такая сумма будет зачислена на счет пользователя.
		<input name='summa' style='width:100%;' placeholder='Сумма вывода' value='$ar[money]' /><div class='C10'></div>
		<textarea name='prinal' style='width:100%; height:150px;'>$ar[text]</textarea><div class='C10'></div>
		<div class='sdiv'><select name='dengion'><option value='0' $s1>Оставить заявку необработанной</option><option value='1' $s2>Вывод денег осуществлен</option><option value='2'>Удалить эту заявку</option>
		</select></div></div><div class='C20'></div><input type='submit' name='onbutton' id='onbutton' class='SaveButton' value='Сохранить изменения'><div class='C10'></div></form>";
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	} $AdminText.="<div class='RoundText' id='Tgg'>".$text.$C."</div>";
}

//=============================================
$_SESSION["Msg"]="";
?>