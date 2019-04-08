<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);
	
	global $pg;
	$table=$alias."_items";
	$table2=$alias."_actions";
	
	// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		require 'actionsResizePhoto.php';
		$ar=explode(".", $P["todata"]); $sdata1=mktime(0, 0, 0, $ar[1], $ar[0], $ar[2]);
		$pics = $pic = "";
		$name = str_replace("'", "\'", $P['name']);
		$text = str_replace("'", "\'", $P['text']);
		$fas = str_replace("'", "\'", $P['fas']);
		$rest = str_replace("'", "\'", $P['rest']);
		if($P["pic"]){			
			$pic = $P["pic"];
			actionsResizePhoto($pic);
			rename($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/picoriginal/".$pic);
		}
		if($P["attachment"]){
			foreach ($P["attachment"] as $pici) {
				$pics .= $pics ? "|".$pici : $pici;
				actionsResizePhoto($pici);
				rename($ROOT."/userfiles/temp/".$pici, $ROOT."/userfiles/picoriginal/".$pici);
			}
		}
		$q="INSERT INTO `$table2` (`data`, `todata`, `pid`, `name`, `text`, `pic`, `pics`, `fas`, `rest`) VALUES ('".time()."', '$sdata1', '".$id."', '".$name."', '".$text."', '$pic', '$pics', '".$fas."', '".$rest."')";
		DB($q); $last=DBL(); DB("UPDATE `$table2` SET `rate`='".$last."' WHERE  (id='".$last."')");
		
		@header("location: ?cat=".$alias."_actions&id=".$id); exit();
	}
	
	// ЭЛЕМЕНТЫ
	$data=DB("SELECT name, orderby, onpage FROM `_pages` WHERE (`link`='".$alias."')"); @mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);
	$AdminText.='<h2 style="float:left;">'.$raz["name"].'</h2><div class="LinkG" style="float:right;"><a href="?cat='.$alias.'_actionadd&id='.$id.'">Добавить акцию</a></div>'.$C5.$_SESSION["Msg"];
	
	$data=DB("SELECT `name`, `stat` FROM `$table` WHERE (`id`=".(int)$id.") LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
	
	### Заполнение данных
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);
	if ($node["stat"]==1) { $chk="checked"; }
	
	$AdminText='<h2>Добавление акции: &laquo<span class="companyName">'.$node["name"].'</span>&raquo;</h2>'.$_SESSION["Msg"];
	
	$AdminText.='<link media="all" href="/modules/standart/multiupload/client/uploader2.css" type="text/css" rel="stylesheet"><script type="text/javascript" src="/modules/standart/multiupload/client/uploader.js"></script>';
	$AdminText.='<div class="RoundText"><form action="'.$_SERVER["REQUEST_URI"].'" enctype="multipart/form-data" method="post" onsubmit="return JsVerify();" class="actionForm"><table class="actionTable"><tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>';
	$AdminText.="<tr class='TRLine0'><td class='VarText'>Название</td><td class='LongInput'><input name='name' type='text' value=''></td></tr>";
	$AdminText.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top; padding-top:10px;">Основное фото акции</td><td class="LongInput"><div class="uploaderCon"><div class="uploader"></div><div class="Info">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles"></div></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Текст</td><td class="LongInput"><textarea name="text" style="outline:none; height:150px;"></textarea></td></tr>';
	$AdminText.="<tr class='TRLine1'><td class='VarText'>Дата окончания</td><td class='DateInput'><input id='datepick' name='todata' type='text' readonly></td></tr>";
	$AdminText.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Ограничения акции</td><td class="LongInput"><textarea name="rest" style="outline:none; height:50px;"></textarea></td></tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top; padding-top:10px;">Предупреждение ФАС</td><td class="LongInput"><textarea name="fas" style="outline:none; height:50px;"></textarea></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Фотографии акции</td><td class="LongInput"><div class="uploader"></div><div class="Info">Вы можете загрузить фотографии в форматах jpg, gif и png</div><div class="uploaderFiles"></div></td></tr>';				
	$AdminText.='</table>'.$C10.'<div class="CenterText"><input type="submit" name="savebutton" class="SaveButton" value="Сохранить"></div>';
	$AdminText.='</form></div>';	
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_contacts&id=".$id."'>Контакты и часы работы</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_pics&id=".$id."'>Фотографии компании</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_consults&id=".$id."'>Консультации</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_actions&id=".$id."'>Акции</a></div>
	$C5<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$table."' ".$chk."></td><td><b>Материал опубликован</b></td></tr></table></div>";
	}
	}
}

//=============================================
$_SESSION["Msg"]="";
?>