<?
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg; $table="post_users"; $AdminRight=""; $AdminText.='<h2>Просмотр статьи на модерации</h2>'; $P=$_POST; 
	$q="SELECT `".$table."`.*, `_users`.`nick` FROM `".$table."` LEFT JOIN `_users` ON `_users`.`id`=`".$table."`.`uid` WHERE (`".$table."`.`stat`=1 && `".$table."`.`id`=".$id.")".$orderby; $data=DB($q);
	if ($data["total"]==0) { $AdminText="Статья не найдена. <a href='?cat=moderate_new'>Вернуться к списку</a>"; } else { @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]);
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	if (isset($P["offbutton"])) {
		DB("INSERT INTO `_usersmess` (`withuid`,`fromorto`,`text`,`data`,`ip`) VALUES ('".(int)$ar["uid"]."','0','".$P["otkaz"]."','".time()."','".$GLOBAL["ip"]."')");	
		DB("UPDATE `$table` SET `stat`=0 WHERE (`".$table."`.`stat`=1 && `".$table."`.`id`=".$id.")");
		if ((int)$P["useroff"]==1) { DB("UPDATE `_users` SET `stat`=0 WHERE (`id`=".$ar["uid"].")"); } @header("location: ?cat=moderate_new"); exit();
	}
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---		
	if (isset($P["onbutton"])) {
		DB("INSERT INTO `post_lenta` (`oplata`,`uid`,`data`,`name`,`lid`,`text`,`pic`,`picxy`,`kw`,`ds`,`picauth`) VALUES ('0','".$ar["uid"]."','".time()."','".$ar["name"]."','".$ar["lid"]."','".$ar["text"]."','".$ar["pic"]."','".$ar["picxy"]."','".$ar["kw"]."','".$ar["ds"]."','".$ar["picauth"]."')"); $newid=DBL();
		
		if ($ar["video"]!="") {
			$vid=GetNormalVideo('<iframe width="853" height="480" src="http://'.str_replace(array('watch?v=','http://',"//"), array('embed/','','') ,$ar["video"]).'" frameborder="0" allowfullscreen></iframe>'); 
			DB("INSERT INTO `_widget_video` (`link`,`pid`,`text`) VALUES ('post','$newid','$vid')");
		}
		
		DB("UPDATE `_widget_pics` SET `link`='post', `pid`='$newid' WHERE (`link`='userslenta' && `pid`=".$id.")");
		DB("UPDATE `$table` SET `stat`='2', `lentaid`='".$newid."' WHERE (`stat`=1 && `id`=$id)");
		
		$text=$P["prinal"]; if ((int)$P["dengion"]!=0) { $text.="\r\r<b>На ваш счет зачислено ".(int)$P["dengion"]." рублей</b>"; DB("INSERT INTO `_userspays` (`data`,`money`,`text`,`uid`,`pid`) VALUES ('".time()."','".$P["dengion"]."','Оплата публикации «$ar[name]»','$ar[uid]','$newid')"); }
		DB("INSERT INTO `_usersmess` (`withuid`,`fromorto`,`text`,`data`,`ip`) VALUES ('".(int)$ar["uid"]."','0','".$text."','".time()."','".$GLOBAL["ip"]."')");	
		
		@header("location: ?cat=post_edit&id=".$newid); exit();
	}
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	
		$text.="<h1>".$ar["name"]."</h1><a href='/users/preview/".$id."' target='_blank' style='color:red;'><b>Предварительный просмотр</b></a> (откроется в новом окне)<br><br><hr>";
		$text.="<div class='C10'></div><div style='width:45%; float:left; overflow:hidden; border:1px solid #CCC; border-radius:10px; padding:10px;'>
		<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'><h2 style='color:green; font-size:17px;'>Принять статью</h2><div class='LongInput'>
		<textarea name='prinal' style='width:100%; height:150px;'>Ваша статья «".$ar["name"]."» успешно прошла модерацию и опубликована на сайте.\r\rЖдем ваших новых статей. С уважением, команда Bubr.ru</textarea>
		<div class='C10'></div><div class='sdiv'><select name='dengion'><option value='300' selected>300 рублей</option><option value='500'>500 рублей</option><option value='0'>0 рублей</option></select></div></div>
		<div class='C20'></div><input type='submit' name='onbutton' id='onbutton' class='SaveButton' value='Одобрить статью'>
		<div class='C10'></div><hr>После одобрения статьи, вы перейдете на её редактирование, где вы сможете её отредактировать и опубликовать.</form></div>
		
		<div style='width:45%; float:right; overflow:hidden; border:1px solid #CCC; border-radius:10px; padding:10px;'>
		<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'><h2 style='color:red; font-size:17px;'>Отклонить статью</h2><div class='LongInput'>
		<textarea name='otkaz' style='width:100%; height:150px;'>Мы ознакомились с предлагаемой статьей «".$ar["name"]."» и пришли к выводу, что не можем её опубликовать на сайте.\r\rПричина: несоответствие содержания формату сайта\r\rЖдем ваших новых статей. С уважением, команда Bubr.ru</textarea>
		<div class='C10'></div><div class='sdiv'><select name='useroff'><option value='0' selected>Ничего не делать с пользователем</option><option value='1'>Забанить пользователя за спам</option></select></div></div>
		<div class='C20'></div><input type='submit' name='offbutton' id='offbutton' class='SaveButton' value='Отклонить статью'>
		<div class='C10'></div><hr>После отклонения статьи, вы перейдете на список статей, которые все ещё ожидают модерацию.</form></div><div class='C'></div>";
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	} $AdminText.="<div class='RoundText' id='Tgg'>".$text."</div>";
}

//=============================================
$_SESSION["Msg"]="";
?>