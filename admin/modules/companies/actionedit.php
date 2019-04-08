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
		
	
	// ЭЛЕМЕНТЫ
	$data=DB("SELECT name, orderby, onpage FROM `_pages` WHERE (`link`='".$alias."')"); @mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);	
	
	$data=DB("SELECT `$table2`.*, `$table`.id as `cid`, `$table`.stat as `cstat` FROM `$table2` JOIN `$table` ON `$table`.id=`$table2`.pid WHERE (`$table2`.`id`=".(int)$id.") LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
	
	### Заполнение данных
	@mysql_data_seek($data["result"], 0); $action=@mysql_fetch_array($data["result"]);
	if ($action["cstat"]==1) { $chk="checked"; }
	if($action["todata"]) $d=ToRusData($action["todata"]);
	
	
	// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		require 'actionsResizePhoto.php';
		$ar=explode(".", $P["todata"]); $sdata1=mktime(0, 0, 0, $ar[1], $ar[0], $ar[2]);
		$pics = $action['pics']; $pic = $action['pic'];
		
		if($pic != $P["pic"]){				
			if($pic) { foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { @unlink($ROOT."/userfiles/".$path."/".$pic); }}
			$pic = $P["pic"];				
			if($pic) { actionsResizePhoto($pic); rename($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/picoriginal/".$pic); }
		}
		
		if($pics){
			$pics = explode('|', $pics);
			foreach ($pics as $key => $pici) {
				if(!in_array($pici, $P["attachment"])) { foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { @unlink($ROOT."/userfiles/".$path."/".$pici); } unset($pics[$key]);}
			}
		}
					
		if($P["attachment"]){
			foreach ($P["attachment"] as $pici) {
				if(is_array($pics) && in_array($pici, $pics)) continue;
				actionsResizePhoto($pici);
				rename($ROOT."/userfiles/temp/".$pici, $ROOT."/userfiles/picoriginal/".$pici);
				$pics[] = $pici;
			}
		}
		$pics = implode('|', $pics);
		
		$q="UPDATE `$table2` SET 
		`todata`='$sdata1',
		`name`='".str_replace("'", "\'", $P['name'])."',
		`text`='".str_replace("'", "\'", $P['text'])."', 
		`pic`='$pic', 
		`pics`='$pics',
		`fas`='".str_replace("'", "\'", $P['fas'])."', 
		`rest`='".str_replace("'", "\'", $P['rest'])."'
		WHERE (`$table2`.`id`='".$action['id']."')";
					
		DB($q);
		
		@header("location: ?cat=".$alias."_actions&id=".$action['cid']); exit();
	}
	
	$AdminText='<h2>Редактировании акции: &laquo<span class="companyName">'.$action["name"].'</span>&raquo;</h2>'.$_SESSION["Msg"];
	
	$AdminText.='<link media="all" href="/modules/standart/multiupload/client/uploader2.css" type="text/css" rel="stylesheet"><script type="text/javascript" src="/modules/standart/multiupload/client/uploader.js"></script>';
	$AdminText.='<div class="RoundText"><form action="'.$_SERVER["REQUEST_URI"].'" enctype="multipart/form-data" method="post" onsubmit="return JsVerify();" class="actionForm"><table class="actionTable"><tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>';
	$AdminText.="<tr class='TRLine0'><td class='VarText'>Название</td><td class='LongInput'><input name='name' type='text' value='".$action['name']."'></td></tr>";
	$AdminText.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top; padding-top:10px;">Основное фото акции</td><td class="LongInput"><div class="uploaderCon" style="'.($action['pic'] ? 'display:none;' : '').'"><div class="uploader"></div><div class="Info">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles">';
	if($action['pic']) $AdminText.='<span class="imgCon"><img src="/userfiles/picpreview/'.$action['pic'].'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="pic" value="'.$action['pic'].'" /></span>';
	$AdminText.='</div></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Текст</td><td class="LongInput"><textarea name="text" style="outline:none; height:150px;">'.$action['text'].'</textarea></td></tr>';
	$AdminText.="<tr class='TRLine1'><td class='VarText'>Дата окончания</td><td class='DateInput'><input id='datepick' name='todata' type='text' value='".$d[5]."' readonly></td></tr>";
	$AdminText.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Ограничения акции</td><td class="LongInput"><textarea name="rest" style="outline:none; height:50px;">'.$action['rest'].'</textarea></td></tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top; padding-top:10px;">Предупреждение ФАС</td><td class="LongInput"><textarea name="fas" style="outline:none; height:50px;">'.$action['fas'].'</textarea></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Фотографии акции</td><td class="LongInput"><div class="uploader"></div><div class="Info">Вы можете загрузить фотографии в форматах jpg, gif и png</div><div class="uploaderFiles">';
	if($action['pics']){
		$pics = explode('|', $action['pics']);
		foreach($pics as $pic){
			$AdminText.='<span class="imgCon"><img src="/userfiles/picpreview/'.$pic.'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="attachment[]" value="'.$pic.'" /></span>';
		}
	}
	$AdminText.='</div></td></tr>';				
	$AdminText.='</table>'.$C10.'<div class="CenterText"><input type="submit" name="savebutton" class="SaveButton" value="Сохранить"></div>';
	$AdminText.='</form></div>';
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$action['cid']."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$action['cid']."'>Основное содержание</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_contacts&id=".$action['cid']."'>Контакты и часы работы</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_pics&id=".$action['cid']."'>Фотографии компании</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_consults&id=".$action['cid']."'>Консультации</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_actions&id=".$action['cid']."'>Акции</a></div>
	$C5<div class='SecondMenu'><a href='/$alias/view/".$action['cid']."/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$action['cid']."-".$table."' ".$chk."></td><td><b>Материал опубликован</b></td></tr></table></div>";
	}
	}
}

//=============================================
$_SESSION["Msg"]="";
?>