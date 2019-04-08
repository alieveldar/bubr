<?
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) { global $pg;
	// СОХРАНЕНИЕ СООБЩЕНИЯ
	$P=$_POST; if (isset($P["savebutton"])) {
		//$res=DB("INSERT INTO `_00domains` (`name`,`prefix`) VALUES ('".DBcut($P["Int0"])."', '".DBcut($P["Inp0"])."')");
		DB("INSERT INTO `_usersmess` (`withuid`,`fromorto`,`text`,`data`,`ip`) VALUES ('".(int)$id."','0','".$P["msgtext"]."','".time()."','".$GLOBAL["ip"]."')");
		$_SESSION["Msg"]="<div class='C20'></div><div class='SuccessDiv'>Сообщение отправлено пользователю!</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}
	// ДОБАВИТЬ СООБЩЕНИЕ
	$AdminText.=$_SESSION["Msg"]."<div class='RoundText' id='Tgg'><form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>
	<span class='NormalInput'><textarea name='msgtext' style='float:left; width:520px;'></textarea></span>
	<input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Отправить сообщение' style='float:right; margin-top:44px;'>".$C."</form></div>";
	
	
	// ЭЛЕМЕНТЫ
	$data=DB("SELECT `_usersmess`.*, `_users`.`nick` FROM `_usersmess` LEFT JOIN `_users` ON `_users`.`id`=`_usersmess`.`withuid` WHERE (`_usersmess`.`withuid`='".(int)$id."') ORDER BY `_usersmess`.`data` DESC"); $text="";
	$AdminText.='<h2 style="float:left;">Диалог с пользователем</h2>';
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]);
		if ($ar["fromorto"]==1) {
			if ($ar["readed"]==0) { $new="<span style='font-size:10px; color:#F00;'>Новое!</span>  "; } else { $new=""; }  $style='style="background:#fff5e4;"';
			$user="<b>".$new." <a href='?cat=moderate_userspost=".$ar["withuid"]."' target='_blank'>".$ar["nick"]."</a> | ".$d[4]." | IP ".$ar["ip"]."</b>"; $nick=$ar["nick"];
		}
		if ($ar["fromorto"]==0) {
			if ($ar["readed"]==0) { $new="<span style='font-size:10px; color:#F00;'>не прочитано</span>  "; } else { $new="<span style='font-size:10px; color:#060;'>прочитано</span>  "; }
			$user="<b style='color:#666;'>".$new." Администрация | ".$d[4]."</b>"; $style='style="background:#f0ffe4;"';
		}
		$text.='<tr '.$style.'  id="Line'.$ar["id"].'">'."<td class='BigText' style='padding:4px;'>".$user."<div class='C10'></div><div style='font:12px/16px Tahoma; color:#000;'>".nl2br($ar["text"])."</div><div class='C5'></div></td>";
		$text.='<td class="Act" valign="top"><a href="javascript:void(0);" onclick="ActionAndUpdate('.$ar["id"].', \'DEL\', '.(int)$pg.');" title="Удалить">'.AIco('exit').'</a></td><td class="Act" valign="top"><input type="checkbox" id="'.$ar["id"].'" class="selectItem"></td>';
		$text.="</tr><tr><td colspan='3' style='padding:0;'><hr style='border-collapse:collapse; border:none; border-bottom:1px dashed #999;'></td>";
	endfor; 
	$AdminText.="<div class='RoundText' id='Tgg'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete()'>Удалить выбранные</a></div><table>".$text."</table></div>";
	// ПРАВАЯ КОЛОНКА
	$AdminRight="<div class='SecondMenu'><a href='?cat=moderate_author=".$ar["withuid"]."' target='_blank'>Статьи пользователя:<br><u><b>".$nick."</b></u></a></div>";
	DB("UPDATE `_usersmess` SET `readed`=1 WHERE (`fromorto`=1 && `withuid`='".$id."')");
}

//=============================================
$_SESSION["Msg"]="";
?>