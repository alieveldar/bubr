<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	$table=$alias."_albums"; $table2=$alias."_photos";

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ

	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT * FROM `$table` WHERE (`id`=".(int)$id.") LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
		
	### Заполнение данных
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);
	if ($node["stat"]==1) { $chk="checked"; }
		
	$AdminText='<h2>Фотографии альбома: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg"];

	### Основные данные	
	$AdminText.='<div class="RoundText"><div id="uploader" class="align-center"></div><div class="Info" align="center">Вы можете загружать файлы jpg, png, gif до 10М и размером не более 10.000px на 10.000px</div></div>';
	
	$data=DB("SELECT `".$table2."`.*, `_users`.`nick` FROM `".$table2."` LEFT JOIN `_users` ON `_users`.`id`=`".$table2."`.`uid` WHERE (`".$table2."`.`pid`='".(int)$id."') ORDER BY `".$table2."`.`rate` ASC");
	if ($data["total"]>0) {
		$AdminText.='<script type="text/javascript" src="http://maps.api.2gis.ru/1.0"></script><script type="text/javascript" src="/admin/texteditor/ckeditor.js"></script><script type="text/javascript" src="/admin/texteditor/adapters/jquery.js"></script>';
		$AdminText.='<div class="RoundText"><div class="LinkR MultiDel"><a href="javascript:void(0);" onclick="MultiDelete()">Удалить выбранные</a></div><div class="Photos">';
		for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $chk0 = $chk1 = $chk2 = ''; $ar=@mysql_fetch_array($data["result"]);
			if ($ar["stat"]==1) { $chk0="checked"; }
			if ($ar["main"]==1) { $chk1="checked"; }
			if ($ar["winner"]==1) { $chk2="checked"; }
			$img='<a href="javascript:void(0);" onclick=\'ViewBlank("", "<img src=/userfiles/picoriginal/'.$ar["pic"].' />");\' title="'.$ar['name'].'"><img src="/userfiles/picsquare/'.$ar['pic'].'" width="221" /></a>';
			
			$AdminText.='<ins class="RoundText Photo'.($ar["main"] ? ' Cover' : '').'" id="Line'.$ar["id"].'"><div class="Img">'.$img.'</div>';
			$AdminText.='<input type="checkbox" id="RS-'.$ar["id"].'-'.$table2.'" value="1" '.$chk0.' />
				<div  class="Act"><input type="checkbox" id="'.$ar["id"].'" class="selectItem"></div>
				<div id="DEL'.$ar["id"].'" class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$ar["pic"].'\')" title="Удалить">'.AIco('exit').'</a></div>
				<div id="EDIT'.$ar["id"].'" class="Act"><a href="javascript:void(0);" onclick="ItemEdit(\''.$ar["id"].'\')" title="Править">'.AIco('28').'</a></div>
				<div  class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$ar["id"].'\')" title="Опустить">'.AIco(4).'</a></div>
				<div  class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$ar["id"].'\')" title="Поднять">'.AIco(3).'</a></div>';
			$AdminText.=$C5.'Загрузил: <a href="/users/view/'.$ar["uid"].'">'.$ar["nick"].'</a>';
			if($ar["winner"]) $AdminText.='<div class="winner">Победитель</div>';
			$AdminText.='</ins>';
		endfor;
		$AdminText.='</div></div><input class="maps_default" type="hidden" value="'.$VARS['maps'].'">';
	}	

// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_photos&id=".$id."'>Фотографии</a></div>
	$C5<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_albums' ".$chk."></td><td><b>Материал опубликован</b></td></tr></table></div>";
	}
	}
}
$_SESSION["Msg"]="";
?>